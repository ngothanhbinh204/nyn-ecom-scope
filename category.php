<?php get_header(); ?>

<main>
	<div class="global-breadcrumb">
			<div class="container">
				<?php 
				if ( function_exists('yoast_breadcrumb') ) {
					yoast_breadcrumb( '<nav class="rank-math-breadcrumb" aria-label="breadcrumbs">','</nav>' );
				} elseif ( function_exists('rank_math_the_breadcrumbs') ) {
					rank_math_the_breadcrumbs();
				} else {
					woocommerce_breadcrumb();
				}
				?>
			</div>
		</div>
<section class="news xl:py-24 py-10">
	<div class="container">

		<div class="wrap-heading flex flex-col justify-center text-center">
			<h2 class="title heading-1 font-extrabold mb-base">
				<?php single_cat_title(); ?>
			</h2>

			<?php
			$current_term = get_queried_object();

			if ($current_term && $current_term instanceof WP_Term) {

				$parent_id = $current_term->parent ? $current_term->parent : $current_term->term_id;

				$parent_term = get_term($parent_id, 'category');

				$child_categories = get_categories([
					'taxonomy'   => 'category',
					'parent'     => $parent_id,
					'hide_empty' => false,
				]);
			}
			?>

			<ul class="nav-primary">

				<?php if (!empty($parent_term) && !is_wp_error($parent_term)) : ?>
					<li class="nav-item">
						<a href="<?php echo esc_url(get_category_link($parent_term->term_id)); ?>"
						class="nav-link <?php echo ($current_term->term_id == $parent_term->term_id) ? 'active' : ''; ?>">
							<?php esc_html_e('ALL', 'canhcamtheme-woo'); ?>
						</a>
					</li>
				<?php endif; ?>

				<?php if (!empty($child_categories)) : ?>
					<?php foreach ($child_categories as $cat) : ?>
						<li class="nav-item">
							<a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"
							class="nav-link <?php echo ($current_term->term_id == $cat->term_id) ? 'active' : ''; ?>">
								<?php echo esc_html($cat->name); ?>
							</a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>

			</ul>
		</div>

		<div class="wrapper-list grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-base mt-base">

			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); 
					$categories     = get_the_category();
					$category_name  = !empty($categories) ? $categories[0]->name : '';
				?>

				<article <?php post_class('news-item'); ?>>
					<div class="news-item group">

						<div class="img">
							<a class="img-ratio ratio:pt-[293_440] zoom-img" href="<?php the_permalink(); ?>">
								<?php if (has_post_thumbnail()) : ?>
									<img class="lozad"
										 data-src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>"
										 alt="<?php the_title_attribute(); ?>" />
								<?php else : ?>
									<img class="lozad"
										 data-src="<?php echo esc_url(get_template_directory_uri() . '/img/placeholder.jpg'); ?>"
										 alt="<?php the_title_attribute(); ?>" />
								<?php endif; ?>
							</a>
						</div>

						<div class="content xl:py-6 xl:px-4 p-4">
							<div class="top-content flex items-center justify-between gap-2 font-normal body-4 font-secondary">
								<div class="day"><?php echo esc_html(get_the_date('d.m.Y')); ?></div>
								<?php if ($category_name) : ?>
									<div class="category text-Primary-1"><?php echo esc_html($category_name); ?></div>
								<?php endif; ?>
							</div>

							<h3 class="heading-4 text-Utility-950 font-bold my-2 group-hover:text-Primary-2 font-secondary">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>

							<div class="desc line-clamp-2 body-1 text-Utility-600 font-medium">
								<?php echo esc_html(get_the_excerpt()); ?>
							</div>
						</div>
					</div>
				</article>

				<?php endwhile; ?>
			<?php else : ?>
				<p class="text-center col-span-full">
					<?php esc_html_e('Không tìm thấy bài viết.', 'canhcamtheme-woo'); ?>
				</p>
			<?php endif; ?>

		</div>

		<div class="pagination mt-base flex-center">
			<?php
			echo wp_bootstrap_pagination([
				'custom_query' => $wp_query, // main query
			]);
			?>
		</div>

	</div>
</section>
</main>
<?php get_footer(); ?>
