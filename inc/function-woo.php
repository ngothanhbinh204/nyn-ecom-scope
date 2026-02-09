<?php

add_action('woocommerce_widget_shopping_cart_total', 'custom_display_cart_total', 20);

function custom_display_cart_total()
{
	// Get the WooCommerce cart object
	$cart = WC()->cart;

	// Get the cart total (including taxes, shipping, etc.)
	$cart_total = $cart->get_total();

	// Output the total with proper formatting
	echo '<strong>' . esc_html__('Total:', 'woocommerce') . '</strong> ' . $cart_total;
}




// 1. Thay đổi tên trạng thái đơn hàng "On-Hold" thành "Chưa thanh toán"
add_filter('wc_order_statuses', 'customize_order_status_name');

function customize_order_status_name($statuses)
{
	if (isset($statuses['wc-on-hold'])) {
		$statuses['wc-on-hold'] = __('Chưa thanh toán', 'woocommerce');
	}
	return $statuses;
}

add_filter('woocommerce_my_account_my_orders_actions', 'customize_order_actions_based_on_payment_method', 10, 2);

function customize_order_actions_based_on_payment_method($actions, $order)
{
	// Lấy trạng thái đơn hàng
	$order_status = $order->get_status();
	// Lấy phương thức thanh toán
	$payment_method = $order->get_payment_method();

	// 1. Đối với đơn hàng Failed
	if ($order_status === 'failed') {
		// Nếu là onepay, giữ lại nút Pay (thanh toán lại)
		if ($payment_method === 'onepay') {
			// Thêm nút Pay nếu chưa có và cần thanh toán
			if (!isset($actions['pay']) && $order->needs_payment()) {
				$actions['pay'] = array(
					'url'  => $order->get_checkout_payment_url(),
					'name' => __('Pay', 'woocommerce'),
				);
			}
		} else {
			// Đối với các phương thức thanh toán khác, ẩn cả Pay và Cancel
			unset($actions['pay']);
			unset($actions['cancel']);
		}
	}

	// 2. Đối với đơn hàng On-Hold (tạm giữ)
	if ($order_status === 'on-hold') {
		// Chỉ hiển thị nút Pay cho phương thức chuyển khoản ngân hàng
		if ($payment_method === 'bacs') {
			// Thêm nút Pay nếu chưa có và cần thanh toán
			if (!isset($actions['pay']) && $order->needs_payment()) {
				$actions['pay'] = array(
					'url'  => $order->get_checkout_payment_url(),
					'name' => __('Pay', 'woocommerce'),
				);
			}

			// Giữ nút Cancel
			if (!isset($actions['cancel']) && $order->has_status(array('pending', 'on-hold'))) {
				$actions['cancel'] = array(
					'url'  => $order->get_cancel_order_url(),
					'name' => __('Cancel', 'woocommerce'),
				);
			}
		} else {
			// Đối với các phương thức thanh toán khác, ẩn cả Pay và Cancel
			unset($actions['pay']);
			unset($actions['cancel']);
		}
	}

	return $actions;
}


add_filter('woocommerce_order_needs_payment', 'custom_add_on_hold_to_needs_payment', 10, 3);
function custom_add_on_hold_to_needs_payment($needs_payment, $order, $valid_order_statuses)
{
	// Kiểm tra nếu đơn hàng ở trạng thái On-hold
	if ($order->has_status('on-hold')) {
		$needs_payment = true; // Đặt needs_payment thành true cho trạng thái On-hold
	}
	return $needs_payment;
}
