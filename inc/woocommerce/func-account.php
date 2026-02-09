<?php

/**
 * WooCommerce Account Functionality
 *
 * This file contains custom WooCommerce account-related functions including
 * login forms, registration forms, user profile management, and account customizations.
 *
 * @package CanhCamTheme
 * @subpackage WooCommerce
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Constants for form validation and configuration
 */
define('CANHCAM_PHONE_MAX_LENGTH', 10);
define('CANHCAM_PHONE_REGEX', '/^(0|\+84)(\s|\.)?((3[2-9])|(5[689])|(7[06-9])|(8[1-689])|(9[0-46-9]))(\d)(\s|\.)?(\d{3})(\s|\.)?(\d{3})$/');

// =============================================================================
// LOGIN FORM FUNCTIONALITY
// =============================================================================

/**
 * Display separate WooCommerce login form via shortcode
 *
 * Creates a standalone login form that can be embedded anywhere using shortcode.
 * Redirects to my account page after successful login.
 *
 * @since 1.0.0
 * @return string HTML output of the login form or logged-in message
 */
function canhcam_separate_login_form()
{
	if (is_user_logged_in()) {
		return '<p>' . esc_html__('You are already logged in', 'canhcamtheme') . '</p>';
	}

	ob_start();
	do_action('woocommerce_before_customer_login_form');
	woocommerce_login_form(array('redirect' => wc_get_page_permalink('myaccount')));
	return ob_get_clean();
}
add_shortcode('wc_login_form_bbloomer', 'canhcam_separate_login_form');

/**
 * Add registration link to login form
 *
 * Appends a registration link at the end of the WooCommerce login form
 * to allow users to navigate to the registration page.
 *
 * @since 1.0.0
 * @return void
 */
function canhcam_add_registration_link()
{
	$register_url = get_page_link_by_template('template-woocommerce/page-register.php');
	if ($register_url) {
		echo '<p>' . __('Bạn chưa có tài khoản? ') . '<a class="text-link" href="' . esc_url($register_url) . '">' . esc_html__('Đăng ký', 'canhcamtheme') . '</a></p>';
	}
}
add_action('woocommerce_login_form_end', 'canhcam_add_registration_link', 1);

// =============================================================================
// REGISTRATION FORM FUNCTIONALITY
// =============================================================================

/**
 * Generate custom WooCommerce registration form
 *
 * Creates a comprehensive registration form with additional custom fields
 * including Vietnamese-specific fields like full name, gender, phone, etc.
 *
 * @since 1.0.0
 * @return string HTML output of the registration form or logged-in message
 */
function canhcam_custom_registration_form()
{
	if (is_user_logged_in()) {
		return '<p>' . esc_html__('Bạn đã đăng nhập thành công.', 'canhcamtheme') . '</p>';
	}

	ob_start();
?>
	<form method="post" class="custom_registration_form">
		<?php echo canhcam_render_registration_field('ten_dang_nhap', 'text', __('Tên đăng nhập', 'canhcamtheme'), true); ?>
		<?php echo canhcam_render_registration_field('ho_ten', 'text', __('Họ và tên', 'canhcamtheme'), true); ?>
		<?php echo canhcam_render_registration_field('email', 'email', __('Địa chỉ Email', 'canhcamtheme'), true); ?>
		<?php echo canhcam_render_phone_field(); ?>
		<?php echo canhcam_render_registration_field('ngay_sinh', 'date', __('Ngày sinh', 'canhcamtheme'), true); ?>
		<?php echo canhcam_render_registration_field('password', 'password', __('Mật khẩu', 'canhcamtheme'), true); ?>
		<?php echo canhcam_render_registration_field('repassword', 'password', __('Xác nhận mật khẩu', 'canhcamtheme'), true); ?>
		<p class="woocommerce-form-row form-row">
			<?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
			<button type="submit" class="woocommerce-Button button btn w-full justify-center btn-primary" name="register" value="<?php esc_attr_e('Đăng ký', 'canhcamtheme'); ?>">
				<?php esc_html_e('Đăng ký', 'canhcamtheme'); ?>
			</button>
		</p>
	</form>
<?php
	return ob_get_clean();
}
add_shortcode('custom_registration_form', 'canhcam_custom_registration_form');

/**
 * Helper function to render registration form fields
 *
 * @since 1.0.0
 * @param string $field_name The field name
 * @param string $field_type The input type (text, email, password, etc.)
 * @param string $label The field label
 * @param bool $required Whether the field is required
 * @return string HTML output for the field
 */
function canhcam_render_registration_field($field_name, $field_type, $label, $required = false)
{
	$field_id = 'reg_' . $field_name;
	$required_attr = $required ? 'required' : '';
	$required_mark = $required ? ' <span class="required">*</span>' : '';
	$current_value = !empty($_POST[$field_name]) ? esc_attr(wp_unslash($_POST[$field_name])) : '';

	// Special handling for phone field
	$extra_attrs = '';
	if ($field_name === 'dien_thoai') {
		$extra_attrs = 'maxlength="' . CANHCAM_PHONE_MAX_LENGTH . '"';
	}

	$validation_message = canhcam_get_field_validation_message($field_name);

	ob_start();
?>
	<div class="form-row form-row-wide">
		<label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($label); ?><?php echo $required_mark; ?></label>
		<input <?php echo $required_attr; ?>
			type="<?php echo esc_attr($field_type); ?>"
			class="input-text"
			name="<?php echo esc_attr($field_name); ?>"
			id="<?php echo esc_attr($field_id); ?>"
			value="<?php echo $current_value; ?>"
			<?php echo $extra_attrs; ?>
			oninput="this.setCustomValidity('')"
			oninvalid="this.setCustomValidity('<?php echo esc_js($validation_message); ?>')" />
	</div>
<?php
	return ob_get_clean();
}



/**
 * Render phone field with specific validation
 *
 * @since 1.0.0
 * @return string HTML output for phone field
 */
