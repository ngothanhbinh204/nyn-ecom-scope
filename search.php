<?php get_header() ?>

<section class="search-page section-py section-product-list" setbackground="/wp-content/themes/forestBay/img/TinTuc/news-bg.jpg">
	<div class="container max-w-screen-2xl">
		<div class="title-48 mb-base text-center"><?= _e('Tìm kiếm', 'canhcamtheme') ?></div>
		<div class="search-query mb-5"><?= _e('Kết quả tìm kiếm từ khóa:', 'canhcamtheme') ?> " <strong><?= get_search_query() ?></strong> "</div>
		<div class="wrap mb-base">
			<?php
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			$args = array(
				'post_type' => 'product',
				'posts_per_page' => 12,
				'paged' => $paged,
				's' => get_search_query()
			);

			$products = new WP_Query($args);

			if ($products->have_posts()) :
				echo '<div class="title-48 mb-base">' . __('Sản phẩm', 'canhcamtheme') . '</div>';
				echo '<div class="wrap-product-grid">';
				while ($products->have_posts()) : $products->the_post();
					wc_get_template_part('content', 'product');
				endwhile;
				echo '</div>';
				wp_reset_postdata();
			else :
				echo '<p class="text-center text-Primary-Red font-semibold text-center text-2xl">' . __('Không tìm thấy sản phẩm nào.', 'canhcamtheme') . '</p>';
			endif;
			?>
		</div>
		<?php
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$args = array(
			'post_type' => 'post',
			'posts_per_page' => 9,
			'paged' => $paged,
			's' => get_search_query()
		);

		$posts_query = new WP_Query($args);

		if ($posts_query->have_posts()) :
			echo '<div class="title-48 mb-base">' . __('Bài viết', 'canhcamtheme') . '</div>';
			echo '<div class="grid md:grid-cols-2 grid-cols-1 lg:grid-cols-3 gap-5 mb-base">';
			while ($posts_query->have_posts()) : $posts_query->the_post();
		?>
				<div class="item-category-primary relative">
					<div class="img zoom-img">
						<a class="img img-ratio ratio:pt-[400_600]" href="<?php the_permalink(); ?>">
							<?= get_image_post(get_the_ID()) ?>
						</a>
					</div>
					<div class="content w-full py-4 text-black">
						<h3 class="title-32 mb-3 hover-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<div class="desc line-clamp-3">
							<?php the_excerpt(); ?>
						</div>
					</div>
				</div>
		<?php
			endwhile;
			echo '</div>';
			wp_reset_postdata();
		endif;
		?>
	</div>
</section>
<?php get_footer() ?>