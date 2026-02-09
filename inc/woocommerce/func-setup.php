<?php
/**
 * Woo - add theme support
 */

add_action('after_setup_theme', 'woocommerce_support');
function woocommerce_support()
{
	add_theme_support('woocommerce');
}

/**
 * Woo - disable style woo
 */

if (class_exists('Woocommerce')) {
	add_filter('woocommerce_enqueue_styles', '__return_empty_array');
}

/**
 * Woo - remove breadcrumb - remove sidebar
 */

remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
/**
 * Woo - remove pagination from product list
 */
remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);




/**
 * Enqueue WooCommerce custom scripts
 */
function cc_woocommerce_scripts()
{
	wp_enqueue_script(
		'cc-woocommerce',
		get_template_directory_uri() . '/scripts/woocommerce/cc-woocommerce.js',
		array('jquery'),
		GENERATE_VERSION,
		true
	);

	wp_localize_script('cc-woocommerce', 'cc_woocommerce_params', array(
		'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('cc_woocommerce_nonce'),
		'apply_coupon_nonce' => wp_create_nonce('apply_coupon_nonce'),
		'remove_coupon_nonce' => wp_create_nonce('remove_coupon_nonce'),
		'woo_quantity_max' => __('Số lượng có thể đặt hàng tối đa là ', 'woocommerce'),
		'woo_quantity_min' => __('Số lượng có thể đặt hàng tối thiểu là ', 'woocommerce'),
		'cancel_order_confirm' => __('Bạn có chắc chắn muốn hủy đơn hàng này không?', 'canhcamtheme'),
		'cancel_order_success' => __('Hủy đơn hàng thành công', 'canhcamtheme'),
		'cancel_order_error' => __('Hủy đơn hàng thất bại', 'canhcamtheme'),
		'no_result' => __('No products found', 'woocommerce'),
	));
	wp_register_style('woocommerce-css', get_template_directory_uri() . '/styles/woocommerce/index.css', array(), GENERATE_VERSION, 'all');
	wp_enqueue_style('woocommerce-css');
}
add_action('wp_enqueue_scripts', 'cc_woocommerce_scripts');


/**
 * Product Viewed
 */

function track_viewed_products()
{
	if (!is_product() || !function_exists('wc_get_product')) return;

	$product_id = get_the_ID();
	// Get existing viewed products
	$viewed = (array) explode(',', (string) $_COOKIE['woocommerce_recently_viewed']);

	// Add new product and limit to 15 items
	if (!in_array($product_id, $viewed)) {
		array_unshift($viewed, $product_id);
		$viewed = array_slice(array_unique($viewed), 0, 15);
	}

	// Set cookie for 30 days
	wc_setcookie('woocommerce_recently_viewed', implode(',', $viewed), time() + DAY_IN_SECONDS * 30);
}
add_action('template_redirect', 'track_viewed_products');

function get_viewed_products()
{
	$viewed_ids = explode(',', (string) $_COOKIE['woocommerce_recently_viewed']);
	$filter_viewed_by_lang = array();

	foreach ($viewed_ids as $product_id) {
		$id_product_translate = get_id_language($product_id, 'product');
		if ($id_product_translate) {
			$filter_viewed_by_lang[] = $id_product_translate;
		}
	}

	$products = array();

	foreach ($filter_viewed_by_lang as $product_id) {
		$product = wc_get_product($product_id);
		if ($product && $product->is_visible()) {
			$products[] = array(
				'id' => $product_id,
				'title' => $product->get_title(),
				'url' => get_permalink($product_id),
				'price' => $product->get_price_html()
			);
		}
	}

	return $products;
}


/**
 * Add Quantity 
 */

add_action('woocommerce_after_quantity_input_field', 'devqn_quantity_plus');
add_action('woocommerce_before_quantity_input_field', 'devqn_quantity_minus');

function devqn_quantity_plus()
{
	global $product;
	// Check if product exists and is not sold individually
	if (($product && !$product->is_sold_individually()) || (isset($GLOBALS['woocommerce']->cart) && !empty($GLOBALS['woocommerce']->cart->get_cart()))) {
		echo '<button type="button" class="plus" >+</button>';
	}
}

function devqn_quantity_minus()
{
	global $product;
	// Check if product exists and is not sold individually
	if (($product && !$product->is_sold_individually()) || (isset($GLOBALS['woocommerce']->cart) && !empty($GLOBALS['woocommerce']->cart->get_cart()))) {
		echo '<button type="button" class="minus">-</button>';
	}
}