function canhcam_render_phone_field()
{
	$current_value = !empty($_POST['dien_thoai']) ? esc_attr(wp_unslash($_POST['dien_thoai'])) : '';

	ob_start();
?>
	<p class="form-row form-row-wide">
		<label for="reg_dien_thoai"><?php esc_html_e('Số điện thoại', 'canhcamtheme'); ?> <span class="required">*</span></label>
		<input required
			maxlength="<?php echo CANHCAM_PHONE_MAX_LENGTH; ?>"
			type="tel"
			class="input-text"
			name="dien_thoai"
			id="reg_dien_thoai"
			value="<?php echo $current_value; ?>"
			oninput="this.setCustomValidity('')"
			oninvalid="this.setCustomValidity('<?php echo esc_js(__('Số điện thoại là bắt buộc', 'canhcamtheme')); ?>')" />
	</p>
<?php
	return ob_get_clean();
}

/**
 * Get validation message for specific field
 *
 * @since 1.0.0
 * @param string $field_name The field name
 * @return string Validation message
 */
function canhcam_get_field_validation_message($field_name)
{
	$messages = array(
		'ten_dang_nhap' => __('Tên đăng nhập là bắt buộc', 'canhcamtheme'),
		'ho_ten' => __('Họ tên là bắt buộc', 'canhcamtheme'),
		'email' => __('Email là bắt buộc', 'canhcamtheme'),
		'password' => __('Mật khẩu là bắt buộc', 'canhcamtheme'),
		'repassword' => __('Xác nhận mật khẩu là bắt buộc', 'canhcamtheme'),
		'ngay_sinh' => __('Ngày sinh là bắt buộc', 'canhcamtheme'),
	);

	return isset($messages[$field_name]) ? $messages[$field_name] : __('Trường này là bắt buộc', 'canhcamtheme');
}
/**
 * Save custom registration data when customer is created
 *
 * Processes and saves all custom registration fields to user meta
 * and updates the WordPress user login field safely.
 *
 * @since 1.0.0
 * @param int $customer_id The newly created customer ID
 * @return void
 * @throws Exception If user update fails
 */
function canhcam_save_registration_data($customer_id)
{
	// Validate customer ID
	if (!$customer_id || !is_numeric($customer_id)) {
		error_log('CanhCam Registration: Invalid customer ID provided');
		return;
	}

	// Sanitize and collect form data
	$form_data = canhcam_sanitize_registration_data($_POST);
	$form_data['customer_id'] = $customer_id;

	// Update WordPress user login safely
	if (!empty($form_data['ten_dang_nhap'])) {
		$update_result = wp_update_user(array(
			'ID' => $customer_id,
			'user_login' => $form_data['ten_dang_nhap']
		));

		if (is_wp_error($update_result)) {
			error_log('CanhCam Registration: Failed to update user login - ' . $update_result->get_error_message());
		} else {
			update_user_meta($customer_id, 'ten_dang_nhap', $form_data['ten_dang_nhap']);
		}
	}

	// Save user profile data
	canhcam_save_user_profile_data($customer_id, $form_data);

	// Initialize default settings
	canhcam_initialize_user_defaults($customer_id);

	// Add address to WooCommerce Address Book if available
	if (function_exists('add_address_to_address_book')) {
		add_address_to_address_book($customer_id, $form_data);
	}

	/**
	 * Action hook after custom registration data is saved
	 *
	 * @since 1.0.0
	 * @param int $customer_id The customer ID
	 * @param array $form_data The sanitized form data
	 */
	do_action('canhcam_after_registration_save', $customer_id, $form_data);
}
add_action('woocommerce_created_customer', 'canhcam_save_registration_data');

/**
 * Sanitize registration form data
 *
 * @since 1.0.0
 * @param array $post_data Raw POST data
 * @return array Sanitized form data
 */
function canhcam_sanitize_registration_data($post_data)
{
	return array(
		'ten_dang_nhap' => isset($post_data['ten_dang_nhap']) ? sanitize_user($post_data['ten_dang_nhap']) : '',
		'email' => isset($post_data['email']) ? sanitize_email($post_data['email']) : '',
		'ho_ten' => isset($post_data['ho_ten']) ? sanitize_text_field($post_data['ho_ten']) : '',
		'dien_thoai' => isset($post_data['dien_thoai']) ? sanitize_text_field($post_data['dien_thoai']) : '',
		'dia_chi' => isset($post_data['dia_chi']) ? sanitize_textarea_field($post_data['dia_chi']) : '',
		'ngay_sinh' => isset($post_data['ngay_sinh']) ? sanitize_text_field($post_data['ngay_sinh']) : '',
		'ma_so_thue' => isset($post_data['ma_so_thue']) ? sanitize_text_field($post_data['ma_so_thue']) : '',
	);
}



/**
 * Save user profile data to user meta
 *
 * @since 1.0.0
 * @param int $customer_id The customer ID
 * @param array $form_data Sanitized form data
 * @return void
 */
function canhcam_save_user_profile_data($customer_id, $form_data)
{
	$meta_fields = array(
		'ho_ten' => array('ho_ten', 'first_name'), // Save to both custom and WP standard field
		'dien_thoai' => array('user_phone'),
		'ngay_sinh' => array('ngay_sinh'),
		'dia_chi' => array('dia_chi'),
		'ma_so_thue' => array('ma_so_thue'),
	);

	foreach ($meta_fields as $form_field => $meta_keys) {
		if (!empty($form_data[$form_field])) {
			foreach ($meta_keys as $meta_key) {
				update_user_meta($customer_id, $meta_key, $form_data[$form_field]);
			}
		}
	}
}

/**
 * Initialize default user settings
 *
 * @since 1.0.0
 * @param int $customer_id The customer ID
 * @return void
 */
function canhcam_initialize_user_defaults($customer_id)
{
	// Set default email subscription preference
	update_user_meta($customer_id, 'nhan_email', 0);

	// Set default account status (0 = inactive, 1 = active)
	update_user_meta($customer_id, 'trang_thai', 0);
}
// =============================================================================
// REGISTRATION VALIDATION
// =============================================================================

