<?php

/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if (!defined('ABSPATH')) {
	exit;
}

do_action('woocommerce_before_account_navigation');
?>

<nav class="woocommerce-MyAccount-navigation">
	<div class="block-info-profile">
		<div class="avatar-profile">
			<img src="<?php bloginfo('template_directory') ?>/img/no-avatar.svg" alt="">
		</div>
		<div class="info-profile">
			<?php
				$user_logged = wp_get_current_user();
				// Get link edit account
				$edit_account_url = wc_get_account_endpoint_url('edit-account');
			?>
			<p class="name-profile"><?= $user_logged->display_name ?></p>
			<a class="email-profile" href="<?= $edit_account_url ?>"><?= _e('Edit account','woocommerce') ?></a>
		</div>
	</div>
	<ul>
		<?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>
			<li class="<?php echo wc_get_account_menu_item_classes($endpoint); ?>">
				<a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>"><?php echo esc_html($label); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action('woocommerce_after_account_navigation'); ?>