/**
 * Wrap Product Quantity and Add to Cart
 */

add_action('woocommerce_before_add_to_cart_button', 'wrap_product_quantity_and_add_to_cart');

function wrap_product_quantity_and_add_to_cart()
{
	echo '<div class="product-wrap-buy flex flex-wrap md:flex-row flex-col md:items-center gap-5">';
}

add_action('woocommerce_after_add_to_cart_button', 'close_wrap_product_quantity_and_add_to_cart');

function close_wrap_product_quantity_and_add_to_cart()
{
	echo '</div>';
}



/**
 * Woo - update count quantity cart
 */

add_filter('woocommerce_add_to_cart_fragments', 'wc_refresh_mini_cart_count');
function wc_refresh_mini_cart_count($fragments)
{
	ob_start();
	$items_count = WC()->cart->get_cart_contents_count();
?>
	<span class="count-cart"><strong>(<?php echo $items_count ? $items_count : 0; ?>)</strong></span>
<?php
	$fragments['.count-cart'] = ob_get_clean();
	return $fragments;
}


/**
 * Woo - remove My Account Menu Links
 */

add_filter('woocommerce_account_menu_items', 'misha_remove_my_account_links');
function misha_remove_my_account_links($menu_links)
{
	unset($menu_links['downloads']); // Disable Downloads
	return $menu_links;
}


/**
 * Woo - Add button add to cart - Ajax
 */

function woo_add_buy_now_button()
{
?>
	<button type="button" class="product-buy-now btn btn-primary solid flex-center lowercase w-full buy_now_button">
		<span><?= __('Buy now', 'woocommerce') ?></span>
		<!-- <i class="fa-regular fa-paper-plane-top"></i> -->
	</button>
	<script>
		jQuery(document).ready(function($) {
			// Custom add to cart button
			$("body").on("click", ".single_add_to_cart_button:not(.buynow)", function(e) {
				e.preventDefault();
				// Declaration
				var $thisbutton = $(this),
					$form = $thisbutton.closest('form.cart'),
					id = $thisbutton.val(),
					product_qty = $form.find('input[name=quantity]').val() || 1,
					variation_id = $form.find('input[name=variation_id]').val() || 0,
					product_id = $form.find('input[name=product_id]').val() || id;
				var dataNoti = {
					title: $thisbutton.closest('.section-product-detail.section-1 .wrap-content').find('.product-title').text(),
					variation: $form.find('li.thwvsf-selected span').text(),
					price: $form.find('.price').html() || $thisbutton.closest('.section-product-detail').find('.product-price').html(),
				}
				var data = {
					action: 'woocommerce_ajax_add_to_cart',
					product_id: product_id,
					product_sku: '',
					variation_id: variation_id,
					quantity: product_qty,
				};
				$(document.body).trigger('adding_to_cart', [$thisbutton, data]);
				$.ajax({
					type: 'post',
					url: woocommerce_params.ajax_url,
					data: data,
					beforeSend: function(response) {
						$('.loading-bar').css({
							'width': '40%',
							'opacity': '1'
						});
						$thisbutton.addClass('disable loading');
					},
					complete: function(response) {
						$('.loading-bar').css({
							'width': '100%',
							'opacity': '1'
						});
						setTimeout(function() {
							$('.loading-bar').css({
								'width': '0',
								'opacity': '0'
							});
						}, 500);
						$thisbutton.removeClass('disable loading');
						$('.add_to_cart_button').removeClass('disable loading');
						$('.shopping-cart-wrapper, .overlay-blur').addClass('active');
					},
					success: function(response) {
						if (response.error && response.product_url) {
							// window.location = response.product_url;
							alert(response.message);
							return;
						} else {
							$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton, dataNoti]);
						}

						const miniCart = response.fragments["div.widget_shopping_cart_content"];
						const countCart = response.fragments[".count-cart"];
						$(".widget_shopping_cart_content").replaceWith(miniCart);
						$(".count-cart").replaceWith(countCart);
						$('body').addClass('show-cart')
					},
				});
				return false;
			});
			// Custom buy now button
			$("body").on("click", ".buy_now_button", function(e) {
				e.preventDefault();
				if ($(this).hasClass('disable')) {
					return false;
				}
				$(this).addClass('disable loading');
				$('.single_add_to_cart_button').addClass('buynow')
				$('.single_add_to_cart_button').trigger('click');
			});
		});
	</script>
<?php
}

add_action('woocommerce_after_add_to_cart_button', 'woo_add_buy_now_button', 9);