/**
 * Validate custom registration form fields
 *
 * Performs comprehensive validation of all custom registration fields
 * including Vietnamese phone number validation and password matching.
 *
 * @since 1.0.0
 * @param WP_Error $errors Existing validation errors
 * @param string $username The submitted username
 * @param string $email The submitted email
 * @return WP_Error Updated errors object
 */
function canhcam_validate_registration_fields($errors, $username, $email)
{
	// Validate required fields
	$required_fields = array(
		'ten_dang_nhap' => __('Tên đăng nhập là bắt buộc!', 'canhcamtheme'),
		'ho_ten' => __('Họ tên là bắt buộc!', 'canhcamtheme'),
		'email' => __('Email là bắt buộc!', 'canhcamtheme'),
		'dien_thoai' => __('Số điện thoại là bắt buộc!', 'canhcamtheme'),
		'password' => __('Mật khẩu là bắt buộc!', 'canhcamtheme'),
		'repassword' => __('Xác nhận mật khẩu là bắt buộc!', 'canhcamtheme'),
		'ngay_sinh' => __('Ngày sinh là bắt buộc!', 'canhcamtheme'),
	);

	foreach ($required_fields as $field => $message) {
		if (empty($_POST[$field])) {
			$errors->add($field . '_error', $message);
		}
	}

	// Validate phone number
	if (!empty($_POST['dien_thoai'])) {
		$phone_errors = canhcam_validate_phone_number($_POST['dien_thoai']);
		foreach ($phone_errors as $error) {
			$errors->add('dien_thoai_error', $error);
		}
	}

	// Validate password confirmation
	if (!empty($_POST['password']) && !empty($_POST['repassword'])) {
		if ($_POST['password'] !== $_POST['repassword']) {
			$errors->add('password_mismatch', __('Mật khẩu không khớp!', 'canhcamtheme'));
		}
	}

	// Validate username uniqueness
	if (!empty($_POST['ten_dang_nhap'])) {
		$username_errors = canhcam_validate_username($_POST['ten_dang_nhap']);
		foreach ($username_errors as $error) {
			$errors->add('ten_dang_nhap_error', $error);
		}
	}

	// Validate email format and uniqueness (WooCommerce handles basic email validation)
	if (!empty($_POST['email']) && !is_email($_POST['email'])) {
		$errors->add('email_error', __('Vui lòng nhập địa chỉ email hợp lệ.', 'canhcamtheme'));
	}

	// Validate birth date
	if (!empty($_POST['ngay_sinh'])) {
		$date_errors = canhcam_validate_birth_date($_POST['ngay_sinh']);
		foreach ($date_errors as $error) {
			$errors->add('ngay_sinh_error', $error);
		}
	}

	return $errors;
}
add_filter('woocommerce_registration_errors', 'canhcam_validate_registration_fields', 10, 3);

/**
 * Validate Vietnamese phone number
 *
 * @since 1.0.0
 * @param string $phone The phone number to validate
 * @return array Array of error messages (empty if valid)
 */
function canhcam_validate_phone_number($phone)
{
	$errors = array();

	if (strlen($phone) > CANHCAM_PHONE_MAX_LENGTH) {
		$errors[] = sprintf(
			__('Số điện thoại không được vượt quá %d ký tự!', 'canhcamtheme'),
			CANHCAM_PHONE_MAX_LENGTH
		);
	}

	if (!preg_match(CANHCAM_PHONE_REGEX, $phone)) {
		$errors[] = __('Vui lòng kiểm tra lại số điện thoại. Hãy nhập số điện thoại hợp lệ!', 'canhcamtheme');
	}

	return $errors;
}

/**
 * Validate username
 *
 * @since 1.0.0
 * @param string $username The username to validate
 * @return array Array of error messages (empty if valid)
 */
function canhcam_validate_username($username)
{
	$errors = array();

	// Check if username already exists
	if (username_exists($username)) {
		$errors[] = __('Tên đăng nhập này đã được sử dụng. Vui lòng chọn tên khác.', 'canhcamtheme');
	}

	// Check username length
	if (strlen($username) < 3) {
		$errors[] = __('Tên đăng nhập phải có ít nhất 3 ký tự.', 'canhcamtheme');
	}

	// Check for invalid characters
	if (!validate_username($username)) {
		$errors[] = __('Tên đăng nhập chứa ký tự không hợp lệ.', 'canhcamtheme');
	}

	return $errors;
}

/**
 * Validate birth date
 *
 * @since 1.0.0
 * @param string $date The birth date to validate
 * @return array Array of error messages (empty if valid)
 */
function canhcam_validate_birth_date($date)
{
	$errors = array();

	// Check date format
	$parsed_date = DateTime::createFromFormat('Y-m-d', $date);
	if (!$parsed_date || $parsed_date->format('Y-m-d') !== $date) {
		$errors[] = __('Vui lòng nhập ngày sinh hợp lệ.', 'canhcamtheme');
		return $errors;
	}

	// Check if date is not in the future
	$today = new DateTime();
	if ($parsed_date > $today) {
		$errors[] = __('Ngày sinh không thể là ngày trong tương lai.', 'canhcamtheme');
	}

	// Check minimum age (optional - can be configured)
	$min_age = apply_filters('canhcam_minimum_registration_age', 13);
	$min_date = $today->sub(new DateInterval('P' . $min_age . 'Y'));
	if ($parsed_date > $min_date) {
		$errors[] = sprintf(
			__('Bạn phải ít nhất %d tuổi để đăng ký.', 'canhcamtheme'),
			$min_age
		);
	}

	return $errors;
}

// =============================================================================
// ADDRESS BOOK INTEGRATION
// =============================================================================

/**
 * Add user address to WooCommerce Address Book during registration
 *
 * Integrates with WooCommerce Address Book plugin to automatically
 * populate billing address from registration data.
 *
 * @since 1.0.0
 * @param int $customer_id The customer ID
 * @param array $form_data The form data containing address information
 * @return void
 */
