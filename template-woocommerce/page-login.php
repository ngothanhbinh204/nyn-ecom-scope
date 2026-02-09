<?php /*
Template name: Page - Đăng nhập
*/ ?>
<?php
if (is_user_logged_in()) {
	wp_redirect(home_url()); // Replace '/dashboard' with the desired redirect page URL
	exit;
}
?>
<?php get_header() ?>
<section class="section-page-login">
	<div class="container">
		<div class="section-wrap-box-white">
			<div class="form-title"><?= _e('Đăng nhập', 'canhcamtheme') ?></div>
			<div class="wrap-form"> <?php echo do_shortcode('[wc_login_form_bbloomer]') ?> </div>
		</div>
	</div>
</section>
<?php get_footer() ?>