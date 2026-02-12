<?php
remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
add_action('custom_woocommerce_payment', 'woocommerce_checkout_payment', 20);

remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
add_action('woocommerce_checkout_coupon_form', 'woocommerce_checkout_coupon_form_custom');
function woocommerce_checkout_coupon_form_custom()
{
	echo '<div>';
	wc_get_template(
		'checkout/form-coupon.php',
		array(
			'checkout' => WC()->checkout(),
		)
	);
	echo '</div>';
}


/**
 * Woo - remove required field in checkout
 */
add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields', 1);
function custom_override_checkout_fields($fields)
{
	$fields['billing']['billing_email']['required'] = false;
	$fields['billing']['billing_phone']['priority'] = 11;
	// $fields['billing']['billing_phone']['validate'] = array('phone');

	return $fields;
}

add_filter('woocommerce_checkout_fields', 'misha_no_phone_validation');

function misha_no_phone_validation($fields)
{
	// billing phone
	unset($fields['billing']['billing_phone']['validate']);
	return $fields;
}


// Move validation function outside to avoid nested function
add_action('woocommerce_after_checkout_validation', 'validate_billing_phone_field', 10, 2);
function validate_billing_phone_field($fields, $errors)
{
	if (!empty($fields['billing_phone'])) {
		$result = preg_match('/^(0|\+840?)(\s|\.)?((3[2-9])|(5[689])|(7[06-9])|(8[1-689])|(9[0-46-9]))(\d)(\s|\.)?(\d{3})(\s|\.)?(\d{3})$/', $fields['billing_phone']);
		if (!$result) {
			$errors->add('validation', __('Số điện thoại không hợp lệ. Vui lòng nhập số điện thoại di động gồm 10 chữ số, bắt đầu bằng 0 hoặc +84.', 'canhcamtheme'));
		}
	}
}

add_action('wp_footer', function () {
	// we need it only on our checkout page
	if (!is_checkout()) {
		return;
	}
?>
	<script>
		const error_message = "<?= _e('Số điện thoại không hợp lệ. Vui lòng nhập số điện thoại di động gồm 10 chữ số, bắt đầu bằng 0 hoặc +84.', 'canhcamtheme') ?>";
		jQuery(function($) {
			$('body').on('blur change', '#billing_phone', function() {
				const wrapper = $(this).closest('.form-row');
				const phone_number = $(this).val();
				console.log("🚀 ~ $ ~ phone_number:", phone_number)
				console.log("🚀 ~ $ ~ phone_number:", phone_number.length)

				if (phone_number.length === 0) {
					wrapper.removeClass('woocommerce-invalid invalid woocommerce-validated valid');
					return;
				}

				const phone_regex = /^(0|\+840?)(\s|\.)?((3[2-9])|(5[689])|(7[06-9])|(8[1-689])|(9[0-46-9]))(\d)(\s|\.)?(\d{3})(\s|\.)?(\d{3})$/;
				console.log("🚀 ~ $ ~ phone_regex:", phone_regex.test(phone_number))
				if (!phone_regex.test(phone_number)) { // check if phone is invalid
					wrapper.removeClass('woocommerce-validated valid').addClass('woocommerce-invalid invalid'); // error
					if (wrapper.find('.error').length === 0) {
						wrapper.append('<span class="error">' + error_message + '</span>');
					}
				} else {
					wrapper.removeClass('woocommerce-invalid invalid').addClass('woocommerce-validated valid'); // success
					wrapper.find('.error').remove();
				}
			});
			jQuery(document.body).on(
				"init_checkout payment_method_selected update_checkout updated_checkout checkout_error applied_coupon_in_checkout removed_coupon_in_checkout adding_to_cart added_to_cart removed_from_cart wc_cart_button_updated cart_page_refreshed cart_totals_refreshed wc_fragments_loaded init_add_payment_method wc_cart_emptied updated_wc_div updated_cart_totals country_to_state_changed updated_shipping_method applied_coupon removed_coupon",
				function(e) {
					console.log(e.type)
					if (e.type === 'checkout_error') {
						$('body').find('#billing_phone').trigger('change');
						$('body').find('#billing_phone').trigger('blur');
					}
				}
			)
		});
	</script>
<?php
});
/**
 * @note Woo - Hidden Product Image in email
 */
