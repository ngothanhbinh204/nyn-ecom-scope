<?php

/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.9.0
 */

if (! defined('ABSPATH')) {
	exit;
}

if ($related_products) : ?>
	<section class="section-product-detail product-suggest section-py !pt-0">
		<div class="container">
			<div class="title-32 font-semibold mb-base">
				<?= _e('Sản phẩm liên quan', 'canhcamtheme') ?>
			</div>
			<div class="swiper-cols-3 relative">
				<div class="swiper py-2" swiper-xl-slides="5" swiper-lg-slides="4" swiper-md-slides="3" swiper-sm-slides="2">
					<div class="swiper-wrapper">
						<?php foreach ($related_products as $related_product) : ?>
							<div class="swiper-slide" style="height: auto;">
								<?php
								$post_object = get_post($related_product->get_id());
								setup_postdata($GLOBALS['post'] = &$post_object); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
								wc_get_template_part('content', 'product');
								?>
							</div>
						<?php endforeach; ?>
						<?php woocommerce_product_loop_end(); ?>
					</div>
					<div class="wrap-button-slide">
						<div class="btn btn-prev btn-sw-1"></div>
						<div class="btn btn-next btn-sw-1"></div>
					</div>
				</div>
			</div>
	</section>
<?php
endif;

wp_reset_postdata();
