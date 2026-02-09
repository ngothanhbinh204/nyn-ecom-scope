<?php

/**
 * Checkout coupon form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-coupon.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

if (!wc_coupons_enabled()) { // @codingStandardsIgnoreLine.
	return;
}

?>
<form class="checkout_coupon woocommerce-form-coupon ajax-coupon-form" method="post" style="display:none">

	<!-- <p><?php esc_html_e('If you have a coupon code, please apply it below.', 'woocommerce'); ?></p> -->

	<p class="form-row form-row-first">
		<!-- <label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label> -->
	</p>
	<div class="wrap-form-coupon">
		<p class="w-full flex-1">
			<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" id="coupon_code" value="" />
		</p>
		<p>
			<button type="button" class="button ajax-apply-coupon<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"><?php esc_html_e('Áp dụng', 'canhcamtheme'); ?></button>
		</p>
	</div>

	<!-- Message container for AJAX responses -->
	<div class="coupon-messages" style="margin-top: 15px;"></div>

	<div class="note text-neutral-500 text-sm mt-5">
		<?php _e('Sau khi áp dụng mã giảm giá có thể không dùng được trong 15 phút.', 'canhcamtheme') ?>
	</div>
	<div class="text-sm mt-5">
		<?php _e('Trong quá trình thanh toán, sẽ tạm khóa mã giảm của quý khách hàng để đảm bảo phiên giao dịch được ổn định. Mã giảm giá sẽ được mở lại ngay khi phiên giao dịch kết thúc', 'canhcamtheme') ?>
	</div>
	<div class="clear"></div>
</form>