add_filter('woocommerce_email_order_items_args', 'remove_product_image_from_order_email');

function remove_product_image_from_order_email($args)
{
	$args['show_image'] = false;
	return $args;
}




/**
 * Woo - Remove order notes field
 */
add_filter('woocommerce_enable_order_notes_field', '__return_false');




add_action('custom_button_checkout', 'woo_add_button_cart_checkout', 1);
function woo_add_button_cart_checkout()
{
?>
	<script>
		jQuery(document).ready(function($) {
			$(document).on("click", ".button-checkout-now", function(event) {
				event.preventDefault();
				$('#place_order').trigger('click');
				return false;
			});
		})
	</script>
	<?php
	echo '<a class="btn btn-primary ml-auto green ui-button large primary ink button-checkout-now">' . __('TIẾN HÀNH THANH TOÁN', 'canhcamtheme') . '</a>';
}


/**
 * Woo - Add Banking
 */

add_filter('woocommerce_bacs_accounts', '__return_false');

add_action('woocommerce_email_before_order_table', 'devvn_email_instructions', 10, 3);
function devvn_email_instructions($order, $sent_to_admin, $plain_text = false)
{

	if (!$sent_to_admin && 'bacs' === $order->get_payment_method() && $order->has_status('on-hold')) {
		devvn_bank_details($order->get_id());
	}
}

add_action('woocommerce_thankyou_bacs', 'devvn_thankyou_page');
function devvn_thankyou_page($order_id)
{
	devvn_bank_details($order_id);
}

function devvn_bank_details($order_id = '')
{
	$bacs_accounts = get_option('woocommerce_bacs_accounts');
	if (!empty($bacs_accounts)) {
		ob_start();
		echo '<div class="format-content mb-5">';
		echo '<p class=""><p><strong>' . __('Vui lòng chuyển khoản theo thông tin bên dưới để xác nhận đơn hàng. Đơn hàng sẽ được xử lý sau khi thanh toán thành công.', 'canhcamtheme') . '</strong></p>';
		echo '</div>';
		echo '<table style=" border: 1px solid #ddd; border-collapse: collapse; width: 100%; margin-bottom:1.2rem;">';
	?>
		<tr>
			<td colspan="2" style="border: 1px solid #eaeaea;padding: 6px 10px;"><strong><?php _e('Thông tin chuyển khoản', 'canhcamtheme') ?></strong></td>
		</tr>
		<?php
		foreach ($bacs_accounts as $bacs_account) {
			$bacs_account = (object) $bacs_account;
			$account_name = $bacs_account->account_name;
			$bank_name = $bacs_account->bank_name;
			$stk = $bacs_account->account_number;
			$icon = $bacs_account->iban;
			$domain = get_site_url();
		?>
			<tr>
				<td style="border: 1px solid #eaeaea;padding: 6px 10px;">
					<strong><?= _e('STK', 'canhcamtheme') ?>:</strong> <?php echo $stk; ?><br>
					<strong><?= _e('Chủ tài khoản', 'canhcamtheme') ?>:</strong> <?php echo $account_name; ?><br>
					<strong><?= _e('Chi nhánh', 'canhcamtheme') ?>:</strong> <?php echo $bank_name; ?><br>
					<strong><?= _e('Nội dung chuyển khoản', 'canhcamtheme') ?>:</strong> W<?php echo $order_id; ?>
				</td>
			</tr>
<?php
		}
		echo '</table>';
		echo ob_get_clean();;
	}
}

/**
 * Woo - Add function banking
 */


// add_action('woocommerce_email_before_order_table', 'devvn_woocommerce_email_before_order_table', 5);
// add_action('woocommerce_thankyou_bacs', 'devvn_woocommerce_email_before_order_table', 5);


/**
 * Display bank details on the "View Order" page for BACS payments.
 *
 * @param WC_Order $order Order object.
 */
