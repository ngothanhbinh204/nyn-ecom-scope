<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// ===== VARIABLES CONFIGURATION =====
$product_id       = $product->get_id();
$product_name     = $product->get_name();
$product_sku      = $product->get_sku() ?: 'N/A';
$short_description = $product->get_short_description();

// Image configuration
$featured_image_id            = $product->get_image_id();
$featured_image_id_of_product = get_post_thumbnail_id( $product_id );
$gallery_image_ids            = $product->get_gallery_image_ids();
$all_image_ids                = array_merge( [ $featured_image_id ], $gallery_image_ids );

// Product type and variations
$is_variable_product = $product->is_type( 'variable' );
$variations          = $is_variable_product ? $product->get_available_variations() : [];

// CSS classes
$main_image_classes  = 'img-ratio ratio:pt-[727_918]';
$thumb_image_classes = 'img-ratio';


$list_benefits_product = get_field('list_benefits_product');
$grid_content = get_field('grid_content');
/**
 * Hook: woocommerce_before_single_product.
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<section class="global-breadcrumb">
	<div class="container">
		<nav class="rank-math-breadcrumb" aria-label="breadcrumbs">
			<?php 
			if ( function_exists('yoast_breadcrumb') ) {
				yoast_breadcrumb( '','' );
			} elseif ( function_exists('rank_math_the_breadcrumbs') ) {
				rank_math_the_breadcrumbs();
			} else {
				woocommerce_breadcrumb();
			}
			?>
		</nav>
	</div>
</section>

<section id="product-<?php the_ID(); ?>" <?php wc_product_class( 'product-detail-1 rem:pt-[34px] bg-Utility-gray-50', $product ); ?>>
		<div class="product-detail-main flex flex-col lg:flex-row xl:gap-0 gap-base">
			<div class="col-left xl:rem:max-w-[1062px] w-full">
				<div class="wrapper grid md:grid-cols-[calc(112/1062*100%)_1fr] grid-cols-[calc(150/1062*100%)_1fr] md:gap-[calc(32/1062*100%)] gap-[calc(24/1062*100%)]">
					<div class="thumb relative">
						<div class="relative size-full">
							<div class="swiper">
								<div class="swiper-wrapper">
									<?php
									if ( $is_variable_product ) :
										foreach ( $variations as $variation ) :
											$variation_image_id = $variation['image_id'];
											$variation_id       = $variation['variation_id'];
											$image_url          = get_the_post_thumbnail_url( $variation_id, 'full' );
											if ( $variation_image_id && $image_url ) :
												?>
												<div class="swiper-slide"
													 data-variation-id="<?= esc_attr( $variation_id ) ?>"
													 data-image-id="<?= esc_attr( $variation_image_id ) ?>"
													 data-image-featured="<?= esc_attr( $featured_image_id_of_product ) ?>">
													<div class="img"><a class="<?= esc_attr( $thumb_image_classes ) ?>"> <img class="lozad" data-src="<?= esc_url( $image_url ) ?>" alt="<?= esc_attr( $product_name ) ?>" /></a></div>
												</div>
												<?php
											endif;
										endforeach;
									endif;
									foreach ( $all_image_ids as $image_id ) :
										if ( $image_id ) :
											$image_url = wp_get_attachment_image_url( $image_id, 'thumbnail' ); 
											$image_url_full = wp_get_attachment_image_url( $image_id, 'full' );
											?>
											<div class="swiper-slide">
												<div class="img"><a class="<?= esc_attr( $thumb_image_classes ) ?>"> <img class="lozad" data-src="<?= esc_url( $image_url_full ) ?>" alt="<?= esc_attr( $product_name ) ?>" /></a></div>
											</div>
											<?php
										endif;
									endforeach;
									?>
								</div>
							</div>
						</div>
						<div class="wrap-button-slide arrow-vertical">
							<div class="button-prev"></div>
							<div class="button-next"></div>
						</div>
					</div>

					<div class="main">
						<div class="swiper">
							<div class="swiper-wrapper">
								<?php
								if ( $is_variable_product ) :
									foreach ( $variations as $variation ) :
										$variation_image_id = $variation['image_id'];
										$variation_id       = $variation['variation_id'];
										$image_url          = get_the_post_thumbnail_url( $variation_id, 'full' );
										if ( $variation_image_id && $image_url ) :
											?>
											<div class="swiper-slide"
												 data-variation-id="<?= esc_attr( $variation_id ) ?>"
												 data-image-id="<?= esc_attr( $variation_image_id ) ?>"
												 data-image-featured="<?= esc_attr( $featured_image_id_of_product ) ?>">
												<div class="img">
													<a class="<?= esc_attr( $main_image_classes ) ?>" data-fancybox="product-gallery" href="<?= esc_url( $image_url ) ?>">
														<img class="lozad" data-src="<?= esc_url( $image_url ) ?>" alt="<?= esc_attr( $product_name ) ?>" />
													</a>
												</div>
											</div>
											<?php
										endif;
									endforeach;
								endif;

								// Main Product Images
								foreach ( $all_image_ids as $image_id ) :
									if ( $image_id ) :
										$image_url = wp_get_attachment_image_url( $image_id, 'full' );
										?>
										<div class="swiper-slide">
											<div class="img">
												<a class="<?= esc_attr( $main_image_classes ) ?>" data-fancybox="product-gallery" href="<?= esc_url( $image_url ) ?>">
													<img class="lozad" data-src="<?= esc_url( $image_url ) ?>" alt="<?= esc_attr( $product_name ) ?>" />
												</a>
											</div>
										</div>
										<?php
									endif;
								endforeach;
								?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-right flex-1">
				<div class="wrapper-content rem:max-w-[567px] w-full mx-auto">
					<div class="wrap-info-product-top pb-6 mb-6 border-b border-b-Utility-200">
						<div class="category font-secondary font-bold uppercase">
							<?php echo wc_get_product_category_list( $product_id, ', ' ); ?>
						</div>
						<h1 class="product-title rem:text-[30px] font-extrabold"><?php echo esc_html( $product_name ); ?></h1>
						<?php if ( $product_sku ) : ?>
							<div class="quantity body-1 font-secondary font-light">
								<?php $packaging_info = get_field('packaging_info', $product_id); ?>
<?php if ( $packaging_info ) : ?>
	<span><?php echo esc_html( $packaging_info ); ?></span>
<?php endif; ?>

							</div>
						<?php endif; ?>
					</div>

					<div class="wrap-info-product-bottom flex flex-col gap-3 mb-6 border-b border-b-Utility-200 pb-6">

						<?php if($list_benefits_product) : ?>
						<?php foreach($list_benefits_product as $benefit) : ?>
						<div class="item flex items-center gap-2">
							<div
								class="icon body-2 font-normal rem:w-[18px] inline-flex items-center justify-center flex-shrink-0">
								<i class="fa-regular fa-<?php echo $benefit['icon']; ?>"></i>
							</div>
							<div class="title body-1 font-secondary font-light"><?php echo $benefit['content_benefits']; ?></div>
						</div>
						<?php endforeach; ?>
						<?php endif; ?>
						<?php if ( $short_description ) : ?>
							<!-- Using short description content. Assuming it might contain LIST format in HTML content or we wrap basic text. 
								 If user inputs <ul><li>, it will style automatically if global/typography matches, 
								 but here we have specific structure with icons. For now, outputting raw short desc. 
								 Ideally, this should be parsed or formatted via specific ACF field for "Key Benefits" list.
							-->
							<div class="format-content body-1 font-secondary font-light">
								<?php echo apply_filters( 'woocommerce_short_description', $short_description ); ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="wrap-info-product-price">
						<div class="price heading-6 font-bold font-secondary text-Utility-black mb-3">
							<?php echo $product->get_price_html(); ?>
						</div>
						<div class="wrap-button-add-to-cart">
							<?php woocommerce_template_single_add_to_cart(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
</section>


<?php
// Retrieve ACF Fields
$product_qualities = get_field('product_qualities');
$ingredient_focus = get_field('ingredient_focus_group');
$product_media_section = get_field('product_media_section');
$product_faqs = get_field('product_faqs');
?>

<!-- Section: Key Qualities (Icons) -->
<?php if($product_qualities): ?>
<section class="product-detail-2 relative xl:py-15 py-10">
	<div class="container-160">
		<div class="wrapper-icon grid lg:grid-cols-4 grid-cols-2 gap-base">
			<?php foreach($product_qualities as $quality): 
				$icon = $quality['icon_image']; // URL
				$text = $quality['quality_text'];
			?>
			<div class="item flex items-center justify-center flex-col">
				<div class="icon mb-6 sq-20 inline-flex items-center justify-center">
					<img class="img-svg w-full h-full" src="<?php echo esc_url($icon); ?>" alt="<?php echo esc_attr($text); ?>">
				</div>
				<div class="content text-center">
					<div class="title heading-5 font-bold font-secondary text-Utility-Black"><?php echo esc_html($text); ?></div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- Section: Ingredient Focus (Verisol) -->
<?php if($ingredient_focus && !empty($ingredient_focus['main_title'])): 
	$ing_sub = $ingredient_focus['sub_title'];
	$ing_title = $ingredient_focus['main_title'];
	$ing_desc = $ingredient_focus['description'];
	$ing_img = $ingredient_focus['focus_image'];
?>
<section class="product-detail-3 relative section-py bg-Utility-50">
	<div class="container">
		<div class="wrap-content text-center">
			<?php if($ing_sub): ?><div class="sub-title body-2 font-bold mb-3 uppercase font-secondary"><?php echo esc_html($ing_sub); ?></div><?php endif; ?>
			<h2 class="title lg:rem:text-[60px] rem:text-[40px] font-extrabold mb-8"><?php echo esc_html($ing_title); ?></h2>
			<div class="line rem:w-[57px] rem:h-[2px] bg-Utility-500 mx-auto"></div>
			<div class="format-content mt-8 body-2 font-light rem:max-w-[824px] mx-auto w-full font-secondary">
				<?php echo wp_kses_post($ing_desc); ?>
			</div>
			<?php if($ing_img): ?>
			<div class="img mt-8 rem:w-[109px] mx-auto"> 
				<a class="img-full img-ratio ratio:pt-[36_109]" href="#">
					<img src="<?php echo esc_url($ing_img); ?>" alt="<?php echo esc_attr($ing_title); ?>">
				</a>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- Section: Full Description / Content -->
<section class="product-detail-4 relative section-py">
	<div class="container">
		<h2 class="title heading-1 font-extrabold"><?php the_title(); ?> - Chi tiết sản phẩm</h2>
		<div class="button-more mt-8">
			<button class="flex items-center gap-2"><span class="font-secondary font-light body-1">Xem thêm</span>
				<div class="icon body-2 font-light"><i class="fa-regular fa-chevron-down"></i></div>
			</button>
		</div>
		<div class="wrapper-content mt-8 grid lg:grid-cols-2 grid-cols-1 gap-x-10 xl:gap-y-15 gap-y-10">
			<?php if($grid_content): ?>
			<?php foreach($grid_content as $item): ?>
			<div class="item"> 
				<h2 class="title heading-4 font-extrabold mb-4"><?php echo esc_html($item['title']); ?></h2>
				<div class="format-content mt-8 body-2 font-light font-secondary">
					<?php echo wp_kses_post($item['content']); ?>
				</div>
			</div>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>

<!-- Section: Video/Media Grid -->
<?php if($product_media_section && !empty($product_media_section['media_gallery'])): 
	$media_gallery = $product_media_section['media_gallery'];
	$side_title = $product_media_section['side_content_title'];
	$side_desc = $product_media_section['side_content_desc'];
	$side_link_text = $product_media_section['side_content_link_text'];
	$side_link_url = $product_media_section['side_content_link_url'];
?>
<section class="product-detail-5 relative">
	<div class="wrapper-main-collection">
		<div class="wrapper-list flex flex-row-reverse">
			<div class="col-left md:w-2/4 w-full">
				<div class="swiper-column-auto relative">
					<div class="swiper">
						<div class="swiper-wrapper">
							<?php foreach($media_gallery as $media): 
								$type = $media['type'];
								$img = $media['image'];
								$vid = $media['video_file'];
							?>
							<div class="swiper-slide">
								<?php if($type == 'video' && $vid): ?>
								<div class="video-item"> 
									<a class="img-ratio ratio:pt-[640_960] zoom-img h-full" href="#">
										<video class="w-full h-full object-cover" src="<?php echo esc_url($vid); ?>" autoplay muted playsinline></video>
									</a>
									<div class="play-icon"><i class="fa-solid fa-pause"></i></div>
								</div>
								<?php elseif($type == 'image' && $img): ?>
								<div class="video-item"> 
									<a class="img-ratio ratio:pt-[640_960] zoom-img h-full" href="#">
										<img class="w-full h-full object-cover" src="<?php echo esc_url($img); ?>" />
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
			</div>
			<div class="col-right md:w-2/4 w-full md:py-0 p-5 bg-Utility-50">
				<div class="wrap-content">
					<div class="wrap-content-top">
						<h3 class="title heading-2 font-extrabold mb-8"><a href="#"><?php echo esc_html($side_title ?: get_the_title()); ?></a></h3>
						<div class="desc body-2 font-normal">
							<?php echo wp_kses_post($side_desc); ?>
						</div>
					</div>
					<?php if($side_link_url): ?>
					<div class="wrap-content-bottom mt-8">
						<div class="button-more"><a class="btn btn-primary" href="<?php echo esc_url($side_link_url); ?>"><span> <?php echo esc_html($side_link_text ?: 'Khám phá sản phẩm'); ?></span></a></div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- Section: FAQ -->
<?php if($product_faqs): ?>
<div class="section-faq relative section-py">
	<div class="container">
		<div class="wrap-heading mb-base">
			<h2 class="title heading-1 font-extrabold">Câu hỏi thường gặp (FAQ)</h2>
			<div class="sub-title body-2 font-light font-secondary">Câu trả lời cho mọi thắc mắc về chăm sóc sắc đẹp.</div>
		</div>
		<div class="wrap-item-toggle flex flex-col gap-6" data-faq-list>
			<?php foreach($product_faqs as $faq): ?>
			<div class="item-toggle group transition-300">
				<div class="title flex items-center justify-between cursor-pointer transition-300">
					<div class="toggle-wrapper"><span class="heading-4 font-extrabold"><?php echo esc_html($faq['question']); ?></span></div>
					<i class="fa-light fa-plus text-xl ml-auto transition-300 text-Neutral-White"></i>
				</div>
				<div class="content" style="display: none;">
					<div class="desc mt-4 body-2 font-light font-secondary">
						<?php echo wp_kses_post($faq['answer']); ?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php if(count($product_faqs) > 5): ?>
		<div class="button-load-more-faq mt-base" data-load-more-faq>
			<button class="flex items-center gap-2"> <span class="font-secondary font-light body-1">Xem thêm</span>
				<div class="icon body-2 font-light"><i class="fa-light fa-chevron-down"></i></div>
			</button>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<!-- Section: Contact CTA -->
<section class="product-detail-6 relative section-py bg-Primary-1">
	<div class="container">
		<div class="wrap-content text-center rem:max-w-[1160px] mx-auto w-full text-white">
			<h2 class="title heading-1 font-extrabold mb-3">
				<?php $cta_product_title = get_field('cta_product_title', 'option');
					if($cta_product_title) echo $cta_product_title;
				?>
			</h2>
			<div class="format-content body-2 font-light font-secondary">
				<?php $cta_product_desc = get_field('cta_product_desc', 'option');
					if($cta_product_desc) echo $cta_product_desc;
				?>
			</div>
			<div class="button-contact mt-base"><a class="btn btn-secondary !border-white" href="#form-contact" data-fancybox><span>
				<?php $cta_product_link_text = get_field('cta_product_link_text', 'option');
					if($cta_product_link_text) echo $cta_product_link_text;
				?>
			</span></a></div>
		</div>
	</div>
</section>

<!-- Contact Form Popup -->
<div class="form-contact" id="form-contact" style="display: none;" data-fancybox-modal>
	<div class="popup-content w-full relative z-50">
		<div class="heading text-center mb-6">
			<h2 class="heading-1 text-black font-extrabold mb-8">
				<?php _e('Đăng ký tư vấn', 'canhcamtheme') ?>
			</h2>
		</div>
			<div class="form-wrapper">
				<?php 
				$contact_form_shortcode = get_field('contact_form_shortcode', 'option');
				if ( $contact_form_shortcode ) {
					echo do_shortcode( $contact_form_shortcode );
				}
				?>
			</div>
	</div>
</div>

<?php 
/**
 * Hook: woocommerce_after_single_product.
 */
do_action( 'woocommerce_after_single_product' ); 
?>

<style>
	/* Custom styles to match the precise design of ProductDetail.html if not covered by global CSS */
	.woocommerce-variation-description { display: none !important; }
	.section-product-detail .product-col-right .variations_form label { text-align: left; }
</style>