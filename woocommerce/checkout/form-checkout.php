<?php

/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (!defined('ABSPATH')) {
	exit;
}
?>
<div class="global-breadcrumb">
					<div class="container">
						<?php 
						if ( function_exists('yoast_breadcrumb') ) {
							yoast_breadcrumb( '<nav class="rank-math-breadcrumb" aria-label="breadcrumbs">','</nav>' );
						} elseif ( function_exists('rank_math_the_breadcrumbs') ) {
							rank_math_the_breadcrumbs();
						} else {
							woocommerce_breadcrumb();
						}
						?>
					</div>
				</div>
<section class="section-checkout section-padding">
	<div class="container">
		<?php
		do_action('woocommerce_before_checkout_form', $checkout);

		// If checkout registration is disabled and not logged in, the user cannot checkout.
		if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
			echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
			return;
		}
		?>
		<?php do_action('woocommerce_checkout_before_order_review'); ?>
		<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
			<div class="row">
				<div class="col lg:w-8/12">
					<div class="wrap-form-checkout section-wrap-box-white p-7.5">
						<div class="title-line">
							<?php _e('Thông tin giao hàng', 'canhcamtheme') ?>
						</div>
						<?php do_action('woocommerce_checkout_billing'); ?>
						<?php do_action('custom_woocommerce_payment') ?>
						<?php if ($checkout->get_checkout_fields()) : ?>
							<?php do_action('woocommerce_checkout_before_customer_details');
							?>
							<?php do_action('woocommerce_checkout_shipping');
							?>
							<?php do_action('woocommerce_checkout_after_customer_details');
							?>
						<?php endif; ?>
						<?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
						<div class="text-red-500 text-sm mt-8">
							<?=
							get_field('payment_note', 'options')
							?>
						</div>
						<?php do_action('custom_button_checkout') ?>
					</div>
				</div>
				<div class="col lg:w-4/12">
					<div class="section-wrap-box-white p-7.5 mb-5">
						<div class="title-line mb-0">
							<?php _e('Đơn hàng của bạn', 'canhcamtheme') ?>
						</div>
						<div id="order_review" class="woocommerce-checkout-review-order">
							<?php do_action('woocommerce_checkout_order_review'); ?>
						</div>
					</div>
					<div class="form-row place-order">
						<noscript>
							<?php
							/* translators: $1 and $2 opening and closing emphasis tags respectively */
							printf(esc_html__('Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce'), '<em>', '</em>');
							?>
							<br /><button type="submit" class="button alt<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e('Update totals', 'woocommerce'); ?>"><?php esc_html_e('Update totals', 'woocommerce'); ?></button>
						</noscript>

						<?php wc_get_template('checkout/terms.php'); ?>

						<?php do_action('woocommerce_review_order_before_submit'); ?>

						<?php echo apply_filters('woocommerce_order_button_html', '<button type="submit" class="button alt' . esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : '') . '" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">' . esc_html($order_button_text) . '</button>'); // @codingStandardsIgnoreLine
						?>

						<?php do_action('woocommerce_review_order_after_submit'); ?>

						<?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
					</div>
					<div class="section-wrap-box-white p-7.5 mt-5 box-coupon">
						<div class="title-line">
							<?php _e('Mã giảm giá', 'canhcamtheme') ?>
						</div>
						<div class="coupon-form-container">
							<?php do_action('woocommerce_checkout_coupon_form'); ?>
						</div>
					</div>
				</div>
			</div>
		</form>
		<?php do_action('woocommerce_checkout_after_order_review'); ?>
	</div>
</section>

<style>
	.woocommerce-message {
		display: none;
	}

	.checkout_coupon.woocommerce-form-coupon {
		display: block !important;
	}

	.woocommerce-form-coupon-toggle {
		display: none;
	}

	.optional {
		display: none;
	}

	/* Coupon form enhancements */
	.coupon-toggle-container {
		margin-bottom: 15px;
	}

	.toggle-coupon-form {
		background: transparent;
		border: 1px solid #ddd;
		padding: 10px 15px;
		border-radius: 4px;
		cursor: pointer;
		width: 100%;
		text-align: left;
		transition: all 0.3s ease;
	}

	.toggle-coupon-form:hover {
		background-color: #f8f9fa;
		border-color: #007cba;
	}

	.ajax-coupon-form {
		margin-top: 15px;
		padding-top: 15px;
		border-top: 1px solid #eee;
	}

	.ajax-coupon-form .ajax-apply-coupon.loading {
		opacity: 0.6;
		cursor: not-allowed;
	}

	.coupon-messages .woocommerce-message,
	.coupon-messages .woocommerce-error {
		margin: 0;
		padding: 10px 15px;
		border-radius: 4px;
		font-size: 14px;
	}

	.coupon-messages .woocommerce-message {
		background-color: #d4edda;
		border-color: #c3e6cb;
		color: #155724;
	}

	.coupon-messages .woocommerce-error {
		background-color: #f8d7da;
		border-color: #f5c6cb;
		color: #721c24;
	}

	@media (max-width: 767.98px) {
		.section-checkout .row {
			flex-direction: column;
			gap: 20px;
		}
	}
</style>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>