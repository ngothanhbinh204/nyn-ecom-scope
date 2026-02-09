<?php
define('GENERATE_VERSION', '1.0.0');

require get_template_directory() . '/inc/function-setup.php';
require get_template_directory() . '/inc/function-root.php';
require get_template_directory() . '/inc/function-field.php';
require get_template_directory() . '/inc/function-pagination.php';
require get_template_directory() . '/inc/function-custom.php';
require get_template_directory() . '/inc/function-woo.php';
require get_template_directory() . '/inc/walker-product-menu.php';

// Woocommerce
require get_template_directory() . '/inc/woocommerce/func-setup.php';
require get_template_directory() . '/inc/woocommerce/func-product-item.php';
require get_template_directory() . '/inc/woocommerce/func-product-detail.php';
require get_template_directory() . '/inc/woocommerce/func-checkout.php';
require get_template_directory() . '/inc/woocommerce/func-register.php';
require get_template_directory() . '/inc/woocommerce/func-account.php';
require get_template_directory() . '/inc/woocommerce/func-woo-search.php';
require get_template_directory() . '/inc/woocommerce/func-search-orders.php';