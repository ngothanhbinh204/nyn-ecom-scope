<?php

/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined('ABSPATH') || exit;

?>
<div class="cart_totals <?php echo (WC()->customer->has_calculated_shipping()) ? 'calculated_shipping' : ''; ?>">

	<?php do_action('woocommerce_before_cart_totals'); ?>

	<div class="flex gap-5 justify-between items-center py-3 total-cart">
		<div class="text">
			<?php esc_html_e('Tạm tính', 'woocommerce'); ?>
		</div>
		<div class="value">
			<?php wc_cart_totals_subtotal_html(); ?>
		</div>
	</div>
	<?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
		<div class="flex gap-5 justify-between items-center py-3 shipping total-cart">
			<div class="text">
				<?php do_action('woocommerce_cart_totals_before_shipping'); ?>

				<?php wc_cart_totals_shipping_html(); ?>

				<?php do_action('woocommerce_cart_totals_after_shipping'); ?>
			</div>
		</div>
	<?php elseif (WC()->cart->needs_shipping() && 'yes' === get_option('woocommerce_enable_shipping_calc')) : ?>
		<div class="flex gap-5 justify-between items-center py-3 shipping">
			<div class="text">
				<?php esc_html_e('Shipping', 'woocommerce'); ?>
			</div>
			<div class="value" data-title="<?php esc_attr_e('Shipping', 'woocommerce'); ?>">
				<?php woocommerce_shipping_calculator(); ?>
			</div>
		</div>
	<?php endif; ?>
	<?php foreach (WC()->cart->get_fees() as $fee) : ?>
		<div class="flex gap-5 justify-between items-center py-3 total-cart">
			<div class="text">
				<?php echo esc_html($fee->name); ?>
			</div>
			<div class="value">
				<?php wc_cart_totals_fee_html($fee); ?>
			</div>
		</div>
	<?php endforeach; ?>
	<?php do_action('woocommerce_cart_totals_before_order_total'); ?>
	<div class="flex gap-5 justify-between items-center py-3 total-cart">
		<div class="text">
			<?php esc_html_e('Tổng tiền', 'woocommerce'); ?>
		</div>
		<div class="value">
			<?php wc_cart_totals_order_total_html(); ?>
		</div>
	</div>
	<?php do_action('woocommerce_cart_totals_after_order_total'); ?>

	<div class="wc-proceed-to-checkout">
		<?php do_action('woocommerce_proceed_to_checkout'); ?>
	</div>

	<?php do_action('woocommerce_after_cart_totals'); ?>

</div>