function canhcam_add_address_to_address_book($customer_id, $form_data)
{
	// Check if WooCommerce Address Book is active
	if (!class_exists('WC_Address_Book')) {
		return;
	}

	// Validate required data
	if (empty($form_data['ho_ten']) || empty($form_data['dia_chi'])) {
		return;
	}

	// Set up billing address data
	$billing_data = array(
		'billing_first_name' => $form_data['ho_ten'],
		'billing_phone' => $form_data['dien_thoai'] ?? '',
		'billing_address_1' => $form_data['dia_chi'],
		'billing_email' => $form_data['email'] ?? '',
		'billing_country' => 'VN', // Default to Vietnam
	);

	// Save billing address
	foreach ($billing_data as $meta_key => $meta_value) {
		if (!empty($meta_value)) {
			update_user_meta($customer_id, $meta_key, $meta_value);
		}
	}

	// Also set as shipping address if not different
	$shipping_data = array(
		'shipping_first_name' => $form_data['ho_ten'],
		'shipping_address_1' => $form_data['dia_chi'],
		'shipping_country' => 'VN',
	);

	foreach ($shipping_data as $meta_key => $meta_value) {
		if (!empty($meta_value)) {
			update_user_meta($customer_id, $meta_key, $meta_value);
		}
	}

	/**
	 * Action hook after address is added to address book
	 *
	 * @since 1.0.0
	 * @param int $customer_id The customer ID
	 * @param array $form_data The form data
	 * @param array $billing_data The billing address data
	 */
	do_action('canhcam_after_address_book_update', $customer_id, $form_data, $billing_data);
}


// =============================================================================
// ADMIN USER PROFILE MANAGEMENT
// =============================================================================

/**
 * Display custom user profile fields in admin user edit screen
 *
 * Shows all custom registration fields in the WordPress admin user profile
 * including Vietnamese-specific fields and 3DS API logs.
 *
 * @since 1.0.0
 * @param WP_User $user The user object being edited
 * @return void
 */
function canhcam_display_admin_user_profile_fields($user)
{
	// Validate user object
	if (!$user || !isset($user->ID)) {
		return;
	}

	// Get all custom user meta data
	$user_data = canhcam_get_user_profile_data($user->ID);

	// Get API logs
	$api_logs = canhcam_get_user_api_logs($user->ID);

?>
	<h3><?php esc_html_e('Thông tin người dùng', 'canhcamtheme'); ?></h3>
	<table class="form-table">
		<?php echo canhcam_render_admin_profile_field('ten_dang_nhap', __('Tên đăng nhập', 'canhcamtheme'), $user_data['ten_dang_nhap'], 'text'); ?>
		<?php echo canhcam_render_admin_profile_field('ho_ten', __('Họ tên', 'canhcamtheme'), $user_data['ho_ten'], 'text'); ?>
		<?php echo canhcam_render_admin_profile_field('user_phone', __('Số điện thoại', 'canhcamtheme'), $user_data['user_phone'], 'tel'); ?>
		<?php echo canhcam_render_admin_profile_field('ngay_sinh', __('Ngày sinh', 'canhcamtheme'), $user_data['ngay_sinh'], 'date'); ?>
		<?php echo canhcam_render_admin_profile_field('dia_chi', __('Địa chỉ', 'canhcamtheme'), $user_data['dia_chi'], 'textarea'); ?>
		<?php echo canhcam_render_admin_profile_field('ma_so_thue', __('Mã số thuế', 'canhcamtheme'), $user_data['ma_so_thue'], 'text'); ?>
		<?php echo canhcam_render_admin_checkbox_field('nhan_email', __('Nhận email', 'canhcamtheme'), $user_data['nhan_email']); ?>
		<?php echo canhcam_render_admin_status_field($user_data['trang_thai']); ?>
		<?php echo canhcam_render_admin_api_log_field('3ds_api_response', __('3DS API Response', 'canhcamtheme'), $api_logs['success']); ?>
		<?php echo canhcam_render_admin_api_log_field('3ds_api_error', __('3DS API Error', 'canhcamtheme'), $api_logs['error']); ?>
	</table>
<?php
}
add_action('show_user_profile', 'canhcam_display_admin_user_profile_fields');
add_action('edit_user_profile', 'canhcam_display_admin_user_profile_fields');

/**
 * Get user profile data for admin display
 *
 * @since 1.0.0
 * @param int $user_id The user ID
 * @return array User profile data
 */
function canhcam_get_user_profile_data($user_id)
{
	return array(
		'ten_dang_nhap' => get_user_meta($user_id, 'ten_dang_nhap', true),
		'ho_ten' => get_user_meta($user_id, 'ho_ten', true),
		'user_phone' => get_user_meta($user_id, 'user_phone', true),
		'ngay_sinh' => get_user_meta($user_id, 'ngay_sinh', true),
		'dia_chi' => get_user_meta($user_id, 'dia_chi', true),
		'ma_so_thue' => get_user_meta($user_id, 'ma_so_thue', true),
		'nhan_email' => get_user_meta($user_id, 'nhan_email', true),
		'trang_thai' => get_user_meta($user_id, 'trang_thai', true),
	);
}

/**
 * Get user API logs
 *
 * @since 1.0.0
 * @param int $user_id The user ID
 * @return array API logs data
 */
function canhcam_get_user_api_logs($user_id)
{
	return array(
		'success' => get_user_meta($user_id, '_3ds_api_response', true),
		'error' => get_user_meta($user_id, '_3ds_api_error', true),
	);
}

/**
 * Render admin profile field
 *
 * @since 1.0.0
 * @param string $field_name Field name
 * @param string $label Field label
 * @param mixed $value Field value
 * @param string $type Field type
 * @return string HTML output
 */
