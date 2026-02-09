<?php
/**
 * The Template for displaying product category archives
 */

defined('ABSPATH') || exit;

get_header();

$term = get_queried_object();
$term_id = $term->term_id ?? 0;
$acf_id = $term ? $term : 'options';
// var_dump($acf_id);
$banner_image = get_field('banner_image', $term);
$banner_title = get_field('banner_title', $term);
$content_banner = get_field('content_banner', $term);
if (!$banner_image) {
	$banner_image = get_template_directory_uri() . '/template-woocommerce/img/4.png';
}
if (!$banner_title) {
	$banner_title = $term->name;
}
$banner_desc  = term_description($term_id);
if (!$banner_desc) {
	$banner_desc = '';
}


$intro_title = get_field('intro_title', $term) ?: '';
$intro_subtitle = get_field('intro_subtitle', $term) ?: '';
$intro_content = get_field('intro_content', $term);

$highlights_title = get_field('highlights_title', $term) ?: '';
$highlights_subtitle = get_field('highlights_subtitle', $term) ?: '';
$highlights = get_field('cat_highlights', $term);

$info_bg = get_field('info_bg', $term) ?: get_template_directory_uri() . '/template-woocommerce/img/bg-fragrances.png';
$info_title = get_field('info_title', $term) ?: '';
$info_subtitle = get_field('info_subtitle', $term) ?: 'Mỗi mùi hương được cấu thành từ tinh dầu cao cấp...';
$info_content_repeater = get_field('info_content_repeater', $term);

?>

