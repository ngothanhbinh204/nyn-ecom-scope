<?php

defined('ABSPATH') || exit;

/**
 * Filter the arguments passed to wc_get_orders in the My Account > Orders page.
 *
 * @param array $args The arguments for wc_get_orders.
 * @return array Modified arguments.
 */
function cc_filter_my_account_orders($args)
{

	// Check if search query is set and not empty
	if (isset($_GET['search_query']) && ! empty($_GET['search_query'])) {
		$search_query = sanitize_text_field($_GET['search_query']);
		// Remove '#' from search query
		$search_query = str_replace('#', '', $search_query);
		if (is_numeric($search_query)) {
			$args['post__in'] = array($search_query);
		} else {
			return array();
		}
	}

	// Check if order date is set and not empty
	if (isset($_GET['order_date']) && ! empty($_GET['order_date'])) {
		$order_date_str = sanitize_text_field($_GET['order_date']);
		$date_obj = DateTime::createFromFormat('d/m/Y', $order_date_str);
		if ($date_obj && $date_obj->format('d/m/Y') === $order_date_str) {
			$args['date_query'] = array(
				array(
					'year'  => $date_obj->format('Y'),
					'month' => $date_obj->format('m'),
					'day'   => $date_obj->format('d'),
				),
			);
		} else {
			return array();
		}
	}

	return $args;
}
add_filter('woocommerce_my_account_my_orders_query', 'cc_filter_my_account_orders');
