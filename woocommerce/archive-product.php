<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 */

defined('ABSPATH') || exit;

get_header('shop'); // Standard Woo Header

$acf_id = 'options';
$banner_title = woocommerce_page_title(false);
$banner_desc = '';
$banner_image = get_template_directory_uri() . '/template-woocommerce/img/4.png';

if ( is_shop() ) {
	$shop_id = wc_get_page_id( 'shop' );
	$acf_id = $shop_id;
	$banner_image_val = get_field('banner_image', $shop_id);
	if($banner_image_val) $banner_image = $banner_image_val;
	
	// For Shop page, Description might come from content or custom field
	$shop_page = get_post($shop_id);
	if($shop_page) $banner_desc = $shop_page->post_excerpt ?: $shop_page->post_content;
} elseif ( is_product_taxonomy() ) {
	$term = get_queried_object();
	$acf_id = $term;
	$banner_image_val = get_field('banner_image', $term);
	if($banner_image_val) $banner_image = $banner_image_val;
	$banner_desc = term_description();
}

// Fetch other ACF fields either from Term or Shop Page
$intro_title = get_field('intro_title', $acf_id);
$intro_subtitle = get_field('intro_subtitle', $acf_id);
$intro_c1 = get_field('intro_content_1', $acf_id);
$intro_c2 = get_field('intro_content_2', $acf_id);

$highlights_title = get_field('highlights_title', $acf_id);
$highlights_subtitle = get_field('highlights_subtitle', $acf_id);
$highlights = get_field('cat_highlights', $acf_id);

$info_bg = get_field('info_bg', $acf_id) ?: get_template_directory_uri() . '/template-woocommerce/img/bg-fragrances.png';
$info_title = get_field('info_title', $acf_id);
$info_subtitle = get_field('info_subtitle', $acf_id);
$info_content_repeater = get_field('info_content_repeater', $acf_id);

?>

