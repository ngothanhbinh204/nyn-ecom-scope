<?php /*
Template name: Page - Đăng ký
*/ ?>
<?php get_header() ?>
<section class="section-page-register">
	<div class="container">
		<div class="box-white">
			<div class="title-form"><?= _e('Đăng ký', 'canhcamtheme') ?></div>
			<?php
			wc_print_notices();
			?>
			<?= do_shortcode('[custom_registration_form]') ?>
		</div>
	</div>
</section>
<?php get_footer() ?>