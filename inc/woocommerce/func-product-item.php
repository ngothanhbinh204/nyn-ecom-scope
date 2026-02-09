<?php
// Product SKU
add_action('woocommerce_product_sku', function () {
	global $product;
	if ($product->get_sku()) {
		echo 'SKU ' . $product->get_sku();
	}
});

add_action('woocommerce_template_loop_add_to_cart', function () {
	global $product;
	$available_variations = $product->is_type('variable') ? count($product->get_available_variations()) : null;
	if ($product->get_price() == 0 || !$product->is_purchasable() || !$product->is_in_stock() || ($available_variations == 0 && $product->is_type('variable'))) {
		echo '<a href="' . get_the_permalink($product->get_id()) . '" class="btn btn-add-to-cart solid uppercase"><span>' . __('Liên hệ', 'canhcamtheme') . '</span><i class="fa-regular fa-comment-pen"></i></a>';
	} else if ($product->is_type('variable')) {
		echo '<a href="' . get_the_permalink($product->get_id()) . '" class="btn btn-add-to-cart"><span>' . __('Xem chi tiết', 'canhcamtheme') . '</span><i class="fa-regular fa-eye"></i></a>';
	} else {
		echo sprintf(
			'<a href="%s" class="btn btn-add-to-cart quick-add-to-cart" data-product-id="%s" data-quantity="1" data-added="' . __('Đã thêm vào giỏ hàng', 'canhcamtheme') . '"><span>' . __('Thêm vào giỏ hàng', 'canhcamtheme') . '</span><i class="fa-regular fa-cart-flatbed-boxes"></i></a>',
			esc_url($product->get_permalink()),
			$product->get_id()
		);
	}
});

// Add same condition for single product page
add_filter('woocommerce_is_purchasable', function ($is_purchasable, $product) {
	if (!$product->is_in_stock() || $product->get_price() == 0) {
		return false;
	}
	return $is_purchasable;
}, 10, 2);

add_action('woocommerce_single_product_summary', function () {
	global $product;
	$available_variations = $product->is_type('variable') ? count($product->get_available_variations()) : null;
	if ((!$product->is_purchasable() || !$product->is_in_stock()) && $product->is_type('simple')) {
		echo '<a href="javascript:;" class="btn btn-add-to-cart solid uppercase" data-fancybox data-src="#popup-register-consultation" data-title-san-pham="' . $product->get_name() . '"><span>' . __('Liên hệ', 'canhcamtheme') . '</span><i class="fa-regular fa-comment-pen"></i></a>';
	} else if ($product->is_type('variable') && (!$product->is_in_stock() || (!$product->is_purchasable()))) {
		// echo '<a href="javascript:;" class="btn btn-add-to-cart solid uppercase" data-fancybox data-src="#popup-register-consultation" data-title-san-pham="' . $product->get_name() . '"><span>' . __('Liên hệ', 'canhcamtheme') . '</span><i class="fa-regular fa-comment-pen"></i></a>';
	}
}, 5);