function canhcam_render_admin_profile_field($field_name, $label, $value, $type = 'text')
{
	ob_start();
?>
	<tr>
		<th><label for="<?php echo esc_attr($field_name); ?>"><?php echo esc_html($label); ?></label></th>
		<td>
			<?php if ($type === 'textarea') : ?>
				<textarea name="<?php echo esc_attr($field_name); ?>" id="<?php echo esc_attr($field_name); ?>" rows="3" class="regular-text"><?php echo esc_textarea($value); ?></textarea>
			<?php else : ?>
				<input type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr($field_name); ?>" id="<?php echo esc_attr($field_name); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text" />
			<?php endif; ?>
		</td>
	</tr>
<?php
	return ob_get_clean();
}



/**
 * Render admin checkbox field
 *
 * @since 1.0.0
 * @param string $field_name Field name
 * @param string $label Field label
 * @param mixed $value Field value
 * @return string HTML output
 */
function canhcam_render_admin_checkbox_field($field_name, $label, $value)
{
	ob_start();
?>
	<tr>
		<th><label for="<?php echo esc_attr($field_name); ?>"><?php echo esc_html($label); ?></label></th>
		<td>
			<input type="checkbox" name="<?php echo esc_attr($field_name); ?>" id="<?php echo esc_attr($field_name); ?>" value="1" <?php checked($value, 1); ?> />
		</td>
	</tr>
<?php
	return ob_get_clean();
}

/**
 * Render admin status field
 *
 * @since 1.0.0
 * @param mixed $current_value Current status value
 * @return string HTML output
 */
function canhcam_render_admin_status_field($current_value)
{
	ob_start();
?>
	<tr>
		<th><label for="trang_thai"><?php esc_html_e('Trạng thái', 'canhcamtheme'); ?></label></th>
		<td>
			<select name="trang_thai" id="trang_thai">
				<option value="0" <?php selected($current_value, 0); ?>><?php esc_html_e('Chưa kích hoạt', 'canhcamtheme'); ?></option>
				<option value="1" <?php selected($current_value, 1); ?>><?php esc_html_e('Đã kích hoạt', 'canhcamtheme'); ?></option>
				<option value="2" <?php selected($current_value, 2); ?>><?php esc_html_e('Khóa', 'canhcamtheme'); ?></option>
			</select>
		</td>
	</tr>
<?php
	return ob_get_clean();
}

/**
 * Render admin API log field
 *
 * @since 1.0.0
 * @param string $field_name Field name
 * @param string $label Field label
 * @param mixed $log_data Log data
 * @return string HTML output
 */
function canhcam_render_admin_api_log_field($field_name, $label, $log_data)
{
	ob_start();
?>
	<tr>
		<th><label for="<?php echo esc_attr($field_name); ?>"><?php echo esc_html($label); ?></label></th>
		<td>
			<div class="api-log-container" style="max-height: 300px; overflow-y: auto; background-color: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; padding: 10px; font-family: monospace; font-size: 13px;">
				<?php if (!empty($log_data)) : ?>
					<pre style="margin: 0; white-space: pre-wrap; word-break: break-word;"><?php echo esc_html(print_r($log_data, true)); ?></pre>
				<?php else : ?>
					<span style="color: #999; font-style: italic;"><?php esc_html_e('No data available', 'canhcamtheme'); ?></span>
				<?php endif; ?>
			</div>
		</td>
	</tr>
<?php
	return ob_get_clean();
}


/**
 * Save custom user profile fields from admin
 *
 * Handles saving of custom user profile fields when updated from
 * the WordPress admin user profile page.
 *
 * @since 1.0.0
 * @param int $user_id The user ID being updated
 * @return bool|void False if user cannot be edited, void otherwise
 */
function canhcam_save_admin_user_profile_fields($user_id)
{
	// Check permissions
	if (!current_user_can('edit_user', $user_id)) {
		return false;
	}

	// Validate user ID
	if (!$user_id || !is_numeric($user_id)) {
		return false;
	}

	// Define field mappings with sanitization functions
	$field_mappings = array(
		'ten_dang_nhap' => 'sanitize_text_field',
		'ho_ten' => 'sanitize_text_field',
		'user_phone' => 'sanitize_text_field',
		'ngay_sinh' => 'sanitize_text_field',
		'dia_chi' => 'sanitize_textarea_field',
		'ma_so_thue' => 'sanitize_text_field',
	);

	// Process regular fields
	foreach ($field_mappings as $field_name => $sanitize_function) {
		if (isset($_POST[$field_name])) {
			$value = call_user_func($sanitize_function, $_POST[$field_name]);
			update_user_meta($user_id, $field_name, $value);

			// Special handling for full name - also update WordPress standard field
			if ($field_name === 'ho_ten') {
				update_user_meta($user_id, 'first_name', $value);
			}
		}
	}

	// Handle checkbox fields
	$checkbox_fields = array('nhan_email', 'trang_thai');
	foreach ($checkbox_fields as $field_name) {
		$value = isset($_POST[$field_name]) ? 1 : 0;
		update_user_meta($user_id, $field_name, $value);
	}

	// Validate phone number if provided
	if (isset($_POST['user_phone']) && !empty($_POST['user_phone'])) {
		$phone_errors = canhcam_validate_phone_number($_POST['user_phone']);
		if (!empty($phone_errors)) {
			// Add admin notice for phone validation errors
			add_action('admin_notices', function () use ($phone_errors) {
				echo '<div class="notice notice-error"><p>' . esc_html(implode(', ', $phone_errors)) . '</p></div>';
			});
		}
	}

	// Validate birth date if provided
	if (isset($_POST['ngay_sinh']) && !empty($_POST['ngay_sinh'])) {
		$date_errors = canhcam_validate_birth_date($_POST['ngay_sinh']);
		if (!empty($date_errors)) {
			// Add admin notice for date validation errors
			add_action('admin_notices', function () use ($date_errors) {
				echo '<div class="notice notice-error"><p>' . esc_html(implode(', ', $date_errors)) . '</p></div>';
			});
		}
	}

	/**
	 * Action hook after admin user profile fields are saved
	 *
	 * @since 1.0.0
	 * @param int $user_id The user ID
	 * @param array $post_data The POST data
	 */
	do_action('canhcam_after_admin_profile_save', $user_id, $_POST);
}
add_action('personal_options_update', 'canhcam_save_admin_user_profile_fields');
add_action('edit_user_profile_update', 'canhcam_save_admin_user_profile_fields');