<main>
	<section class="page-banner-main">
		<div class="img img-ratio pt-[calc(640/1920*100rem)]">
			<img class="lozad" data-src="<?php echo esc_url($banner_image); ?>" alt="<?php echo esc_attr($banner_title); ?>"/>
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

	<!-- Section 1: Product Grid (Swiper) -->
	<section class="product-1 section-py">
		<div class="container">
			<div class="wrap-heading mb-base md:gap-0 gap-3 md:flex-row flex-col items-start  flex md:items-center justify-between"> 
				<h2 class="title"><?php echo esc_html($banner_title); ?></h2>
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
				<p>Chưa có sản phẩm nào.</p>
			<?php endif; ?>
		</div>
	</section>

	<!-- Sections (Only show if fields exist) -->
	<?php if ($intro_title || $intro_c1): ?>
	<section class="product-2 section-py bg-Utility-50">
		<div class="container"> 
			<div class="wrap-heading rem:max-w-[920px] w-full mx-auto text-center font-extrabold">
				<h2 class="title rem:text-[60px]" data-aos="fade-up"><?php echo esc_html($intro_title); ?></h2>
				<?php if($intro_subtitle): ?>
					<div class="sub-title rem:text-[30px] mb-8" data-aos="fade-up" data-aos-delay="100"><?php echo esc_html($intro_subtitle); ?></div>
				<?php endif; ?>
				<div class="line rem:h-[2px] rem:w-[57px] bg-Utility-Black mx-auto" data-aos="zoom-in" data-aos-delay="200"> </div>
				<div class="format-content body-2 font-light font-secondary space-y-8 mt-8" data-aos="fade-up" data-aos-delay="300">
					<?php if($intro_c1) echo wp_kses_post($intro_c1); ?>
					<?php if($intro_c2) echo wp_kses_post($intro_c2); ?>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ($highlights): ?>
	<section class="product-3 section-py">
		<div class="wrap-heading mb-base flex flex-col gap-1 font-extrabold text-center"> 
			<h2 class="title rem:text-[60px]" data-aos="fade-up"><?php echo esc_html($highlights_title); ?></h2>
			<div class="sub-title rem:text-[30px]" data-aos="fade-up" data-aos-delay="100"><?php echo esc_html($highlights_subtitle); ?></div>
		</div>
		<div class="wrapper-main-collection">
			<?php foreach ($highlights as $index => $item): 
				$gallery = $item['gallery'];
				$h_title = $item['title'];
				$h_desc = $item['description'];
				$h_link = $item['link'];
				$is_even = $index % 2 !== 0; 
			?>
			<div class="wrapper-list flex <?php echo $is_even ? 'flex-row-reverse' : ''; ?>">
				<div class="col-left md:w-2/4 w-full">
					<div class="swiper-column-auto relative">
						<div class="swiper">
							<div class="swiper-wrapper">
								<?php if($gallery): foreach($gallery as $media_url): 
									$is_video = preg_match('/\.(mp4|webm|ogg)$/i', $media_url);
								?>
									<div class="swiper-slide">
										<?php if($is_video): ?>
											<div class="video-item"> 
												<a class="img-ratio ratio:pt-[640_960] zoom-img h-full" href="#"> 
													<video class="w-full h-full object-cover" src="<?php echo esc_url($media_url); ?>" autoplay muted playsinline></video>
												</a>
												 <div class="play-icon"><i class="fa-solid fa-pause"></i></div>
											</div>
										<?php else: ?>
											 <div class="video-item"> 
												<a class="img-ratio ratio:pt-[640_960] zoom-img h-full" href="#"> 
													<img class="w-full h-full object-cover" src="<?php echo esc_url($media_url); ?>" />
												</a>
											 </div>
										<?php endif; ?>
									</div>
								<?php endforeach; endif; ?>
							</div>
						</div>
						<div class="wrap-button-slide">
							<div class="btn btn-sw-1 btn-prev secondary"></div>
							<div class="btn btn-sw-1 btn-next secondary"></div>
						</div>
					</div>
				</div>
				<div class="col-right md:w-2/4 w-full md:py-0 p-5">
					<div class="wrap-content">
						<div class="wrap-content-top">
							<h3 class="title heading-2 font-extrabold mb-8"><a href="<?php echo esc_url($h_link); ?>"><?php echo esc_html($h_title); ?></a></h3>
							<div class="desc body-2 font-normal"> 
								<?php echo wp_kses_post($h_desc); ?>
							</div>
						</div>
						<?php if($h_link): ?>
						<div class="wrap-content-bottom mt-8">
							<div class="button-more"><a class="btn btn-primary" href="<?php echo esc_url($h_link); ?>"><span> Khám phá sản phẩm</span></a></div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ($info_content_repeater): ?>
	<section class="product-4 section-py" style="background-image: url('<?php echo esc_url($info_bg); ?>'); background-size: cover; background-position: center;">
		<div class="container">
			<div class="wrap-heading mb-base rem:max-w-[1160px] w-full mx-auto flex flex-col gap-1 font-extrabold text-center"> 
				<h2 class="title rem:text-[60px]" data-aos="fade-up"><?php echo esc_html($info_title); ?></h2>
				<div class="sub-title rem:text-[30px] mb-8" data-aos="fade-up" data-aos-delay="100"><?php echo esc_html($info_subtitle); ?></div>
				<div class="line rem:h-[2px] rem:w-[57px] bg-Utility-Black mx-auto" data-aos="zoom-in" data-aos-delay="200"> </div>
				
				<div class="wrapper-inner flex flex-col gap-8 mt-8">
					<?php foreach ($info_content_repeater as $idx => $item): ?>
						<div class="content-item" data-aos="fade-up" data-aos-delay="<?php echo 300 + ($idx * 100); ?>"> 
							<div class="title rem:text-[30px] font-extrabold mb-1"><?php echo esc_html($item['item_title']); ?></div>
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

<?php get_footer('shop'); ?>