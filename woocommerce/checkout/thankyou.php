<?php

/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 *
 * @var WC_Order $order
 */

defined('ABSPATH') || exit;
?>
<?php
$payment_status = isset($_GET['vpc_Message']) ? sanitize_text_field($_GET['vpc_Message']) : ''; // Sanitize input
// var_dump($payment_status); // Return Canceled

?>
<div class="woocommerce-order">
	<?php
	if ($order) :
		do_action('woocommerce_before_thankyou', $order->get_id());
	?>
		<?php if ($order->has_status('failed') || $payment_status === 'Canceled') : // Updated condition 
		?>
			<section class="section-checkout-success section-py">
				<div class="container">
					<div class="wrap-progress-steps">
						<div class="progress-steps-container">
							<div class="progress-steps" id="progressSteps1">
								<div class="step s1 completed" data-step="1">
									<div class="circle">1</div>
									<div class="label"><?= _e('Đặt hàng thành công', 'canhcamtheme') ?></div>
								</div>
								<div class="connector c1"></div>
								<div class="step s2 active" data-step="2">
									<div class="circle">2</div>
									<div class="label">
										<?= _e('Thanh toán thất bại', 'canhcamtheme') ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="section-wrap-box-white p-10 ">
						<div class="row gap-y-5">
							<div class="col md:w-6/12">
								<div class="img img-ratio ratio:pt-[425px_521px]">
									<img src="<?php bloginfo('template_directory') ?>/img/failed-purchase.jpg" alt="">
								</div>
							</div>
							<div class="col md:w-6/12 flex flex-col justify-center items-start">
								<h1 class="title-48px" style="color: #FF0000;">
									<?= _e('Thanh toán thất bại!', 'canhcamtheme') ?>
								</h1>
								<div class="mb-5">
									<?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
								</div>
								<p class="mb-5"><?= _e('Mã Đơn hàng của bạn là:', 'canhcamtheme') ?> #<?php echo $order->get_order_number(); ?>
									<?= get_field('checkout_fail', 'options') ?>
							</div>
						</div>
					</div>
				</div>
			</section>
			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?></p>
			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="button pay"><?php esc_html_e('Pay', 'woocommerce'); ?></a>
				<?php if (is_user_logged_in()) : ?>
					<a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="button pay"><?php esc_html_e('My account', 'woocommerce'); ?></a>
				<?php endif; ?>
			</p>
		<?php else : ?>
			<section class="section-checkout-success section-py">
				<div class="container">
					<div class="wrap-progress-steps">
						<div class="progress-steps-container">
							<div class="progress-steps" id="progressSteps1">
								<div class="step s1 completed" data-step="1">
									<div class="circle">1</div>
									<div class="label"><?= _e('Đặt hàng thành công', 'canhcamtheme') ?></div>
								</div>
								<div class="connector c1"></div>
								<div class="step s2 active" data-step="2">
									<div class="circle">2</div>
									<div class="label">
										<?php
										$payment_method_code = $order->get_payment_method();
										?>
										<?php if ($payment_method_code == 'onepay'): ?>
											<?= _e('Thanh toán thành công', 'canhcamtheme') ?>
										<?php else: ?>
											<?php echo wp_kses_post($order->get_payment_method_title()); ?>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="section-wrap-box-white p-10 ">
						<div class="row gap-y-5">
							<div class="col md:w-6/12">
								<div class="img img-ratio ratio:pt-[425px_521px]">
									<img src="<?php bloginfo('template_directory') ?>/img/successful-purchase.webp" alt="">
								</div>
							</div>
							<div class="col md:w-6/12 flex flex-col justify-center items-start">
								<h1 class="title-48px text-primary-green mb-6">
									<?= _e('Thank you!', 'canhcamtheme') ?>
								</h1>
								<div class="format-content mb-5">
									<p class=""><?= _e('Cảm ơn quý khách đã tin tưởng và mua sắm tại website của chúng tôi.', 'canhcamtheme') ?></p>
									<p class=""><?= _e('Mã đơn hàng của quý khách là:', 'canhcamtheme') ?><strong> #<?php echo $order->get_order_number(); ?></strong> <?= _e('Hiện tại đơn hàng của bạn đang được xử lý.', 'canhcamtheme') ?></p>
								</div>
								<div class="mb-5">
									<?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
								</div>
								<div class="format-content">
									<p class="mb-5"><?= _e('Hiện tại đơn hàng của bạn đang được xử lý. Thông tin chi tiết đơn hàng sẽ được gửi đến hộp thư của bạn:', 'canhcamtheme') ?> <?php echo $order->get_billing_email(); ?></p>
								</div>
								<?= get_field('checkout_success', 'options') ?>
							</div>
						</div>
					</div>
				</div>
			</section>

			<?php // wc_get_template('checkout/order-received.php', array('order' => $order));
			?>

			<!-- <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

				<li class="woocommerce-order-overview__order order">
					<?php esc_html_e('Order number:', 'woocommerce'); ?>
					<strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?></strong>
				</li>

				<li class="woocommerce-order-overview__date date">
					<?php esc_html_e('Date:', 'woocommerce'); ?>
					<strong><?php echo wc_format_datetime($order->get_date_created()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?></strong>
				</li>

				<?php if (is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email()) : ?>
					<li class="woocommerce-order-overview__email email">
						<?php esc_html_e('Email:', 'woocommerce'); ?>
						<strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?></strong>
					</li>
				<?php endif; ?>

				<li class="woocommerce-order-overview__total total">
					<?php esc_html_e('Total:', 'woocommerce'); ?>
					<strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?></strong>
				</li>

				<?php if ($order->get_payment_method_title()) : ?>
					<li class="woocommerce-order-overview__payment-method method">
						<?php esc_html_e('Payment method:', 'woocommerce'); ?>
						<strong><?php echo wp_kses_post($order->get_payment_method_title()); ?></strong>
					</li>
				<?php endif; ?>

			</ul> -->

		<?php endif; ?>
		<div class="hidden">
			<?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id());
			?>
			<?php do_action('woocommerce_thankyou', $order->get_id());
			?>
		</div>

	<?php else : ?>
		<?php wc_get_template('checkout/order-received.php', array('order' => false)); ?>

	<?php endif; ?>
	<style>
		.woocommerce-notice.woocommerce-thankyou-order-failed,
		.woocommerce-notice.woocommerce-thankyou-order-failed-actions {
			display: none;
		}

		.wrap-box-white {
			box-shadow: 2px 2px 10px 1px rgba(0, 0, 0, 0.1);
		}

		.img img {
			object-fit: contain;
		}
	</style>

</div>