// =============================================================================
// FRONTEND ACCOUNT EDIT FUNCTIONALITY
// =============================================================================

/**
 * Display custom fields in WooCommerce edit account form
 *
 * Adds custom user profile fields to the frontend account edit form
 * including readonly fields for username and customer code.
 *
 * @since 1.0.0
 * @return void
 */
function canhcam_display_frontend_account_fields()
{
	$user_id = get_current_user_id();

	// Validate user
	if (!$user_id) {
		return;
	}

	// Get user profile data
	$user_data = canhcam_get_user_profile_data($user_id);

?>
	<fieldset>
		<legend><?php esc_html_e('Thông tin cá nhân', 'canhcamtheme'); ?></legend>

		<?php echo canhcam_render_frontend_readonly_field('ten_dang_nhap', __('Tên đăng nhập', 'canhcamtheme'), $user_data['ten_dang_nhap'], __('Tên đăng nhập không thể thay đổi', 'canhcamtheme')); ?>

		<?php echo canhcam_render_frontend_account_field('ho_ten', 'text', __('Họ tên', 'canhcamtheme'), $user_data['ho_ten'], true); ?>

		<?php echo canhcam_render_frontend_account_field('ngay_sinh', 'date', __('Ngày sinh', 'canhcamtheme'), $user_data['ngay_sinh'], true); ?>

	</fieldset>
<?php
}
add_action('woocommerce_edit_account_form', 'canhcam_display_frontend_account_fields', 20);

/**
 * Render readonly field for frontend account form
 *
 * @since 1.0.0
 * @param string $field_name Field name
 * @param string $label Field label
 * @param mixed $value Field value
 * @param string $help_text Help text to display
 * @return string HTML output
 */
function canhcam_render_frontend_readonly_field($field_name, $label, $value, $help_text = '')
{
	ob_start();
?>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="<?php echo esc_attr($field_name); ?>"><?php echo esc_html($label); ?></label>
		<input type="text"
			class="woocommerce-Input woocommerce-Input--text input-text"
			name="<?php echo esc_attr($field_name); ?>"
			id="<?php echo esc_attr($field_name); ?>"
			value="<?php echo esc_attr($value); ?>"
			readonly />
		<?php if ($help_text) : ?>
			<span><em><?php echo esc_html($help_text); ?></em></span>
		<?php endif; ?>
	</p>
<?php
	return ob_get_clean();
}

/**
 * Render regular field for frontend account form
 *
 * @since 1.0.0
 * @param string $field_name Field name
 * @param string $field_type Field type
 * @param string $label Field label
 * @param mixed $value Field value
 * @param bool $required Whether field is required
 * @return string HTML output
 */
function canhcam_render_frontend_account_field($field_name, $field_type, $label, $value, $required = false)
{
	$required_attr = $required ? 'required' : '';
	$required_mark = $required ? ' <span class="required">*</span>' : '';

	ob_start();
?>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="<?php echo esc_attr($field_name); ?>"><?php echo esc_html($label); ?><?php echo $required_mark; ?></label>
		<input type="<?php echo esc_attr($field_type); ?>"
			class="woocommerce-Input woocommerce-Input--text input-text"
			name="<?php echo esc_attr($field_name); ?>"
			id="<?php echo esc_attr($field_name); ?>"
			value="<?php echo esc_attr($value); ?>"
			<?php echo $required_attr; ?> />
	</p>
<?php
	return ob_get_clean();
}



/**
 * Validate custom fields on frontend account edit form
 *
 * Validates all custom fields when user updates their account details
 * from the frontend my account page.
 *
 * @since 1.0.0
 * @param WP_Error $errors Existing validation errors
 * @return WP_Error Updated errors object
 */
function canhcam_validate_frontend_account_fields($errors)
{
	// Validate required fields
	$required_fields = array(
		'ho_ten' => __('Họ tên là bắt buộc!', 'canhcamtheme'),
		'ngay_sinh' => __('Ngày sinh là bắt buộc!', 'canhcamtheme'),
		'account_phone' => __('Số điện thoại là bắt buộc!', 'canhcamtheme'),
	);

	foreach ($required_fields as $field => $message) {
		if (empty($_POST[$field])) {
			$errors->add($field . '_error', $message);
		}
	}

	// Validate phone number
	if (!empty($_POST['account_phone'])) {
		$phone_errors = canhcam_validate_phone_number($_POST['account_phone']);
		foreach ($phone_errors as $error) {
			$errors->add('account_phone_error', $error);
		}
	}

	// Validate birth date
	if (!empty($_POST['ngay_sinh'])) {
		$date_errors = canhcam_validate_birth_date($_POST['ngay_sinh']);
		foreach ($date_errors as $error) {
			$errors->add('ngay_sinh_error', $error);
		}
	}

	return $errors;
}
add_filter('woocommerce_save_account_details_errors', 'canhcam_validate_frontend_account_fields', 10);

/**
 * Save custom fields from frontend account edit form
 *
 * Processes and saves custom user profile fields when updated
 * from the frontend my account page.
 *
 * @since 1.0.0
 * @param int $user_id The user ID being updated
 * @return void
 */
