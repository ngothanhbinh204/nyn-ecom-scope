<?php
$account_info = $args['user_info'];
$user_phone = get_user_meta(get_current_user_id(), 'user_phone', true);
$edit_account = wc_get_account_endpoint_url('edit-account');
$edit_address_book_url = wc_get_account_endpoint_url('address-book');

// Get default address from custom plugin
$default_address = null;
if (class_exists('WC_Custom_Address_Book')) {
	$address_book_instance = new WC_Custom_Address_Book();
	$current_user_id = get_current_user_id();
	if ($current_user_id) {
		$default_address = $address_book_instance->get_default_address($current_user_id);
	}
}

$view_detail = $args['view_detail'] ?: false;
?>
<div class="wrap-box-account-column">
	<div class="wrap-box-info mb-5">
		<div class="box-title">
			<h2><?= _e('My Information', 'woocommerce') ?></h2>
			<a href="<?= $edit_account ?>" class="link-account"><?= _e('Edit', 'woocommerce') ?></a>
		</div>
		<div class="box-info-dashboard">
			<div class="box-info-dashboard__item">
				<div class="box-info-dashboard__item__title"><?= _e('Full Name', 'woocommerce') ?></div>
				<div class="box-info-dashboard__item__content"><?= $account_info->display_name ?></div>
			</div>
			<div class="box-info-dashboard__item">
				<div class="box-info-dashboard__item__title"><?= _e('Phone', 'woocommerce') ?></div>
				<div class="box-info-dashboard__item__content">
					<?= $user_phone ?>
				</div>
			</div>
			<div class="box-info-dashboard__item">
				<div class="box-info-dashboard__item__title"><?= _e('Email', 'woocommerce') ?></div>
				<div class="box-info-dashboard__item__content"><?= $account_info->user_email ?></div>
			</div>
		</div>
	</div>
	<div class="wrap-box-info mb-5">
		<div class="flex items-center justify-between box-title">
			<h2><?= __('Book Address', 'ait-address-book') ?> - <span class="bold-color"><?= __('Default', 'ait-address-book') ?></span></h2>
			<a href="<?= esc_url($edit_address_book_url) ?>" class="link-account"><?= __('Edit', 'ait-address-book') ?></a>
		</div>
		<?php if ($default_address) : ?>
			<div class="box-info-dashboard">
				<div class="box-info-dashboard__item">
					<div class="box-info-dashboard__item__title"><?= __('Full Name', 'ait-address-book') ?></div>
					<div class="box-info-dashboard__item__content"><?= esc_html(trim($default_address['first_name'] . ' ' . $default_address['last_name'])) ?></div>
				</div>
				<div class="box-info-dashboard__item">
					<div class="box-info-dashboard__item__title"><?= __('Phone', 'ait-address-book') ?></div>
					<div class="box-info-dashboard__item__content"><?= esc_html($default_address['phone']) ?></div>
				</div>
				<div class="box-info-dashboard__item">
					<div class="box-info-dashboard__item__title"><?= __('Address', 'ait-address-book') ?></div>
					<div class="box-info-dashboard__item__content">
						<?php
						$address_parts = array();
						if (!empty($default_address['address_1'])) {
							$address_parts[] = esc_html($default_address['address_1']);
						}
						if (!empty($default_address['ward_name'])) {
							$address_parts[] = esc_html($default_address['ward_name']);
						}
						if (!empty($default_address['district_name'])) {
							$address_parts[] = esc_html($default_address['district_name']);
						}
						if (!empty($default_address['city_name'])) {
							$address_parts[] = esc_html($default_address['city_name']);
						}
						echo implode(',&nbsp;', $address_parts);
						?></div>
				</div>
			</div>
		<?php else : ?>
			<div class="box-info-dashboard">
				<?= __('No default address set.', 'ait-address-book') ?> <a href="<?= esc_url($edit_address_book_url) ?>"><?= __('Manage Addresses', 'ait-address-book') ?></a>
			</div>
		<?php endif; ?>
	</div>
</div>