function cc_display_bank_details_on_view_order($order)
{
	// Check if the order object is valid and if the payment method is BACS
	if (!$order || $order->get_payment_method() !== 'bacs') {
		return;
	}
	// Output the bank details using the existing function
	echo '<div style="margin-top: 20px;">';
	devvn_bank_details($order->get_id());
	echo '</div>';
}
add_action('woocommerce_order_details_after_order_table', 'cc_display_bank_details_on_view_order', 20);

// AJAX Coupon Handlers for Checkout
add_action('wp_ajax_apply_coupon_ajax', 'handle_apply_coupon_ajax');
add_action('wp_ajax_nopriv_apply_coupon_ajax', 'handle_apply_coupon_ajax');

function handle_apply_coupon_ajax()
{
	// Verify nonce for security (optional but recommended)
	if (isset($_POST['security']) && !wp_verify_nonce($_POST['security'], 'apply_coupon_nonce')) {
		wp_send_json_error(array('message' => 'Security check failed.'));
		return;
	}

	// Check if WooCommerce is active and cart exists
	if (!class_exists('WooCommerce') || !WC()->cart) {
		wp_send_json_error(array('message' => 'WooCommerce not available.'));
		return;
	}

	$coupon_code = sanitize_text_field($_POST['coupon_code']);

	if (empty($coupon_code)) {
		wp_send_json_error(array('message' => 'Vui lòng nhập mã giảm giá.'));
		return;
	}

	// Check if coupon is already applied
	$applied_coupons = WC()->cart->get_applied_coupons();
	if (in_array($coupon_code, $applied_coupons)) {
		wp_send_json_error(array('message' => 'Mã giảm giá này đã được áp dụng.'));
		return;
	}

	// Try to apply the coupon
	$result = WC()->cart->apply_coupon($coupon_code);

	if ($result) {
		// Calculate totals after applying coupon
		WC()->cart->calculate_totals();

		wp_send_json_success(array(
			'message' => 'Mã giảm giá đã được áp dụng thành công!',
			'cart_total' => WC()->cart->get_total(),
			'applied_coupons' => WC()->cart->get_applied_coupons()
		));
	} else {
		// Get the last error message from WooCommerce notices
		$notices = wc_get_notices('error');
		$error_message = 'Mã giảm giá không hợp lệ hoặc đã hết hạn.';

		if (!empty($notices)) {
			$error_message = $notices[0]['notice'];
			wc_clear_notices(); // Clear the notices so they don't show elsewhere
		}

		wp_send_json_error(array('message' => $error_message));
	}
}

add_action('wp_ajax_remove_coupon_ajax', 'handle_remove_coupon_ajax');
add_action('wp_ajax_nopriv_remove_coupon_ajax', 'handle_remove_coupon_ajax');

function handle_remove_coupon_ajax()
{
	// Verify nonce for security (optional but recommended)
	if (isset($_POST['security']) && !wp_verify_nonce($_POST['security'], 'remove_coupon_nonce')) {
		wp_send_json_error(array('message' => 'Security check failed.'));
		return;
	}

	// Check if WooCommerce is active and cart exists
	if (!class_exists('WooCommerce') || !WC()->cart) {
		wp_send_json_error(array('message' => 'WooCommerce not available.'));
		return;
	}

	$coupon_code = sanitize_text_field($_POST['coupon_code']);

	if (empty($coupon_code)) {
		wp_send_json_error(array('message' => 'Mã giảm giá không hợp lệ.'));
		return;
	}

	// Try to remove the coupon
	$result = WC()->cart->remove_coupon($coupon_code);

	if ($result) {
		// Calculate totals after removing coupon
		WC()->cart->calculate_totals();

		wp_send_json_success(array(
			'message' => 'Mã giảm giá đã được gỡ bỏ.',
			'cart_total' => WC()->cart->get_total(),
			'applied_coupons' => WC()->cart->get_applied_coupons()
		));
	} else {
		wp_send_json_error(array('message' => 'Không thể gỡ bỏ mã giảm giá.'));
	}
}