/**
 * Woo - Add to cart - Ajax
 */
add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');

function woocommerce_ajax_add_to_cart()
{
	global $woocommerce;

	$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
	$quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
	$variation_id = absint($_POST['variation_id']);
	$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
	$product_status = get_post_status($product_id);

	// Thêm xử lý cho selected_gift
	$selected_gift = isset($_POST['selected_gift']) ? $_POST['selected_gift'] : '';

	// Nếu validation thành công và trạng thái sản phẩm đã publish
	error_log('passed_validation: ' . $passed_validation);
	error_log('product_status: ' . $product_status);
	if ($passed_validation && 'publish' === $product_status) {
		// Thêm selected_gift vào cart item data
		$custom_data = array();
		if (!empty($selected_gift)) {
			$selected_gift = json_decode(stripslashes($selected_gift), true);

			// Xác định loại quà tặng
			$gift_type = $selected_gift['gift_type'] ?? '';

			if ($gift_type === 'online' || $gift_type === 'category_online') {
				$custom_data['gift-online'] = array($selected_gift);
			} elseif ($gift_type === 'offline' || $gift_type === 'category_offline') {
				$custom_data['gift-offline'] = array($selected_gift);
			}
		}
		// Check if product is already in cart (for "Sold individually" case)
		$product_in_cart = false;
		$error_message = '';

		// Get the product
		$product = wc_get_product($product_id);

		// Check if product is sold individually
		$sold_individually = $product->is_sold_individually();

		// Check if product is already in cart
		foreach (WC()->cart->get_cart() as $cart_item) {
			if ($cart_item['product_id'] == $product_id) {
				$product_in_cart = true;
				if ($sold_individually) {
					$error_message = sprintf(__('You cannot add more "%s" to your cart. This product is sold individually.', 'woocommerce'), $product->get_name());
				}
				break;
			}
		}

		// If product is not in cart or isn't sold individually, add it
		if (!$product_in_cart || !$sold_individually) {
			$added = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, array(), $custom_data);
			error_log('added: ' . $added);

			if ($added) {
				do_action('woocommerce_ajax_added_to_cart', $product_id);

				if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
					wc_add_to_cart_message(array($product_id => $quantity), true);
				}

				WC_AJAX::get_refreshed_fragments();
			} else {
				// Get the last error
				$notices = wc_get_notices('error');
				$error_message = !empty($notices) ? end($notices)['notice'] : __('Failed to add product to cart.', 'woocommerce');

				$data = array(
					'error' => true,
					'message' => $error_message,
					'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
				);

				wp_send_json($data);
			}
		} else {
			$data = array(
				'error' => true,
				'message' => $error_message,
				'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
			);

			wp_send_json($data);
		}
	} else {
		$notices = wc_get_notices('error');
		error_log(print_r($notices, true));
		$error_message = !empty($notices) ? end($notices)['notice'] : __('Failed to add product to cart.', 'woocommerce');
		$data = array(
			'error' => true,
			'message' => $error_message,
			'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
		);
		wp_send_json($data);
	}
	wp_die();
}


function add_loading_css()
{
	echo '<div class="loading-bar"></div>';
}

add_action('wp_footer', 'add_loading_css');


/**
 * Update cart item in cart and mini cart
 */



add_action('wp_ajax_update_cart_item', 'update_cart_item');
add_action('wp_ajax_nopriv_update_cart_item', 'update_cart_item');

function update_cart_item()
{
	$cart_item_key = sanitize_text_field($_POST['cart_item_key']);
	$quantity = intval($_POST['quantity']);

	if ($quantity > 0 && WC()->cart->get_cart_item($cart_item_key)) {
		WC()->cart->set_quantity($cart_item_key, $quantity, true);
		WC()->cart->calculate_totals();
	}

	WC_AJAX::get_refreshed_fragments();
	wp_die();
}
/**
 * Custom quantity for cartpage
 */
function woo_quantity_cart_page()
{
?>
	<script type="text/javascript">
		// jQuery(document).ready(function() {
		// 	if (typeof cc_woocommerce_params === 'undefined') return false;
		// 	jQuery(document).on('click', '.woocommerce-cart-form .minus, .woocommerce-cart-form .plus', function(e) {
		// 		setTimeout(() => {
		// 			jQuery('body').find('[name="update_cart"]').prop('disabled', false)
		// 			jQuery('body').find('[name="update_cart"]').trigger('click')
		// 		}, 400);
		// 		jQuery(document.body).trigger('wc_fragment_refresh'); // Đoạn này giúp nó refresh lại page khi update sản phẩm
		// 		jQuery(document.body).trigger('wc_fragments_refreshed');

		// 		e.stopPropagation()
		// 	})
		// })
	</script>
<?php
}

