<?php
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
add_action('woocommerce_custom_price', 'woocommerce_template_single_price', 10);

function custome_add_to_cart_message($message, $product_data, $stock_quantity, $stock_quantity_in_cart)
{
	$message =  sprintf(
		'%s',
		/* translators: 1: quantity in stock 2: current quantity */
		sprintf(__('You cannot add that amount to the cart &mdash; we have %1$s in stock and you already have %2$s in your cart.', 'woocommerce'), wc_format_stock_quantity_for_display($stock_quantity, $product_data), wc_format_stock_quantity_for_display($stock_quantity_in_cart, $product_data)),
		esc_url(wc_get_cart_url()),
		esc_attr($wp_button_class),
		__('View cart', 'woocommerce')
	);;
	return $message;
}
add_filter('woocommerce_cart_product_not_enough_stock_already_in_cart_message', 'custome_add_to_cart_message', 10, 4);