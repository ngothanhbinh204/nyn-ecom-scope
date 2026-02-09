(function ($) {
	"use strict";

	const CCWooCommerce = {
		// Core initialization
		woocommerce_params: window.cc_woocommerce_params || {},
		init: function () {
			this.cart.init();
			this.checkout.init();
			this.productDetail.init();
			this.search();
			this.ordersList.init();
			this.quantityHandler.init();
		},

		// Product Detail
		productDetail: {
			init: function () {
				this.watchProductVariation();
			},
			watchProductVariation: function () {
				$(document).on("change paste keyup", "input.variation_id", function () {
					const variationId = $(this).val();
					if ($(this).val() != "") {
						$(".price-range-fake").hide();
						$(".product-price-wrap").removeClass("loading");
						$(".content.format-content").hide();
						$(".content.format-content[data-variation-id='" + variationId + "']").show();
						const data_product_variation = $('form.variations_form').attr('data-product_variations');
						if (data_product_variation) {
							const variations = JSON.parse(data_product_variation);
							const currentVariation = variations.find(variation => variation.variation_id == variationId); // Use == for loose comparison as variationId might be a string
							if (currentVariation) {
								const sku = currentVariation.sku;
								const priceHtml = currentVariation.price_html;
								$('.product-sku').text(sku);
								if (priceHtml) {
									$('.product-price-wrap .product-price .price').html(priceHtml);
								}
							} else {
								// console.log("Variation not found for ID:", variationId);
							}
						}
					}
					// slide to image
					const $slides = $('.product-main-image .swiper-slide');
					$slides.each(function (index) {
						if ($(this).data('variation-id') == variationId) {
							window.productDetail.slideTo(index);
							return false;
						}
					});
				});
				setTimeout(() => {
					$(".product-price-wrap").removeClass("loading");
				}, 1000);

			},

		},

		// Unified Quantity Handler - Replaces both productDetail.productQuantity and cart.updateQuantityCart
		quantityHandler: {
			init: function () {
				this.setupDecimalsPrototype();
				this.bindQuantityEvents();
			},

			setupDecimalsPrototype: function () {
				if (!String.prototype.getDecimals) {
					String.prototype.getDecimals = function () {
						var num = this,
							match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
						if (!match) {
							return 0;
						}
						return Math.max(0, (match[1] ? match[1].length : 0) - (match[2] ? +match[2] : 0));
					}
				}
			},

			bindQuantityEvents: function () {
				// Handle plus/minus button clicks for both product detail and cart contexts
				$(document).on('click', '.quantity .plus, .quantity .minus, .product-quantity .plus, .product-quantity .minus', this.handleButtonClick.bind(this));

				// Handle direct input changes for cart items
				$(document).on("input", ".product-quantity .qty", this.handleInputChange.bind(this));
			},

			handleButtonClick: function (e) {
				var $button = $(e.currentTarget);
				var $qty = $button.closest('.quantity, .product-quantity').find('.qty');
				var currentVal = parseFloat($qty.val());
				var max = parseFloat($qty.attr('max'));
				var min = parseFloat($qty.attr('min'));
				var step = $qty.attr('step');
				var isCartContext = $button.closest('.product-quantity').length > 0;

				// Format values with consistent defaults
				if (!currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
				if (max === '' || max === 'NaN') max = '';
				if (min === '' || min === 'NaN') min = isCartContext ? 1 : 0; // Cart items minimum is 1, product detail minimum is 0
				if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = 1;

				// Calculate new value
				var newVal = currentVal;
				if ($button.is('.plus')) {
					if (max && (currentVal >= max)) {
						newVal = max;
						alert(cc_woocommerce_params.woo_quantity_max + max);
					} else {
						newVal = (currentVal + parseFloat(step)).toFixed(step.getDecimals());
					}
				} else {
					if (min && (currentVal <= min)) {
						newVal = min;
						alert(cc_woocommerce_params.woo_quantity_min + min);
					} else if (currentVal > 0) {
						newVal = (currentVal - parseFloat(step)).toFixed(step.getDecimals());
					}
				}

				$qty.val(newVal);

				// Handle context-specific actions
				if (isCartContext) {
					this.handleCartUpdate($qty, newVal);
				} else {
					// Trigger change event for product detail context
					$qty.trigger('change');
				}
			},

			handleInputChange: function (e) {
				var $qty = $(e.currentTarget);
				var quantity = $qty.val();
				if (!quantity) return;

				var currentVal = parseFloat(quantity);
				var max = parseFloat($qty.attr("max"));
				var min = parseFloat($qty.attr("min"));
				var step = $qty.attr("step");

				// Format values
				if (!currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
				if (max === '' || max === 'NaN') max = '';
				if (min === '' || min === 'NaN') min = 1; // Cart context minimum is 1
				if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = 1;

				// Validate quantity bounds
				if (max && (currentVal > max)) {
					currentVal = max;
					alert(cc_woocommerce_params.woo_quantity_max + max);
					$qty.val(max);
					return;
				}
				if (min && (currentVal < min)) {
					currentVal = min;
					alert(cc_woocommerce_params.woo_quantity_min + min);
					$qty.val(min);
					return;
				}

				this.handleCartUpdate($qty, quantity);
			},

			handleCartUpdate: function ($qty, quantity) {
				// Extract cart item key from the input name attribute
				var nameAttr = $qty.attr("name");
				if (!nameAttr || !nameAttr.match(/\[(.*?)\]/)) {
					console.warn("Cart item key not found in quantity input name attribute");
					return;
				}
				var cart_item_key = nameAttr.match(/\[(.*?)\]/)[1];

				// Handle WooCommerce cart form updates
				if ($qty.closest(".woocommerce-cart-form").length) {
					$("[name='update_cart']").removeAttr("disabled");
					$("[name='update_cart']").trigger("click");
					$(document.body).trigger('wc_fragment_refresh');
					$(document.body).trigger('wc_fragments_refreshed');
				}

				// AJAX update for mini-cart
				$.ajax({
					type: "POST",
					url: woocommerce_params.ajax_url,
					data: {
						action: "update_cart_item",
						cart_item_key: cart_item_key,
						quantity: quantity,
					},
					beforeSend: function () {
						$(".mini-cart-wrapper").addClass("loading");
					},
					success: function (response) {
						// Update the mini cart fragments
						if (response.fragments) {
							const miniCart = response.fragments["div.widget_shopping_cart_content"];
							const countCart = response.fragments[".count-cart"];
							if (miniCart) $(".widget_shopping_cart_content").replaceWith(miniCart);
							if (countCart) $(".count-cart").replaceWith(countCart);
						}
						$(document.body).trigger("wc_fragment_refresh");
						$(".mini-cart-wrapper").removeClass("loading");
					},
					error: function (xhr, status, error) {
						console.error("Cart update failed:", status, error);
						$(".mini-cart-wrapper").removeClass("loading");
					}
				});
			}
		},
		search: function () {
			var typingTimer;
			var doneTypingInterval = 150; // Time in ms
			var $input = $('input[name="s"]');
			var $suggestBox = $(".header-search-suggest");
			var $headerSearch = $(".wrap-form-search-result");

			$input.on("keyup", function () {
				clearTimeout(typingTimer);
				if ($input.val().length === 0) {
					$suggestBox.empty(); // Clear suggestions if input is empty
				} else {
					typingTimer = setTimeout(doneTyping, doneTypingInterval);
				}
			});

			$input.on("keydown", function () {
				clearTimeout(typingTimer);
			});
			$(document).on("click", function (event) {
				if (!$(event.target).closest(".header-search").length) {
					$suggestBox.empty(); // Clear suggestions if clicking outside the search box
				}
			});

			function doneTyping () {
				var keyword = $input.val();
				if (keyword.length >= 3) {
					$headerSearch.addClass("loading");

					var productCategoryPromise = $.ajax({
						url: woocommerce_params.ajax_url,
						method: "POST",
						data: {
							action: "product_category_search",
							keyword: keyword,
						},
					});

					var productPromise = $.ajax({
						url: woocommerce_params.ajax_url,
						method: "POST",
						data: {
							action: "product_search",
							keyword: keyword,
						},
					});
					Promise.all([productCategoryPromise, productPromise]).then(function (responses) {
						$headerSearch.removeClass("loading");
						$suggestBox.empty();

						var categoryResponse = responses[0];
						var productResponse = responses[1];

						if (categoryResponse.success) {
							$.each(categoryResponse.data, function (index, category) {
								var categoryItem = `
                            <div class="item-search-suggest category-suggest">
								<div class="category-title">
									<a href="${category.link}">${category.name}</a>
								</div>
                            </div>`;
								$suggestBox.append(categoryItem);
							});
						}

						if (productResponse.success) {
							if (productResponse.data.length > 0) {
								$.each(productResponse.data, function (index, product) {
									var suggestItem = `
                            <div class="item-search-suggest">
                                <div class="img">
                                    <a href="${product.link}">
                                        <img src="${product.image}" alt="${product.title}">
                                    </a>
                                </div>
                                <div class="content">
                                    <div class="product-title">
                                        <h3>
                                            <a href="${product.link}">${product.title}</a>
                                        </h3>
                                    </div>
                                    <div class="product-price">
                                        <span>${product.price}</span>
                                    </div>
                                </div>
                            </div>`;
									$suggestBox.append(suggestItem);
								});
							} else {
								$suggestBox.append(`<p class="no-result p-5">${cc_woocommerce_params.no_result}</p>`);
							}
						}
					});
				}
			}
		},
		// Cart Module
		cart: {
			init: function () {
				this.minicart();
				// Quantity handling is now unified in the quantityHandler
				this.quickAddToCart();
			},

			quickAddToCart: function () {
				$(document).on("click", ".quick-add-to-cart", function (e) {
					e.preventDefault();
					const $button = $(this);
					if ($button.hasClass('disable')) return;

					const productId = $button.data("product-id");
					const quantity = $button.data("quantity") || 1;

					if (!productId) {
						console.error("Quick add to cart button missing product ID.");
						return;
					}

					const data = {
						action: 'woocommerce_ajax_add_to_cart', // Action for the backend PHP function
						product_id: productId,
						quantity: quantity,
						variation_id: $button.data("variation-id") || 0, // Handle if it's a variation quick add
					};

					// Minimal notification data (optional, adjust as needed)
					const dataNoti = {
						title: $button.closest('.product-item').find('.product-title a').text() || 'Product',
					};

					$(document.body).trigger('adding_to_cart', [$button, data]);

					$.ajax({
						type: 'post',
						url: CCWooCommerce.woocommerce_params.ajax_url, // Use the localized ajax URL
						data: data,
						beforeSend: function (response) {
							// Show loading indicators
							$('.loading-bar').css({ 'width': '40%', 'opacity': '1' });
							$button.addClass('disable loading');
						},
						complete: function (response) {
							// Hide loading indicators
							$button.removeClass('disable loading');
							// Ensure other buttons aren't stuck loading if the request originated elsewhere
							// $('.add_to_cart_button').removeClass('disable loading'); // Uncomment if needed
						},
						success: function (response) {
							if (response.error && response.product_url) {
								// Handle error (e.g., show a message)
								console.error("Add to cart error:", response.message);
								alert(response.message || 'Error adding product to cart.'); // Simple alert, replace with better UI if desired
								return;
							} else {
								// Trigger WooCommerce added_to_cart event

								// Update mini cart fragments
								if (response.fragments) {
									const miniCart = response.fragments["div.widget_shopping_cart_content"];
									const countCart = response.fragments[".count-cart"];
									if (miniCart) $(".widget_shopping_cart_content").replaceWith(miniCart);
									if (countCart) $(".count-cart").replaceWith(countCart);
								}
								$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button, dataNoti]);
								// Show the mini cart
								$('body').addClass('show-cart');
								$('.cart-overlay').addClass('active');
								$button.addClass('added disable');
							}
						},
						error: function (xhr, status, error) {
							// Handle AJAX errors
							console.error("AJAX Add to cart failed:", status, error);
							alert('An error occurred while adding the item to the cart. Please try again.'); // Simple alert
							$button.removeClass('disable loading'); // Ensure button is re-enabled on error
						}
					});

				});
			},


			minicart: function () {
				function funcRemove () {
					$("body").removeClass("show-cart");
					$(".cart-overlay").removeClass("active");
				}
				$('.cart-overlay').on('click', function () {
					funcRemove();
				});
				$(".header-cart:not(.disabled)").on("click", function () {
					$("body").toggleClass("show-cart");
					$(".cart-overlay").toggleClass("active");
				});
				$(".overlay, .mini-cart-wrapper .close").on("click", function () {
					if ($("body").hasClass("show-cart")) {
						funcRemove();
					}
				});
				$(document).keyup(function (e) {
					if (e.key === "Escape") {
						funcRemove();
					}
				});
			},


		},

		// Checkout Module
		checkout: {
			init: function () {
				this.initCouponForm();
				// console.log("Checkout initialized");
			},

			initCouponForm: function () {
				// Handle toggle coupon form visibility
				$(document).on('click', '.toggle-coupon-form', this.toggleCouponForm.bind(this));

				// Handle AJAX coupon button click (not form submission)
				$(document).on('click', '.ajax-apply-coupon', this.handleCouponButtonClick.bind(this));

				// Handle Enter key press in coupon input field (multiple selectors for safety)
				$(document).on('keypress', '#coupon_code, input[name="coupon_code"], .coupon-form-container input[type="text"]', this.handleCouponInputKeypress.bind(this));

				// Handle coupon removal
				$(document).on('click', '.woocommerce-remove-coupon', this.handleCouponRemoval.bind(this));

				// Prevent coupon form from submitting normally (safety measure)
				$(document).on('submit', '.ajax-coupon-form', this.preventCouponFormSubmission.bind(this));
			},

			preventCouponFormSubmission: function (e) {
				// Always prevent normal form submission for coupon form
				e.preventDefault();
				e.stopPropagation();

				// Trigger button click instead
				const $form = $(e.currentTarget);
				const $button = $form.find('.ajax-apply-coupon');
				if ($button.length) {
					$button.trigger('click');
				}
			},

			toggleCouponForm: function (e) {
				e.preventDefault();
				const $button = $(e.currentTarget);
				const $form = $('.ajax-coupon-form');

				if ($form.is(':visible')) {
					$form.slideUp(300);
					$button.find('span').text('Nhập mã giảm giá');
				} else {
					$form.slideDown(300);
					$button.find('span').text('Ẩn mã giảm giá');
					// Focus on the coupon input
					setTimeout(function () {
						$form.find('#coupon_code').focus();
					}, 350);
				}
			},

			handleCouponButtonClick: function (e) {
				e.preventDefault();
				e.stopPropagation(); // Prevent event bubbling to avoid checkout form interference

				const $button = $(e.currentTarget);

				// Use a more reliable approach to find elements
				const $input = this.findCouponInput($button);
				const $messages = this.findCouponMessages($button);
				const $form = $button.closest('.ajax-coupon-form');

				if (!$input || !$input.length) {
					console.error('Coupon input field not found');
					alert('Lỗi: Không tìm thấy trường nhập mã giảm giá.');
					return;
				}

				this.processCouponApplication($form, $button, $input, $messages);
			},

			findCouponInput: function ($button) {
				let $input;
				const $form = $button.closest('.ajax-coupon-form');
				if ($form.length) {
					$input = $form.find('#coupon_code');
					if ($input.length) return $input;

					$input = $form.find('input[name="coupon_code"]');
					if ($input.length) return $input;

					$input = $form.find('.input-text');
					if ($input.length) return $input;
				}
				return null;
			},

			findCouponMessages: function ($button) {
				let $messages;
				const $form = $button.closest('.ajax-coupon-form');
				if ($form.length) {
					$messages = $form.find('.coupon-messages');
					if ($messages.length) return $messages;
				}
				
				return $('<div>'); // Return empty div as fallback
			},

			handleCouponInputKeypress: function (e) {
				// Handle Enter key press in coupon input
				if (e.which === 13 || e.keyCode === 13) {
					e.preventDefault();
					e.stopPropagation(); // Prevent event bubbling to avoid checkout form interference

					const $input = $(e.currentTarget);
					const $button = this.findCouponButton($input);
					const $messages = this.findCouponMessages($input);
					const $form = $input.closest('.ajax-coupon-form');

					if (!$button || !$button.length) {
						console.error('Coupon button not found');
						return;
					}

					this.processCouponApplication($form, $button, $input, $messages);
				}
			},

			findCouponButton: function ($input) {
				let $button;
				$button = $input.closest('.wrap-form-coupon').find('.ajax-apply-coupon');
				if ($button.length) return $button;
				return null;
			},

			processCouponApplication: function ($form, $button, $input, $messages) {
				// Add safety checks
				if (!$input || !$input.length) {
					console.error('Invalid input element passed to processCouponApplication');
					return;
				}

				const inputValue = $input.val();
				if (typeof inputValue === 'undefined' || inputValue === null) {
					console.error('Cannot read input value');
					return;
				}

				const couponCode = inputValue.trim();

				// Clear previous messages
				if ($messages && $messages.length) {
					$messages.empty();
				}

				if (!couponCode) {
					this.showCouponMessage($messages, 'error', 'Vui lòng nhập mã giảm giá.');
					return;
				}

				// Show loading state
				if ($button && $button.length) {
					$button.prop('disabled', true).addClass('loading');
					$button.text('Đang xử lý...');
				}

				// AJAX request to apply coupon
				$.ajax({
					type: 'POST',
					url: CCWooCommerce.woocommerce_params.ajax_url,
					data: {
						action: 'apply_coupon_ajax',
						coupon_code: couponCode,
						security: CCWooCommerce.woocommerce_params.apply_coupon_nonce || ''
					},
					success: this.handleCouponSuccess.bind(this, $form, $button, $input, $messages),
					error: this.handleCouponError.bind(this, $form, $button, $messages)
				});
			},

			handleCouponSuccess: function ($form, $button, $input, $messages, response) {
				// Reset button state
				$button.prop('disabled', false).removeClass('loading');
				$button.text('Áp dụng');

				if (response.success) {
					// Show success message
					this.showCouponMessage($messages, 'success', response.data.message || 'Mã giảm giá đã được áp dụng thành công!');

					// Clear the input
					$input.val('');

					// Update checkout totals
					this.updateCheckoutTotals();

					// Hide the coupon form after successful application
					setTimeout(function () {
						$form.slideUp(300);
						$('.toggle-coupon-form span').text('Nhập mã giảm giá');
					}, 2000);

				} else {
					// Show error message
					this.showCouponMessage($messages, 'error', response.data.message || 'Có lỗi xảy ra khi áp dụng mã giảm giá.');
				}
			},

			handleCouponError: function ($form, $button, $messages, xhr, status, error) {
				// Reset button state
				$button.prop('disabled', false).removeClass('loading');
				$button.text('Áp dụng');

				// Show error message
				this.showCouponMessage($messages, 'error', 'Có lỗi xảy ra. Vui lòng thử lại.');
				console.error('Coupon AJAX error:', status, error);
			},

			handleCouponRemoval: function (e) {
				e.preventDefault();

				const $link = $(e.currentTarget);
				const couponCode = $link.data('coupon') || $link.attr('data-coupon');

				if (!couponCode) return;

				// AJAX request to remove coupon
				$.ajax({
					type: 'POST',
					url: CCWooCommerce.woocommerce_params.ajax_url,
					data: {
						action: 'remove_coupon_ajax',
						coupon_code: couponCode,
						security: CCWooCommerce.woocommerce_params.remove_coupon_nonce || ''
					},
					success: function (response) {
						if (response.success) {
							// Update checkout totals
							CCWooCommerce.checkout.updateCheckoutTotals();
						}
					},
					error: function (xhr, status, error) {
						console.error('Remove coupon AJAX error:', status, error);
					}
				});
			},

			showCouponMessage: function ($container, type, message) {
				if (!$container || !$container.length) {
					console.warn('Message container not found, showing alert instead:', message);
					alert(message);
					return;
				}

				const messageClass = type === 'success' ? 'woocommerce-message' : 'woocommerce-error';
				const messageHtml = `<div class="${messageClass}" role="alert">${message}</div>`;
				$container.html(messageHtml);
			},

			updateCheckoutTotals: function () {
				// Trigger WooCommerce checkout update
				$('body').trigger('update_checkout');

				// Also update fragments for mini cart if needed
				$(document.body).trigger('wc_fragment_refresh');
			},

			processCheckout: function () {
				// console.log("Processing checkout");
			},
			validateFields: function () {
				// console.log("Validating checkout fields");
			},
		},

		// Ajax Handler
		ajax: {
			request: function () {
				console.log("Making AJAX request");
			},

			handleSuccess: function () {
				console.log("AJAX request successful");
			},

			handleError: function () {
				console.log("AJAX request failed");
			},
		},

		// Utility Functions
		utils: {
			formatPrice: function () {
				console.log("Formatting price");
			},

			validateInput: function () {
				console.log("Validating input");
			},

			showNotification: function () {
				console.log("Showing notification");
			},
		},

		ordersList: {
			init: function () {
				// console.log("Orders list initialized");
				this.filterOrders();
				this.deleteOrder();
			},
			deleteOrder: function () {
				$(document).on("click", ".woocommerce-button.button.cancel", function (e) {
					const linkHref = e.target.href;
					if (!linkHref) return;
					const linkUrl = new URL(linkHref);
					const urlParams = linkUrl.searchParams;

					if (urlParams.has("cancel_order")) {
						if (window.confirm(cc_woocommerce_params.cancel_order_confirm)) {
							return true;
						} else {
							return false;
						}
					} else {
						return false;
					}
				});
			},
			filterOrders: function () {
				// console.log("Searching orders");
			},
		},
	};

	// Initialize on document ready
	$(document).ready(function () {
		CCWooCommerce.init();
	});
})(jQuery);