add_action('wp_footer', 'woo_quantity_cart_page', 100);


/**
 * Delay sending email
 */
// add_filter('woocommerce_defer_transactional_emails', '__return_true');


// add_filter('woocommerce_form_field', 'checkout_fields_in_label_error', 10, 4);
// function checkout_fields_in_label_error($field, $key, $args, $value)
// {
// 	if (strpos($field, '</span>') !== false && $args['required']) {
// 		$error = '<span class="error" style="display:none">';
// 		$error .= sprintf(__('%s is a required field.', 'woocommerce'), $args['label']);
// 		$error .= '</span>';
// 		$field = substr_replace($field, $error, strpos($field, '</span>'), 0);
// 	}
// 	return $field;
// }

add_filter('woocommerce_form_field', 'woocommerce_checkout_fields_inline_error', 10, 4);
function woocommerce_checkout_fields_inline_error($field, $key, $args, $value)
{
	if ($args['required']) {
		$error = '<span class="error" style="display:none">';
		$error .= sprintf(__('* %s là trường bắt buộc.', 'canhcamtheme'), $args['label']);
		$error .= '</span>';

		// Insert the error after the input field
		$closing_div_pos = strrpos($field, '</p>');  // Assuming fields are wrapped in <p> tags
		if ($closing_div_pos !== false) {
			$field = substr_replace($field, $error, $closing_div_pos, 0);
		}
	}
	return $field;
}

add_action('woocommerce_widget_shopping_cart_total', 'bbloomer_minicart_custom_text', 1);

function bbloomer_minicart_custom_text()
{
	if (! WC()->cart) return;
	echo '<strong class="mini_cart_text">' . __('Tạm tính', 'woocommerce') . ': </strong>';
}
add_filter('woocommerce_my_account_my_orders_query', 'custom_my_account_orders', 10, 1);
function custom_my_account_orders($args)
{
	// Set the post per page
	$args['limit'] = 10;
	return $args;
}



/**
 * Enqueue custom script for Product edit screen in WP Admin.
 */
function custom_enqueue_product_edit_script($hook)
{
	global $post_type, $pagenow; // Added $pagenow check

	// Check if we are on the post edit screen (post.php or post-new.php) and the post type is 'product'
	if (in_array($pagenow, ['post.php', 'post-new.php']) && 'product' === $post_type) {

		// Define GENERATE_VERSION if it's not already defined to avoid errors
		if (!defined('GENERATE_VERSION')) {
			define('GENERATE_VERSION', '1.0.0'); // Or use wp_get_theme()->get('Version')
		}

		wp_enqueue_script(
			'custom-product-admin-script', // Handle for the script
			get_template_directory_uri() . '/scripts/custom-product-admin.js', // Path to the script file
			array('jquery', 'acf-input'), // Dependencies: jQuery and ACF input scripts
			GENERATE_VERSION, // Version number
			true // Load in footer
		);
		wp_enqueue_style(
			'custom-product-admin', // Handle for the script
			get_template_directory_uri() . '/styles/custom-product-admin.css', // Path to the script file
			array(), // Dependencies: jQuery and ACF input scripts
			'2.0.0', // Version number 
			'all' // Media type - apply to all media types
		);
	}
}
add_action('admin_enqueue_scripts', 'custom_enqueue_product_edit_script', 20);



// Remove related products
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);

add_filter('woocommerce_output_related_products_args', 'bbloomer_change_number_related_products', 9999);

function bbloomer_change_number_related_products($args)
{
	$args['posts_per_page'] = 8; // # of related products
	return $args;
}


/**
 * Limit cart sync to WooCommerce pages only
 */
function setup_cart_fragments_optimization()
{
	wp_enqueue_script('wc-cart-fragments');
	add_filter('woocommerce_get_script_data', 'limit_cart_sync_to_wc_pages', 10, 2);
}
add_action('wp_enqueue_scripts', 'setup_cart_fragments_optimization');

function limit_cart_sync_to_wc_pages($script_data, $handle)
{
	if ('wc-cart-fragments' === $handle) {
		if (is_woocommerce() || is_cart() || is_checkout()) {
			return $script_data;
		}
		return null;
	}
	return $script_data;
}
add_filter('wc_add_to_cart_message', 'remove_add_to_cart_message');