function canhcam_save_frontend_account_fields($user_id)
{
	// Validate user ID
	if (!$user_id || !is_numeric($user_id)) {
		return;
	}

	// Define field mappings with sanitization functions
	$field_mappings = array(
		'ho_ten' => 'sanitize_text_field',
		'ngay_sinh' => 'sanitize_text_field',
		'ma_so_thue' => 'sanitize_text_field',
	);

	// Process regular fields
	foreach ($field_mappings as $field_name => $sanitize_function) {
		if (isset($_POST[$field_name]) && !empty($_POST[$field_name])) {
			$value = call_user_func($sanitize_function, $_POST[$field_name]);
			update_user_meta($user_id, $field_name, $value);

			// Special handling for full name - also update WordPress standard field
			if ($field_name === 'ho_ten') {
				update_user_meta($user_id, 'first_name', $value);
			}
		}
	}

	// Handle optional fields
	if (isset($_POST['ma_so_thue'])) {
		update_user_meta($user_id, 'ma_so_thue', sanitize_text_field($_POST['ma_so_thue']));
	}

	// Handle checkbox fields
	update_user_meta($user_id, 'nhan_email', isset($_POST['nhan_email']) ? 1 : 0);

	// Prepare data for potential API sync or other integrations
	$form_data = array(
		'user_id' => $user_id,
		'ho_ten' => isset($_POST['ho_ten']) ? sanitize_text_field($_POST['ho_ten']) : '',
		'email' => isset($_POST['account_email']) ? sanitize_email($_POST['account_email']) : '',
		'dien_thoai' => isset($_POST['account_phone']) ? sanitize_text_field($_POST['account_phone']) : '',
		'ngay_sinh' => isset($_POST['ngay_sinh']) ? sanitize_text_field($_POST['ngay_sinh']) : '',
		'ma_so_thue' => isset($_POST['ma_so_thue']) ? sanitize_text_field($_POST['ma_so_thue']) : '',
		'nhan_email' => isset($_POST['nhan_email']) ? 1 : 0,
	);

	/**
	 * Action hook after frontend account details are saved
	 *
	 * @since 1.0.0
	 * @param int $user_id The user ID
	 * @param array $form_data The sanitized form data
	 */
	do_action('canhcam_after_frontend_account_save', $user_id, $form_data);
}
add_action('woocommerce_save_account_details', 'canhcam_save_frontend_account_fields', 20);


// =============================================================================
// UTILITY FUNCTIONS
// =============================================================================

/**
 * Add phone number to current user object
 *
 * Extends the current user object with the phone number from user meta
 * for easier access throughout the application.
 *
 * @since 1.0.0
 * @param WP_User $current_user The current user object
 * @return WP_User Modified user object with phone number
 */
function canhcam_add_phone_to_current_user($current_user)
{
	if (!is_a($current_user, 'WP_User')) {
		return $current_user;
	}

	// Fetch the user_phone meta value and add it to the current_user object
	$user_phone = get_user_meta($current_user->ID, 'user_phone', true);
	$current_user->user_phone = $user_phone;

	return $current_user;
}
add_filter('wp_get_current_user', 'canhcam_add_phone_to_current_user');

/**
 * Redirect to login page with success message after registration
 *
 * Redirects users to the login page with a success parameter
 * after successful registration.
 *
 * @since 1.0.0
 * @param string $redirect_to The original redirect URL
 * @return string Modified redirect URL
 */
function canhcam_registration_redirect($redirect_to)
{
	if (isset($_POST['register']) && !is_wp_error($redirect_to)) {
		$login_url = get_page_link_by_template('template-woocommerce/page-login.php');
		if ($login_url) {
			$redirect_to = add_query_arg('registered', 'true', $login_url);
		}
	}
	return $redirect_to;
}
add_filter('woocommerce_registration_redirect', 'canhcam_registration_redirect', 2);

/**
 * Display registration success message
 *
 * Shows a success notice when user is redirected after successful registration.
 *
 * @since 1.0.0
 * @return void
 */
function canhcam_registration_success_message()
{
	if (isset($_GET['registered']) && $_GET['registered'] === 'true') {
		wc_add_notice(__('Bạn đã đăng ký thành công.', 'canhcamtheme'), 'success');
	}
}
add_action('wp', 'canhcam_registration_success_message');

/**
 * Add container wrapper for account pages
 *
 * Adds a container div around the account navigation for styling purposes.
 *
 * @since 1.0.0
 * @return void
 */
function canhcam_add_account_container() {
    ?>

    <!-- ===== BREADCRUMB (FULL WIDTH) ===== -->
    <div class="global-breadcrumb">
        <div class="container">
            <?php
            if (function_exists('rank_math_the_breadcrumbs')) {

                rank_math_the_breadcrumbs();

            } elseif (function_exists('yoast_breadcrumb')) {

                yoast_breadcrumb(
                    '<nav class="yoast-breadcrumbs" aria-label="Breadcrumbs">',
                    '</nav>'
                );

            } elseif (function_exists('woocommerce_breadcrumb')) {

                woocommerce_breadcrumb();
            }
            ?>
        </div>
    </div>

    <!-- ===== ACCOUNT GRID WRAPPER ===== -->
    <div class="container wrap-grid-account">

    <?php
}
add_action('woocommerce_before_account_navigation', 'canhcam_add_account_container', 1);


// =============================================================================
// ACCOUNT CUSTOMIZATION & DISPLAY
// =============================================================================

/**
 * Modify billing fields for account address editing
 *
 * Customizes billing fields when editing address in my account area.
 * Currently preserves original functionality but provides structure for future modifications.
 *
 * @since 1.0.0
 * @param array $billing_fields Array of billing fields
 * @return array Modified billing fields
 */
function canhcam_modify_account_billing_fields($billing_fields)
{
	if (is_wc_endpoint_url('edit-address')) {
		// Future customizations can be added here
		// Example: unset($billing_fields['billing_company']);
		// Example: $billing_fields['billing_country']['required'] = false;
	}
	return $billing_fields;
}
add_filter('woocommerce_billing_fields', 'canhcam_modify_account_billing_fields', 20, 1);

/**
 * Display custom account address with styling
 *
 * Renders the account address section with custom styling and template.
 * Used as a custom action hook for flexible placement.
 *
 * @since 1.0.0
 * @return void
 */