<main>
	<section class="page-banner-main">
		<div class="img img-ratio pt-[calc(640/1920*100rem)]">
			<img class="lozad" data-src="<?php echo esc_url($banner_image); ?>"
				alt="<?php echo esc_attr($banner_title); ?>" />
		</div>
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
		<div class="content">
			<div class="container">
				<div class="wrap-content">
					<h2 class="title"><?php echo esc_html($banner_title); ?></h2>
					<div class="desc body-5 font-secondary">
						<?php echo wp_kses_post($banner_desc); ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="product-1 section-py">
		<div class="container">
			<div
				class="wrap-heading mb-base md:gap-0 gap-3 md:flex-row flex-col items-start  flex md:items-center justify-between">
				<h2 class="title"><?php echo esc_html($term->name); ?></h2>
				<div class="filters">
					<?php
					$catalog_orderby_options = woocommerce_catalog_ordering();
					// $orderby = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
					?>

				</div>
			</div>

			<?php if ( have_posts() ) : ?>
			<div class="swiper-column-auto relative auto-3-column">
				<div class="swiper">
					<div class="swiper-wrapper">
						<?php while ( have_posts() ) : the_post(); 
								global $product;
								if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
									$product = wc_get_product( get_the_ID() );
								}
							?>
						<div class="swiper-slide">
							<?php wc_get_template_part('content', 'product'); ?>
						</div>
						<?php endwhile; ?>
					</div>
				</div>
				<div class="wrap-button-slide">
					<div class="btn btn-sw-1 btn-prev"></div>
					<div class="btn btn-sw-1 btn-next"></div>
				</div>
			</div>
			<?php else : ?>
			<p>Chưa có sản phẩm nào trong danh mục này.</p>
			<?php endif; ?>
		</div>
	</section>

	<?php if ($intro_title || $intro_content): ?>
	<section class="product-2 section-py bg-Utility-50">
		<div class="container">
			<div class="wrap-heading rem:max-w-[920px] w-full mx-auto text-center font-extrabold">
				<h2 class="title rem:text-[60px]" data-aos="fade-up"><?php echo esc_html($intro_title); ?></h2>
				<?php if($intro_subtitle): ?>
				<div class="sub-title rem:text-[30px] mb-8" data-aos="fade-up" data-aos-delay="100">
					<?php echo wp_kses_post($intro_subtitle); ?></div>
				<?php endif; ?>
				<div class="line rem:h-[2px] rem:w-[57px] bg-Utility-Black mx-auto" data-aos="zoom-in"
					data-aos-delay="200"> </div>
				<div class="format-content body-2 font-light font-secondary space-y-8 mt-8" data-aos="fade-up"
					data-aos-delay="300">
					<?php if($intro_content) echo wp_kses_post($intro_content); ?>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php 
	$collection_products_args = array(
		'post_type' => 'product',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field' => 'term_id',
				'terms' => $term_id,
			)
		)
	);
	$collection_products = new WP_Query($collection_products_args);
	
	if ($collection_products->have_posts()): 
	?>
	<section class="product-3 section-py">
		<div class="wrap-heading mb-base flex flex-col gap-1 font-extrabold text-center">
			<h2 class="title rem:text-[60px]" data-aos="fade-up">
				<?php echo esc_html($highlights_title ?: 'Bộ sưu tập'); ?></h2>
			<?php if ($highlights_subtitle): ?>
			<div class="sub-title rem:text-[30px]" data-aos="fade-up" data-aos-delay="100">
				<?php echo esc_html($highlights_subtitle); ?></div>
			<?php endif; ?>
		</div>
		<div class="wrapper-main-collection">
			<?php 
			$index = 0;
			while ($collection_products->have_posts()): 
				$collection_products->the_post();
				$product_id = get_the_ID();
				$collection_gallery = get_field('collection_image', $product_id);
				$collection_desc = get_field('description_collection', $product_id);
				
				if (empty($collection_gallery) && empty($collection_desc)) {
					continue;
				}
				
				$is_even = $index % 2 !== 0;
				$product_url = get_permalink($product_id);
				$product_title = get_the_title($product_id);
			?>
			<div class="wrapper-list flex <?php echo $is_even ? 'flex-row-reverse' : ''; ?>">
				<div class="col-left md:w-2/4 w-full">
					<?php if ($collection_gallery && is_array($collection_gallery)): ?>
					<div class="swiper-column-auto relative">
						<div class="swiper">
							<div class="swiper-wrapper">
								<?php foreach($collection_gallery as $image_data): 
									$image_url = $image_data['url'];
									$is_video = preg_match('/\.(mp4|webm|ogg)$/i', $image_url);
								?>
								<div class="swiper-slide">
									<?php if($is_video): ?>
									<div class="video-item">
										<a class="img-ratio ratio:pt-[640_960] zoom-img h-full"
											href="<?php echo esc_url($product_url); ?>">
											<video class="w-full h-full object-cover"
												src="<?php echo esc_url($image_url); ?>" autoplay muted
												playsinline></video>
										</a>
										<div class="play-icon"><i class="fa-solid fa-pause"></i></div>
									</div>
									<?php else: ?>
									<div class="video-item">
										<a class="img-ratio ratio:pt-[640_960] zoom-img h-full"
											href="<?php echo esc_url($product_url); ?>">
											<img class="w-full h-full object-cover"
												src="<?php echo esc_url($image_url); ?>"
												alt="<?php echo esc_attr($product_title); ?>" />
										</a>
									</div>
									<?php endif; ?>
								</div>
								<?php endforeach; ?>
							</div>
						</div>
						<div class="wrap-button-slide">
							<div class="btn btn-sw-1 btn-prev secondary"></div>
							<div class="btn btn-sw-1 btn-next secondary"></div>
						</div>
					</div>
					<?php else: ?>
					<div class="img-ratio ratio:pt-[640_960]">
						<a href="<?php echo esc_url($product_url); ?>">
							<?php echo get_the_post_thumbnail($product_id, 'large', array('class' => 'w-full h-full object-cover')); ?>
						</a>
					</div>
					<?php endif; ?>
				</div>
				<div class="col-right md:w-2/4 w-full md:py-0 p-5">
					<div class="wrap-content">
						<div class="wrap-content-top">
							<h3 class="title heading-2 font-extrabold mb-8">
								<a
									href="<?php echo esc_url($product_url); ?>"><?php echo esc_html($product_title); ?></a>
							</h3>
							<?php if ($collection_desc): ?>
							<div class="desc body-2 font-normal">
								<?php echo wp_kses_post($collection_desc); ?>
							</div>
							<?php endif; ?>
						</div>
						<div class="wrap-content-bottom mt-8">
							<div class="button-more">
								<a class="btn btn-primary" href="<?php echo esc_url($product_url); ?>">
									<span>Khám phá sản phẩm</span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php 
				$index++;
			endwhile; 
			wp_reset_postdata();
			?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ($info_content_repeater): ?>
	<section class="product-4 section-py"
		style="background-image: url('<?php echo esc_url($info_bg); ?>'); background-size: cover; background-position: center;">
		<div class="container">
			<div
				class="wrap-heading mb-base rem:max-w-[1160px] w-full mx-auto flex flex-col gap-1 font-extrabold text-center">
				<h2 class="title rem:text-[60px]" data-aos="fade-up"><?php echo esc_html($info_title); ?></h2>
				<div class="sub-title rem:text-[30px] mb-8" data-aos="fade-up" data-aos-delay="100">
					<?php echo wp_kses_post($info_subtitle); ?></div>
				<div class="line rem:h-[2px] rem:w-[57px] bg-Utility-Black mx-auto" data-aos="zoom-in"
					data-aos-delay="200"> </div>

				<div class="wrapper-inner flex flex-col gap-8 mt-8">
					<?php foreach ($info_content_repeater as $idx => $item): ?>
					<div class="content-item" data-aos="fade-up" data-aos-delay="<?php echo 300 + ($idx * 100); ?>">
						<div class="title rem:text-[30px] font-extrabold mb-1">
							<?php echo esc_html($item['item_title']); ?></div>
						<div class="format-content body-2 font-light font-secondary">
							<?php echo wp_kses_post($item['item_desc']); ?>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

</main>

<?php get_footer(); ?>