function remove_add_to_cart_message()
{
	return;
}

/**
 * 
 */


add_filter('woocommerce_dropdown_variation_attribute_options_html', 'wc_dropdown_variation_attribute_options_sorted', 20, 2);
function wc_dropdown_variation_attribute_options_sorted($html, $args)
{
	$args = wp_parse_args(
		apply_filters('woocommerce_dropdown_variation_attribute_options_args', $args),
		array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => __('Choose an option', 'woocommerce'),
		)
	);

	// Get selected value.
	if (false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product) {
		$selected_key = 'attribute_' . sanitize_title($args['attribute']);
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$args['selected'] = isset($_REQUEST[$selected_key]) ? wc_clean(wp_unslash($_REQUEST[$selected_key])) : $args['product']->get_variation_default_attribute($args['attribute']);
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	$options               = $args['options'];
	$product               = $args['product'];
	$attribute             = $args['attribute'];
	$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title($attribute);
	$id                    = $args['id'] ? $args['id'] : sanitize_title($attribute);
	$class                 = $args['class'];
	$show_option_none      = (bool) $args['show_option_none'];
	$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __('Choose an option', 'woocommerce'); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

	if (empty($options) && ! empty($product) && ! empty($attribute)) {
		$attributes = $product->get_variation_attributes();
		$options    = $attributes[$attribute];
	}

	$html  = '<select id="' . esc_attr($id) . '" class="' . esc_attr($class) . '" name="' . esc_attr($name) . '" data-attribute_name="attribute_' . esc_attr(sanitize_title($attribute)) . '" data-show_option_none="' . ($show_option_none ? 'yes' : 'no') . '">';
	$html .= '<option value="">' . esc_html($show_option_none_text) . '</option>';

	if (! empty($options)) {
		if ($product && taxonomy_exists($attribute)) {
			// Get terms if this is a taxonomy - ordered. We need the names too.
			$terms = wc_get_product_terms(
				$product->get_id(),
				$attribute,
				array(
					'fields' => 'all',
				)
			);

			//sorting starts here
			foreach ($terms as $key => $term) {
				$i = 0;
				foreach ($product->get_available_variations() as $variation) {
					$i++;
					if ($term->slug == $variation['attributes'][$name]) {
						$key = $i - 1;
						unset($terms[$key]);
						$terms[$key] = $term;
					}
				}
			}

			ksort($terms);

			foreach ($terms as $term) {
				if (in_array($term->slug, $options, true)) {
					$html .= '<option value="' . esc_attr($term->slug) . '" ' . selected(sanitize_title($args['selected']), $term->slug, false) . '>' . esc_html(apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute, $product)) . '</option>';
				}
			}
		} else {
			foreach ($options as $option) {
				// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
				$selected = sanitize_title($args['selected']) === $args['selected'] ? selected($args['selected'], sanitize_title($option), false) : selected($args['selected'], $option, false);
				$html    .= '<option value="' . esc_attr($option) . '" ' . $selected . '>' . esc_html(apply_filters('woocommerce_variation_option_name', $option, null, $attribute, $product)) . '</option>';
			}
		}
	}

	return $html .= '</select>';
}


/**
 * Woo - Cancel order
 */

add_filter('woocommerce_valid_order_statuses_for_cancel', 'filter_valid_order_statuses_for_cancel');
function filter_valid_order_statuses_for_cancel($statuses)
{
	if (! is_wc_endpoint_url('orders')) {
		$statuses = array('on-hold', 'pending', 'failed'); // Define order statuses
	}
	return $statuses;
}

function custom_modify_cancel_order_action_url($actions, $order)
{
	if (isset($actions['cancel'])) {
		$my_account_url = wc_get_page_permalink('myaccount');
		$current_url_for_redirect = home_url(add_query_arg(null, null));

		if (! empty($current_url_for_redirect)) {
			$actions['cancel']['url'] = $order->get_cancel_order_url($current_url_for_redirect);
		} else {
			$actions['cancel']['url'] = $order->get_cancel_order_url($my_account_url);
		}
	}
	return $actions;
}
add_filter('woocommerce_my_account_my_orders_actions', 'custom_modify_cancel_order_action_url', 10, 2);

/**
 * Remove shipping calc in page cart
 */

function disable_shipping_calc_on_cart($show_shipping)
{
	if (is_cart()) {
		return false;
	}
	return $show_shipping;
}
add_filter('woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_calc_on_cart', 99);

?>