<?php get_header() ?>
<section class="page-content section-py">
	<div class="container">
		<div class="format-content body-2">
			<?php if (is_account_page()) : ?>
				<?php the_content() ?>
			<?php else : ?>
				<?php the_content() ?>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php get_footer() ?>