<?php
// Handle the AJAX request for product categories
function handle_product_category_search()
{
	$keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
	$args = array(
		'taxonomy' => 'product_cat',
		'name__like' => $keyword,
		'number' => 10,
	);

	$categories = get_terms($args);
	$results = array();

	if (!empty($categories) && !is_wp_error($categories)) {
		foreach ($categories as $category) {
			$results[] = array(
				'name' => $category->name,
				'link' => get_term_link($category),
			);
		}
	}

	wp_send_json_success($results);
}
add_action('wp_ajax_nopriv_product_category_search', 'handle_product_category_search');
add_action('wp_ajax_product_category_search', 'handle_product_category_search');

// Handle the AJAX request
function handle_product_search()
{
	$keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
	$args = array(
		'post_type' => 'product',
		'posts_per_page' => 10,
		's' => $keyword,
	);

	$query = new WP_Query($args);
	$results = array();

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$product = wc_get_product(get_the_ID());
			$price_html = $product->get_price_html();
			$results[] = array(
				'title' => get_the_title(),
				'link' => get_permalink(),
				'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
				'price' => ($price_html === '0' || $product->get_price() == 0) ? '' : $price_html,
			);
		}
	}

	wp_send_json_success($results);
}
add_action('wp_ajax_nopriv_product_search', 'handle_product_search');
add_action('wp_ajax_product_search', 'handle_product_search');
