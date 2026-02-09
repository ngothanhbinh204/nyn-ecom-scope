jQuery(document).ready(function ($) {
	const lang = {
		compare_empty: 'Thêm sản phẩm',
		compare_empty_description: 'Không có sản phẩm nào được so sánh',
		compare_empty_button: 'Thêm sản phẩm',
		compare_product_already_exists: 'Sản phẩm đã tồn tại trong danh sách so sánh',
		compare_limit_reached: 'Bạn chỉ có thể so sánh tối đa 5 sản phẩm',
		error_loading_product: 'Lỗi khi tải sản phẩm',
		added: 'Đã thêm',
		add_to_compare: 'Thêm vào so sánh',
		loading_product: 'Đang tải sản phẩm...',
		loading_search: 'Đang tìm kiếm...',
		loading_add: 'Đang thêm...',
		loading_remove: 'Đang xóa...',
		loading_clear: 'Đang xóa tất cả...',
		loading_close: 'Đang đóng...',
		loading_open: 'Đang mở...',
	}
	if ($('html').attr('lang') === 'en-US') {
		lang.compare_empty = 'Add product';
		lang.compare_empty_description = 'No products to compare';
		lang.compare_empty_button = 'Add product';
		lang.compare_product_already_exists = 'Product already exists in the comparison list';
		lang.compare_limit_reached = 'You can only compare up to 5 products';
		lang.added = 'Added';
		lang.add_to_compare = 'Add to compare';
		lang.error_loading_product = 'Error loading products';
		lang.loading_product = 'Loading products...';
		lang.loading_search = 'Searching...';
		lang.loading_add = 'Adding...';
		lang.loading_remove = 'Removing...';
		lang.loading_clear = 'Clearing all...';
		lang.loading_close = 'Closing...';
		lang.loading_open = 'Opening...';
	}
	const compareProduct = {
		init: function () {
			try {
				const storedProducts = localStorage.getItem("compareProducts");
				this.compareProducts = storedProducts ? JSON.parse(storedProducts) : [];
				if (!Array.isArray(this.compareProducts)) {
					this.compareProducts = [];
				}
			} catch (error) {
				console.error("Error parsing compare products:", error);
				this.compareProducts = [];
			}
			this.popupLoading = false;
			this.searchTimeout = null;
			// this.updateCompareSticky();
			this.setupEventListeners();
		},

		setupEventListeners: function () {
			// Add to compare
			$(".btn-product-compare-handle").on("click", this.handleAddToCompare.bind(this));

			// Remove from compare
			$(document).on("click", ".remove-compare", this.handleRemoveFromCompare.bind(this));

			// Toggle comparison bar
			$(".btn-compare-trigger").on("click", this.toggleComparisonBar);

			// Clear all products
			$(".btn-delete-all-compare-product").on("click", this.clearAllProducts.bind(this));

			// Compare now button action
			$(".btn-compare-now").on("click", this.compareNow);

			// Close comparison bar
			$(".btn-close-bar").on("click", this.closeComparisonBar);

			// Add product popup trigger
			$(document).on("click", ".btn-add-compare-popup", this.openAddComparePopup.bind(this));

			// Search input handler
			$(document).on("input", "#compare-popup-search", this.handlePopupSearch.bind(this));

			// Add to compare from popup
			$(document).on("click", "#dialog-content .btn-product-compare-handle", this.handleAddToCompareFromPopup.bind(this));
		},

		handleAddToCompare: function (e) {
			e.preventDefault();
			const $button = $(e.currentTarget);
			const productId = $button.data("id-product");

			if (this.compareProducts.some((product) => product.id === productId)) {
				alert(lang.compare_product_already_exists);
				return;
			}

			if (this.compareProducts.length >= 5) {
				alert(lang.compare_limit_reached);
				return;
			}

			$button.addClass("btn-loading");

			$.post(compareData.ajaxurl, {
				action: "add_to_compare",
				product_id: productId,
			}).done((response) => {
				this.compareProducts = response.data.products;
				localStorage.setItem("compareProducts", JSON.stringify(this.compareProducts));
				this.updateCompareSticky();
				this.updatePopupAddButtonState(productId, true);
				$button.removeClass("btn-loading");
				$('.sticky-comparison').addClass('active')
				if (window.matchMedia("(max-width: 1023.98px)").matches) {
					$('body').addClass('disable-scroll')
				}
			});
		},

		handleRemoveFromCompare: function (e) {
			e.preventDefault();
			const productId = $(e.target).data("id-product");
			const isSlide = $(e.target).closest('.swiper-slide').length > 0 || $(e.target).closest('th.item-compare-product').length > 0;
			$('main').append('<div class="loading-fixed"><div class="loader"></div></div>')
			$.post(compareData.ajaxurl, {
				action: "remove_from_compare",
				product_id: productId,
			}).done((response) => {
				this.compareProducts = response.data.products;
				localStorage.setItem("compareProducts", JSON.stringify(this.compareProducts));
				this.updatePopupAddButtonState(productId, false);
				if (isSlide) {
					window.location.reload();
				} else {
					this.updateCompareSticky();
					$('.loading-fixed').remove()
				}
			});
		},

		updateCompareSticky: function () {
			const $stickyCompare = $(".sticky-compare-bar");
			const $compareList = $stickyCompare.find(".compare-products-list");
			const $comparisonProductSlots = $(".comparison-product");
			const $countCompare = $(".count-compare");

			$stickyCompare.toggleClass("hidden", this.compareProducts.length === 0);
			$('.sticky-comparison').toggleClass('visible', this.compareProducts.length > 0);
			$countCompare.text(this.compareProducts.length);

			$compareList.empty();
			this.compareProducts.forEach((product, index) => {
				if (index < $comparisonProductSlots.length && product.html) {
					$comparisonProductSlots.eq(index).html(product.html);
				}
			});

			// Update the comparison products section
			if ($comparisonProductSlots.length) {
				// Reset all slots to empty state
				$comparisonProductSlots.html('<div class="btn-add-compare-popup border border-dashed border-Neutral-200 rounded-1 flex-center flex-col p-5 cursor-pointer hover:border-Primary-Red transition-300 aspect-square bg-neutral-50 "><span class="text-sm text-Neutral-400">' + lang.compare_empty + '</span></div>');

				// Fill slots with product HTML
				this.compareProducts.forEach((product, index) => {
					if (index < $comparisonProductSlots.length && product.html) {
						$comparisonProductSlots.eq(index).html(product.html);
					}
				});
			}
			window.lozad.observe();
		},

		toggleComparisonBar: function (e) {
			e.preventDefault();
			$(".sticky-comparison").toggleClass("active");
			if (window.matchMedia("(max-width: 1023.98px)").matches) {
				$('body').toggleClass('disable-scroll')
			}
		},

		clearAllProducts: function () {
			$('main').append('<div class="loading-fixed"><div class="loader"></div></div>')
			// Clear localStorage
			this.compareProducts = [];
			localStorage.setItem("compareProducts", JSON.stringify(this.compareProducts));

			// Clear cookie via AJAX
			$.post(compareData.ajaxurl, {
				action: "clear_compare_products",
			}).done(() => {
				// Update compare sticky bar
				this.updateCompareSticky();
				$('.loading-fixed').remove()
			});
		},

		closeComparisonBar: function () {
			$(".sticky-comparison").removeClass("active");
			if (window.matchMedia("(max-width: 1023.98px)").matches) {
				$('body').removeClass('disable-scroll')
			}
		},

		openAddComparePopup: function (e) {
			if (this.popupLoading) return;

			const self = this;
			this.popupLoading = true;
			const $productList = $("#compare-popup-product-list");
			$productList.html('<div class="text-center col-span-full py-10">' + lang.loading_product + '</div>');
			$("#compare-popup-search").val(''); // Clear search field

			Fancybox.show([{ src: "#dialog-content", type: "inline" }], {
				on: {
					"destroy": (fancybox) => {
						self.popupLoading = false;
						console.log('Fancybox closed, popupLoading:', self.popupLoading);
					}
				}
			});

			// Fetch initial products
			$.post(compareData.ajaxurl, {
				action: "get_initial_compare_products",
			}).done((response) => {
				if (response.success) {
					$productList.html(response.data.html);
				} else {
					$productList.html('<div class="col-span-full text-center text-red-500">' + lang.error_loading_product + '</div>');
				}
			}).fail(() => {
				$productList.html('<div class="col-span-full text-center text-red-500">' + lang.error_loading_product + '</div>');
			}).always(() => {
				// Do not set popupLoading = false here, wait for Fancybox close
			});
		},

		handlePopupSearch: function (e) {
			clearTimeout(this.searchTimeout);
			const searchTerm = $(e.target).val();
			const $productList = $("#compare-popup-product-list");

			this.searchTimeout = setTimeout(() => {
				$productList.html('<div class="text-center col-span-full py-10">Đang tìm kiếm...</div>');
				$.post(compareData.ajaxurl, {
					action: "search_compare_products",
					search_term: searchTerm,
				}).done((response) => {
					if (response.success) {
						$productList.html(response.data.html);
					} else {
						$productList.html('<div class="col-span-full text-center text-red-500">' + lang.error_loading_product + '</div>');
					}
				}).fail(() => {
					$productList.html('<div class="col-span-full text-center text-red-500">' + lang.error_loading_product + '</div>');
				});
			}, 500); // Debounce search for 500ms
		},

		handleAddToCompareFromPopup: function (e) {
			e.preventDefault();
			const $button = $(e.currentTarget);
			const productId = $button.data("id-product");
			const $productList = $("#compare-popup-product-list"); // Get the product list container

			if ($button.prop('disabled')) {
				return; // Already added or being processed
			}

			if (this.compareProducts.length >= 5) {
				alert(lang.compare_limit_reached);
				return;
			}

			// Add loading indicator inside the product list
			$productList.prepend('<div class="loading-fixed absolute-full bg-white/50 flex-center z-10"><div class="loader"></div></div>');
			$button.prop('disabled', true).addClass('bg-gray-400 cursor-not-allowed').removeClass('bg-Primary-Red hover:bg-Primary-Red/80').text(lang.loading_add);

			$.post(compareData.ajaxurl, {
				action: "add_to_compare",
				product_id: productId,
			}).done((response) => {
				this.compareProducts = response.data.products;
				localStorage.setItem("compareProducts", JSON.stringify(this.compareProducts));
				this.updateCompareSticky();
				$button.text(lang.added); // Update button text immediately
				// No need to remove disabled or change class back, it's added now.
			}).fail(() => {
				// Re-enable button on failure
				$button.prop('disabled', false).removeClass('bg-gray-400 cursor-not-allowed').addClass('bg-Primary-Red hover:bg-Primary-Red/80').text(lang.add_to_compare);
				alert('Có lỗi xảy ra, vui lòng thử lại.');
			}).always(() => {
				// Remove loading indicator
				$productList.find('.loading-fixed').remove();
			});
		},

		updatePopupAddButtonState: function (productId, isAdded) {
			const $button = $(`#dialog-content .btn-product-compare-handle[data-id-product="${productId}"]`);
			if ($button.length) {
				if (isAdded) {
					$button.prop('disabled', true).addClass('bg-gray-400 cursor-not-allowed').removeClass('bg-Primary-Red hover:bg-Primary-Red/80').text(lang.added);
				} else {
					// This case might happen if removed from outside the popup
					$button.prop('disabled', false).removeClass('bg-gray-400 cursor-not-allowed').addClass('bg-Primary-Red hover:bg-Primary-Red/80').text(lang.add_to_compare);
				}
			}
		},
	};

	// Initialize the compare functionality
	compareProduct.init();
});
