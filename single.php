<?php get_header() ?>
<main>
<!-- <?php get_template_part('modules/common/breadcrumb') ?> -->
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
<section class="news-detail section-py">
	<div class="container">
		<div class="news-detail-main rem:max-w-[1160px] w-full mx-auto gap-base">
			<div class="position-relative relative">
				<h1 class="heading-1 font-bold"><?php the_title(); ?></h1>
				<div class="news-item-meta py-3 flex items-center gap-2">
					<div class="news-item-date text-gray-300"><?php echo get_the_date('d/m/Y'); ?></div>
					<?php 
					$categories = get_the_category();
					if (!empty($categories)) : 
					?>
					<div class="news-item-category text-Primary-2 font-bold"><?php echo esc_html($categories[0]->name); ?></div>
					<?php endif; ?>
					<div class="line rem:h-[1px] flex-1 bg-Utility-Black"></div>
				</div>
				<div class="format-content body-1 font-secondary">
					<?php
					if (canhcam_embed()):
					?>
						<?php the_content() ?>
					<?php else: ?>
						<?php
						get_content_3ds(get_the_ID());
						?>
					<?php endif; ?>
				</div>
				
				<?php if (have_rows('social_networks', 'option')) : ?>
					<div class="sticky-share-post absolute right-full top-0 xl:rem:mr-[160px] mr-5 bottom-0">
						<div class="detail-share flex lg:flex-col gap-5 sticky top-[calc(var(--header-height)+1.5625rem)]">
							<ul>
								<?php while (have_rows('social_networks', 'option')) : the_row(); 
									$icon = get_sub_field('icon');
									$link = get_sub_field('link');

									if (!$link) continue;

									$url    = esc_url($link['url']);
									$label  = esc_attr($link['title'] ?: 'Social Link');
									$target = $link['target'] ? esc_attr($link['target']) : '_blank';
								?>
									<li>
										<a href="<?php echo $url; ?>"
										target="<?php echo $target; ?>"
										rel="noopener noreferrer"
										aria-label="<?php echo $label; ?>">
											<i class="fa-brands <?php echo esc_attr($icon); ?>"></i>
										</a>
									</li>
								<?php endwhile; ?>
							</ul>
						</div>
					</div>
					<?php endif; ?>

			</div>
		</div>
	</div>
</section>

<!-- Related Posts Section -->
<section class="news-detail-new xl:py-24 py-10 border-t border-t-Utility-200">
	<div class="container">
		<h2 class="title heading-1 font-bold font-secondary mb-base text-center">Bài viết mới nhất</h2>
		<div class="swiper-column-auto relative autoplay swiper-loop auto-3-column">
			<div class="swiper">
				<div class="swiper-wrapper">
					<?php
					$args = array(
						'posts_per_page' => 6,
						'post_type' => 'post',
						'order' => 'DESC',
						'orderby' => 'date',
						'post__not_in' => array(get_the_ID())
					);
					$related_query = new WP_Query($args);
					
					if ($related_query->have_posts()) : 
						while ($related_query->have_posts()) : $related_query->the_post();
							$categories = get_the_category();
							$category_name = !empty($categories) ? $categories[0]->name : '';
					?>
						<div class="swiper-slide">
							<div class="news-item">
								<div class="news-item group">
									<div class="img"> 
										<a class="img-ratio ratio:pt-[293_440] zoom-img" href="<?php the_permalink(); ?>">
											<?php if (has_post_thumbnail()) : ?>
												<img class="lozad" data-src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>" alt="<?php the_title_attribute(); ?>" />
											<?php else: ?>
												<img class="lozad" data-src="<?php echo get_template_directory_uri(); ?>/img/placeholder.jpg" alt="<?php the_title_attribute(); ?>" />
											<?php endif; ?>
										</a>
									</div>
									<div class="content xl:py-6 xl:px-4 p-4">
										<div class="top-content flex items-center justify-between gap-2 font-normal body-4 font-secondary">
											<div class="day"><?php echo get_the_date('d.m.Y'); ?></div>
											<?php if ($category_name): ?>
											<div class="category text-Primary-1"><?php echo esc_html($category_name); ?></div>
											<?php endif; ?>
										</div>
										<h3 class="heading-4 text-Utility-950 font-bold my-2 group-hover:text-Primary-2 font-secondary">
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</h3>
										<div class="desc line-clamp-2 body-1 text-Utility-600 font-medium">
											<?php echo get_the_excerpt(); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php 
						endwhile;
					endif;
					wp_reset_postdata();
					?>
				</div>
			</div>
			<div class="wrap-button-slide">
				<div class="btn btn-sw-1 btn-prev"></div>
				<div class="btn btn-sw-1 btn-next"></div>
			</div>
		</div>
	</div>
</section>
</main>
<?php get_footer() ?>

<style>
	@media (min-width: 1200px) {
		.ftwp-fixed-to-post #ftwp-contents {
			left: 20px !important;
		}
	}

	.format-content table td {
		padding: 10px;
	}
</style>