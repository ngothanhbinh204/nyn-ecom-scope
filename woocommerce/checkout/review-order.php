<?php

/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;
?>
<div class="shop_table woocommerce-checkout-review-order-table">
	<?php
	do_action('woocommerce_review_order_before_cart_contents');

	foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
		$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
		$product_id = $_product->get_id();
		if ($_product->is_type('variation')) {
			$product_id = $_product->get_parent_id();
		}
		$image_product = get_image_post($product_id, 'url');
		if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
	?>

			<div class="item-review-order <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
				<div class="wrap-img">
					<div class="img">
						<img src="<?php echo $image_product; ?>" alt="">
					</div>
					<div class="wrap flex-1">
						<div class="title product-name flex justify-between gap-1 flex-wrap">
							<p>
								<?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)) . '&nbsp;'; ?>
							</p>
						</div>
						<div class="wrap-price-right flex-1">
							<div class="price flex items-center gap-1">
								<style>
									.wrap-price-checkout ins {
										text-decoration: none;
										font-size: 0.7291666667rem;
									}

									.wrap-price-checkout del {
										font-size: 12px;
									}

									.wrap-price-checkout {
										display: flex;
										gap: .3rem;
										align-items: baseline;
										flex-direction: row-reverse;
									}
								</style>
								<div class="wrap-price-checkout">
									<?php
									$price = WC()->cart->get_product_price($_product);
									echo apply_filters('woocommerce_cart_item_price', $price, $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>
								</div>
								<div class="quantity">
									<?php echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $cart_item['quantity']) . '</strong>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>
								</div>
							</div>

							<div class="total product-total">
								<?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</div>
						</div>
					</div>
				</div>
				<?php echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
	<?php
		}
	}
	do_action('woocommerce_review_order_after_cart_contents');
	?>
	<div class="wrap-footer">
		<div class="py-5">
			<table>
				<tr>
					<td>
						<strong>
							<?= _e('Tạm tính', 'canhcamtheme') ?>
						</strong>
					</td>
					<td>
						<?php
						echo wc_cart_totals_subtotal_html();
						?>
					</td>
				</tr>
				<?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

					<?php do_action('woocommerce_review_order_before_shipping'); ?>

					<?php wc_cart_totals_shipping_html(); ?>

					<?php do_action('woocommerce_review_order_after_shipping'); ?>

				<?php endif; ?>

				<?php foreach (WC()->cart->get_fees() as $fee) : ?>
					<tr class="fee">
						<td>
							<strong>
								<?php echo esc_html($fee->name); ?>
							</strong>
						</td>
						<td><?php wc_cart_totals_fee_html($fee); ?></td>
					</tr>
				<?php endforeach; ?>
				<?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
					<?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
						<?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						?>
							<tr class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
								<td>
									<strong> <?php echo esc_html($tax->label); ?></strong>
								</td>
								<td><?php echo wp_kses_post($tax->formatted_amount); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr class="tax-total">
							<td>
								<strong>
									<?php echo esc_html(WC()->countries->tax_or_vat()); ?></strong>
							</td>
							<td><?php wc_cart_totals_taxes_total_html(); ?></td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				<?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
					<tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
						<td>
							<strong>
								Coupon:
							</strong>
						</td>
						<td><?php echo $coupon->get_code(); ?></td>
					</tr>
				<?php endforeach; ?>
				<?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
					<tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
						<td>
							<strong>
								<?php _e('Giảm giá', 'canhcamtheme') ?>
							</strong>
						</td>
						<td class="text-primary-green"><?php echo $coupon->amount ?>%
							<?php wc_cart_totals_coupon_html($coupon);
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
		<div class="total">
			<table>
				<tr>
					<td>
						<strong><?= _e('Tổng cộng', 'canhcamtheme') ?></strong>
					</td>
					<td>
						<?php wc_cart_totals_order_total_html(); ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<style>
	.woocommerce-shipping-totals.shipping th {
		font-size: .72917rem;
		text-align: left;
	}
</style>