function canhcam_display_custom_account_address()
{
	$current_user = wp_get_current_user();
	if (!($current_user instanceof WP_User)) {
		return;
	}

	// Get user's shipping address
	$customer_id = $current_user->ID;
	$shipping_address = wc_get_account_formatted_address('shipping', $customer_id);

	// Add custom styling
?>
	<style>
		.wrap-box-account-column>div:nth-child(1) {
			display: none;
		}

		.wrap-box-account-column {
			margin-top: 20px;
		}
	</style>
<?php

	// Load custom template part
	get_template_part(
		'modules/woo-components/my-account/box-account-address',
		'',
		array(
			'user_info' => $current_user,
			'address' => $shipping_address
		)
	);
}
add_action('custom_account_address_default', 'canhcam_display_custom_account_address');

/**
 * Display custom dashboard information
 *
 * Customizes the WooCommerce account dashboard to show user information
 * and recent orders using custom template parts.
 *
 * @since 1.0.0
 * @return void
 */
function canhcam_display_dashboard_user_info()
{
	$current_user = wp_get_current_user();
	if (!($current_user instanceof WP_User)) {
		return;
	}

	// Get user's shipping address
	$customer_id = $current_user->ID;
	$shipping_address = wc_get_account_formatted_address('shipping', $customer_id);

	// Display user information section
	get_template_part(
		'modules/woo-components/my-account/box-account-address',
		'',
		array(
			'user_info' => $current_user,
			'address' => $shipping_address
		)
	);

	// Display recent orders section
	$recent_orders = canhcam_get_user_recent_orders($customer_id);
	get_template_part(
		'modules/woo-components/my-account/box-account-orders',
		'',
		array('user_orders' => $recent_orders)
	);
}
add_action('woocommerce_account_dashboard', 'canhcam_display_dashboard_user_info');

/**
 * Get user's recent orders
 *
 * @since 1.0.0
 * @param int $customer_id The customer ID
 * @param int $limit Number of orders to retrieve
 * @return array Array of WC_Order objects
 */
function canhcam_get_user_recent_orders($customer_id, $limit = 10)
{
	$order_args = array(
		'limit' => $limit,
		'customer_id' => $customer_id,
		'status' => array_keys(wc_get_order_statuses()),
		'orderby' => 'date',
		'order' => 'DESC',
	);

	return wc_get_orders($order_args);
}

// =============================================================================
// ACCOUNT FIELD CUSTOMIZATION
// =============================================================================

/**
 * Remove last name from required account fields
 *
 * Removes the last name field from the required fields list
 * in the account details form.
 *
 * @since 1.0.0
 * @param array $fields Array of required fields
 * @return array Modified fields array
 */
function canhcam_remove_required_last_name($fields)
{
	unset($fields['account_last_name']);
	return $fields;
}
add_filter('woocommerce_save_account_details_required_fields', 'canhcam_remove_required_last_name');

/**
 * Hide last name field with CSS
 *
 * Adds CSS to hide the last name field from the account edit form.
 *
 * @since 1.0.0
 * @return void
 */
function canhcam_hide_last_name_field()
{
?>
	<style>
		body label[for="account_last_name"] {
			display: none !important;
		}

		#account_last_name {
			display: none;
		}
	</style>
<?php
}
add_action('woocommerce_before_edit_account_form', 'canhcam_hide_last_name_field');

/**
 * Add phone field to account edit form
 *
 * Adds a phone number field to the WooCommerce account edit form
 * with the current user's phone number as the default value.
 *
 * @since 1.0.0
 * @return void
 */
function canhcam_add_phone_field_to_account_form()
{
	$user_id = get_current_user_id();
	if (!$user_id) {
		return;
	}

	$user_phone = get_user_meta($user_id, 'user_phone', true);
?>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_phone"><?php esc_html_e('Số điện thoại', 'canhcamtheme'); ?> <span class="required">*</span></label>
		<input type="tel"
			class="woocommerce-Input woocommerce-Input--text input-text"
			name="account_phone"
			id="account_phone"
			value="<?php echo esc_attr($user_phone); ?>"
			maxlength="<?php echo CANHCAM_PHONE_MAX_LENGTH; ?>" />
	</p>
<?php
}
add_action('woocommerce_edit_account_form_fields', 'canhcam_add_phone_field_to_account_form', 99);

/**
 * Save phone field from account edit form
 *
 * Saves the phone number when user updates their account details.
 *
 * @since 1.0.0
 * @param int $user_id The user ID being updated
 * @return void
 */
function canhcam_save_phone_field_from_account_form($user_id)
{
	if (isset($_POST['account_phone']) && !empty($_POST['account_phone'])) {
		$phone = sanitize_text_field($_POST['account_phone']);
		update_user_meta($user_id, 'user_phone', $phone);
	}
}
add_action('woocommerce_save_account_details', 'canhcam_save_phone_field_from_account_form');

/**
 * Remove first name from required account fields
 *
 * Removes the first name field from the required fields list
 * in the account details form.
 *
 * @since 1.0.0
 * @param array $fields Array of required fields
 * @return array Modified fields array
 */
function canhcam_remove_required_first_name($fields)
{
	unset($fields['account_first_name']);
	return $fields;
}
add_filter('woocommerce_save_account_details_required_fields', 'canhcam_remove_required_first_name');

/**
 * Hide first name field with CSS
 *
 * Adds CSS to hide the first name field from the account edit form.
 *
 * @since 1.0.0
 * @return void
 */
function canhcam_hide_first_name_field()
{
?>
	<style>
		label[for="account_first_name"] {
			display: none !important;
		}

		#account_first_name {
			display: none;
		}
	</style>
<?php
}
add_action('woocommerce_before_edit_account_form', 'canhcam_hide_first_name_field');

// Woo: Redirect to login page if not logged in
function custom_redirects()
{
	if (is_account_page() && !is_user_logged_in() && !is_wc_endpoint_url('lost-password') && !is_wc_endpoint_url('reset-password')) {
		$login_page = get_page_link_by_template('template-woocommerce/page-login.php');
		wp_redirect(get_permalink($login_page));
		die;
	}
}
add_action('template_redirect', 'custom_redirects');
