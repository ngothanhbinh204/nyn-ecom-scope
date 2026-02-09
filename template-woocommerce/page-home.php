<?php
/**
 * Template Name: Home Page
 */
get_header(); ?>

<main>
	<section class="home-1 relative">
		<div class="home-1-slide relative">
			<div class="swiper">
				<div class="swiper-wrapper">
					<?php if (have_rows('home_1_slider')): ?>
					<?php while (have_rows('home_1_slider')): the_row();
							$type = get_sub_field('type');
							$video = get_sub_field('video');
							$image = get_sub_field('image');
							$title_1 = get_sub_field('title_1');
							$title_2 = get_sub_field('title_2');
							$sub_title = get_sub_field('sub_title');
							$button = get_sub_field('button');
							?>
					<div class="swiper-slide">
						<a class="play-icon" href="#"> <i class="fa-light fa-pause"></i></a>
						<div class="home-1-banner relative">
							<a class="rem:h-[920px] block lozad"
								href="<?php echo $button ? esc_url($button['url']) : '#'; ?>">
								<?php if ($type == 'video' && $video): ?>
								<video class="w-full h-full object-cover" src="<?php echo esc_url($video); ?>" autoplay
									muted playsinline loop></video>
								<?php elseif ($image): ?>
								<img class="lozad object-cover w-full h-full"
									data-src="<?php echo esc_url($image['url']); ?>"
									alt="<?php echo esc_attr($image['alt']); ?>" />
								<?php endif; ?>
							</a>
							<div class="home-1-content">
								<div class="container-fluid">
									<div class="content-left">
										<div class="wrap-title">
											<h2 class="title-1"><?php echo esc_html($title_1); ?></h2>
											<div class="line"> </div>
											<h2 class="title-2"><?php echo esc_html($title_2); ?></h2>
										</div>
										<h3 class="sub-title body-5 font-normal"><?php echo esc_html($sub_title); ?>
										</h3>
										<?php if ($button): ?>
										<div class="wrap-button mt-base">
											<a class="btn btn-secondary" href="<?php echo esc_url($button['url']); ?>"
												target="<?php echo esc_attr($button['target'] ?: '_self'); ?>">
												<span><?php echo esc_html($button['title']); ?></span>
											</a>
										</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php endwhile; ?>
					<?php endif; ?>
				</div>
			</div>
			<div class="wrap-pagination">
				<div class="swiper-pagination"></div>
			</div>
		</div>
	</section>

	<section class="home-2 relative section-py !pb-0">
		<div class="container-160">
			<div class="wrapper grid lg:grid-cols-4 grid-cols-2 gap-base">
				<?php if (have_rows('home_2_items')): ?>
				<?php while (have_rows('home_2_items')): the_row();
						$icon = get_sub_field('icon');
						$title = get_sub_field('title');
						?>
				<div class="item flex items-center justify-center flex-col">
					<div class="icon mb-6 sq-20 inline-flex items-center justify-center">
						<?php if ($icon): ?>
						<img class="img-svg w-full h-full" src="<?php echo esc_url($icon); ?>"
							alt="<?php echo esc_attr($title); ?>">
						<?php endif; ?>
					</div>
					<div class="content text-center">
						<div class="title heading-5 font-bold font-secondary text-Utility-Black">
							<?php echo esc_html($title); ?></div>
					</div>
				</div>
				<?php endwhile; ?>
				<?php endif; ?>
			</div>
			<div class="wrapper-content text-center xl:my-20 my-10 mx-auto">
				<h2 class="title xl:rem:text-[80px] rem:text-[48px] block-title font-extrabold text-Utility-Black">
					<?php echo esc_html(get_field('home_2_main_title') ?: 'Lối sống, được chọn lọc.'); ?></h2>
				<div class="desc mt-6 heading-4 font-secondary font-normal">
					<?php echo wp_kses_post(get_field('home_2_main_desc')); ?>
				</div>
			</div>
		</div>
	</section>

	<?php
$sections = get_field('home_3_sections') ?? [];
?>

	<section class="home-3">
		<div class="container default-container-js">
			<div class="wrapper-main">
				<?php if (!empty($sections)): ?>
				<?php foreach ($sections as $index => $section): 
                    $image = $section['image'] ?? null;
                    $title = $section['title'] ?? '';
                    $desc = $section['description'] ?? '';
                    $button = $section['button'] ?? null;
                    
                    // Xác định hàng chẵn/lẻ (index bắt đầu từ 0)
                    $is_even = ($index + 1) % 2 === 0;
                    $stick_edge = $is_even ? 'right' : 'left';
                    
                    // Xử lý image
                    $img_url = $image ? esc_url($image['url']) : get_template_directory_uri() . '/template-woocommerce/img/1.jpg';
                    $img_alt = $image ? esc_attr($image['alt']) : '';
                    
                    // Xử lý button
                    $btn_url = $button ? esc_url($button['url']) : '#';
                    $btn_target = $button ? esc_attr($button['target'] ?: '_self') : '_self';
                    $btn_title = $button ? esc_html($button['title']) : '';
                ?>
				<div class="wrapper-inner flex items-center md:even:flex-row-reverse md:flex-row flex-col">
					<div class="col-left md:rem:w-[960px] w-full" data-aos="fade-right" data-aos-delay="100"
						data-aos-duration="800" stick-to-edge="<?php echo $stick_edge; ?>" unstick-min="1024">
						<div class="img">
							<a class="img-ratio ratio:pt-[640_960] zoom-img">
								<img class="lozad" data-src="<?php echo $img_url; ?>" alt="<?php echo $img_alt; ?>" />
							</a>
						</div>
					</div>
					<div class="col-right flex-1 w-full" data-aos="fade-left" data-aos-delay="200"
						data-aos-duration="800">
						<div class="wrap-content-top">
							<h3 class="title heading-2 font-extrabold mb-8">
								<a href="<?php echo $btn_url; ?>"><?php echo esc_html($title); ?></a>
							</h3>
							<div class="desc body-2 font-normal">
								<?php echo wp_kses_post($desc); ?>
							</div>
						</div>
						<div class="wrap-content-bottom">
							<?php if ($button): ?>
							<div class="button-more mt-8">
								<a class="btn btn-primary" href="<?php echo $btn_url; ?>"
									target="<?php echo $btn_target; ?>">
									<span><?php echo $btn_title; ?></span>
								</a>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="home-4 xl:py-24 xl:!pb-0 py-10">
		<div class="container">
			<h2 class="title block-title heading-1 font-extrabold text-center mb-8">
				<?php echo esc_html(get_field('home_4_title') ?: 'Những cảm nhận từ cộng đồng NYN'); ?></h2>
			<div class="swiper-column-auto relative swiper-loop autoplay">
				<div class="swiper">
					<div class="swiper-wrapper">
						<?php if (have_rows('home_4_testimonials')): ?>
						<?php while (have_rows('home_4_testimonials')): the_row();
								$content = get_sub_field('content');
								$info = get_sub_field('info');
								$star = get_sub_field('star');
								// Note: HTML uses 5 stars hardcoded. ACF does not have rating yet.
								?>
						<div class="swiper-slide">
							<div class="item bg-Utility-50 xl:py-16 xl:px-7 p-4 bpd-1 text-center">
								<div class="content-top">
									<div class="rating mb-3 text-[#FF9D34] body-4">
										<?php for ($i = 1; $i <= $star; $i++): ?>
										<i class="fa-solid fa-star"></i>
										<?php endfor; ?>
									</div>
									<div class="desc body-2 font-secondary line-clamp-2 font-normal">
										<?php echo wp_kses_post($content); ?>
									</div>
								</div>
								<div class="content-bottom mt-8 flex flex-col gap-1">
									<div class="info body-1 font-secondary font-bold"><?php echo esc_html($info); ?>
									</div>
									<!-- Hardcoded link for now as per HTML structure but no field yet -->
									<a class="more body-1 underline font-normal font-secondary" href="#">Xem thêm</a>
								</div>
							</div>
						</div>
						<?php endwhile; ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="wrap-button-slide">
					<div class="btn btn-sw-1 btn-prev"></div>
					<div class="btn btn-sw-1 btn-next"></div>
				</div>
			</div>
		</div>
	</section>

	<section class="home-5 section-py">
		<div class="container">
			<h2 class="title block-title heading-1 font-extrabold text-center mb-8">
				<?php echo esc_html(get_field('home_5_title') ?: 'Sản phẩm được chọn lọc'); ?></h2>
			<div class="swiper-column-auto relative">
				<div class="swiper">
					<div class="swiper-wrapper">
						<?php 
						$selected_products = get_field('home_5_products');
						if ($selected_products): 
							$count = 0;
							foreach ($selected_products as $post):
								setup_postdata($post);
								global $product;
								// Pass delay to template via a temporary global or global variable
								$GLOBALS['nyn_aos_delay'] = ($count % 4) * 100;
								?>
						<div class="swiper-slide">
							<?php wc_get_template_part('content', 'product'); ?>
						</div>
						<?php
								$count++;
							endforeach;
							wp_reset_postdata();
							unset($GLOBALS['nyn_aos_delay']);
						endif; ?>
					</div>
				</div>
			</div>
			<div class="sub-title mt-base xl:rem:h-[52px] p-2 bg-Utility-Black flex-center text-white font-secondary body-2 font-normal"
				data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
				<p><?php echo esc_html(get_field('home_5_bottom_text') ?: 'Sản phẩm được phát triển theo tiêu chuẩn rõ ràng · Thông tin minh bạch · Giá trị được tạo nên từ chất lượng'); ?>
				</p>
			</div>
		</div>
	</section>

	<section class="home-6 section-py !pt-0">
		<div class="container">
			<h2 class="block-title heading-1 font-extrabold text-center mb-8">
				<?php echo esc_html(get_field('home_promotion_title') ?: 'Khuyến mãi đang diễn ra'); ?></h2>
			<div class="wrapper grid lg:grid-cols-2 grid-cols-1 xl:gap-0 gap-base items-center">
				<div class="col-left">
					<div class="img">
						<a class="img-ratio ratio:pt-[466_700]" href="#">
							<?php $p_img = get_field('home_promotion_image'); ?>
							<?php if ($p_img): ?>
							<img class="lozad" data-src="<?php echo esc_url($p_img['url']); ?>"
								alt="<?php echo esc_attr($p_img['alt']); ?>" />
							<?php else: ?>
							<img class="lozad"
								data-src="<?php echo get_template_directory_uri(); ?>/template-woocommerce/img/1.jpg"
								alt="" />
							<?php endif; ?>
						</a>
					</div>
				</div>
				<div class="col-right xl:rem:pl-[120px]">
					<div class="swiper-column-auto relative swiper-loop autoplay">
						<div class="swiper">
							<div class="swiper-wrapper">
								<?php if (have_rows('home_promotion_slider')): ?>
								<?php while (have_rows('home_promotion_slider')): the_row(); 
										$title = get_sub_field('title');
										$desc = get_sub_field('description');
										$button_promotion = get_sub_field('button_promotion');
										$button_promotion_url = $button_promotion['url'];
										$button_promotion_title = $button_promotion['title'];
										$button_promotion_target = $button_promotion['target'];
										?>
								<div class="swiper-slide">
									<div class="wrap-content">
										<div class="wrap-content-top">
											<h2 class="title heading-1 font-extrabold mb-5">
												<?php echo esc_html($title); ?></h2>
											<div
												class="format-content body-2 font-normal text-Utility-Black font-secondary mb-base">
												<p><?php echo esc_html($desc); ?></p>
											</div>
										</div>
										<div class="wrap-content-bottom">
											<div class="button-more">
												<a href="<?php echo esc_url($button_promotion_url); ?>"
													class="btn btn-primary"><span><?php echo esc_html($button_promotion_title ?: 'Xem chi tiết'); ?></span></a>
											</div>
										</div>
									</div>
								</div>
								<?php endwhile; ?>
								<?php endif; ?>
							</div>
						</div>
						<div class="wrapper-bottom-button flex items-center gap-5 mt-base">
							<div class="arrow-button flex items-center gap-5">
								<div class="btn btn-sw-1 btn-prev"></div>
								<div class="btn btn-sw-1 btn-next"></div>
							</div>
							<div class="swiper-pagination"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="home-7 xl:rem:pb-[96px] pb-10">
		<div class="container">
			<h2 class="title block-title heading-1 font-extrabold text-center mb-8">
				<?php echo esc_html(get_field('home_6_title') ?: 'Cam kết từ NYN'); ?></h2>
			<div class="wrapper-list grid lg:grid-cols-4 grid-cols-2 gap-6">
				<?php if (have_rows('home_6_items')): ?>
				<?php $delay = 0; while (have_rows('home_6_items')): the_row();
						$icon = get_sub_field('icon');
						$title = get_sub_field('title');
						?>
				<div class="item flex items-center justify-center flex-col text-center" data-aos="fade-up"
					data-aos-delay="<?php echo $delay; ?>" data-aos-duration="1000">
					<div class="icon sq-16 inline-flex items-center justify-center">
						<?php if ($icon): ?>
						<img class="img-svg w-full h-full" src="<?php echo esc_url($icon); ?>"
							alt="<?php echo esc_attr($title); ?>">
						<?php else: ?>
						<img class="img-svg w-full h-full"
							src="<?php echo get_template_directory_uri(); ?>/template-woocommerce/img/icon.svg" alt="">
						<?php endif; ?>
					</div>
					<div class="content mt-8 xl:px-12">
						<div class="title heading-5 font-normal font-secondary"><?php echo esc_html($title); ?></div>
					</div>
				</div>
				<?php $delay += 100; endwhile; ?>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php
$gr_left = get_field('gr_content_left');
$gr_right = get_field('gr_content_right');

$left_heading = $gr_left['home_7_heading'] ?? 'Kết nối cùng NYN';
$left_desc = $gr_left['home_7_desc'] ?? '';
$left_quote = $gr_left['home_7_quote'] ?? 'NYN tin vào sự kết nối – không đại trà, mà có chọn lọc.';
$left_contacts = $gr_left['home_7_contacts'] ?? [];

$right_heading = $gr_right['home_8_heading_right'] ?? 'Kết nối cùng NYN';
$right_desc = $gr_right['home_8_desc_right'] ?? 'Team NYN luôn sẵn sàng lắng nghe và tư vấn';
$right_time = $gr_right['home_8_time_right'] ?? 'Thứ 2 – CN: 8:00 - 21:00';
$right_contacts = $gr_right['home_8_contacts_right'] ?? [];

$brands = ['facebook', 'twitter', 'instagram', 'youtube', 'linkedin', 'google', 'pinterest', 'tiktok', 'snapchat', 'reddit', 'tumblr', 'whatsapp', 'telegram'];
?>

	<section class="home-8 overflow-hidden xl:rem:py-[49px] pt-10">
		<div class="container">
			<div class="wrapper grid md:grid-cols-2 grid-cols-1">
				<div class="col-left item-slide-left xl:rem:pr-[120px]" data-aos="fade-right" data-aos-delay="100"
					data-aos-duration="800">
					<div class="content text-center">
						<div class="content-top">
							<div class="wrap-heading">
								<h3 class="title heading-1 font-extrabold mb-8"><?php echo esc_html($left_heading); ?>
								</h3>
								<div class="desc body-2 font-secondary font-normal">
									<p><?php echo wp_kses_post($left_desc); ?></p>
								</div>
							</div>
							<div class="wrap-bottom mt-8">
								<div class="title heading-5 font-normal font-secondary">
									<?php echo esc_html($left_quote); ?></div>
								<div class="wrap-social flex items-center justify-center gap-8 mt-1">
									<?php if (!empty($left_contacts)): ?>
									<?php foreach ($left_contacts as $contact): 
                                        $icon = $contact['icon'] ?? '';
                                        $link = $contact['link'] ?? null;
                                        if ($link):
                                            $is_brand = false;
                                            foreach ($brands as $brand) {
                                                if (strpos($icon, $brand) !== false) {
                                                    $is_brand = true;
                                                    break;
                                                }
                                            }
                                        ?>
									<div class="item flex items-center gap-2 text-Utility-Black font-bold">
										<div class="icon">
											<i
												class="fa<?php echo $is_brand ? '-brands' : '-solid'; ?> fa-<?php echo esc_attr($icon); ?>"></i>
										</div>
										<a href="<?php echo esc_url($link['url']); ?>"
											target="<?php echo esc_attr($link['target'] ?? '_self'); ?>"><?php echo esc_html($link['title']); ?></a>
									</div>
									<?php endif; ?>
									<?php endforeach; ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-right" data-aos="fade-left" data-aos-delay="200" data-aos-duration="800">
					<div class="content text-center">
						<div class="content-top">
							<div class="wrap-heading">
								<h3 class="title heading-1 font-extrabold mb-8"><?php echo esc_html($right_heading); ?>
								</h3>
								<div class="desc body-2 font-secondary font-normal">
									<p><?php echo esc_html($right_desc); ?></p>
								</div>
							</div>
							<div class="wrap-time-open body-2 font-normal font-secondary mt-1">
								<div class="label">Thời gian hỗ trợ:</div>
								<p><?php echo esc_html($right_time); ?></p>
							</div>
							<div class="wrap-bottom mt-8">
								<div class="wrap-social flex items-center justify-center gap-8 mt-1">
									<?php if (!empty($right_contacts)): ?>
									<?php foreach ($right_contacts as $contact): 
                                        $icon = $contact['icon'] ?? '';
                                        $link = $contact['link'] ?? null;
                                        if ($link):
                                            $is_brand = false;
                                            foreach ($brands as $brand) {
                                                if (strpos($icon, $brand) !== false) {
                                                    $is_brand = true;
                                                    break;
                                                }
                                            }
                                        ?>
									<div class="item flex items-center gap-2 text-Utility-Black font-bold">
										<div class="icon">
											<i
												class="fa<?php echo $is_brand ? '-brands' : '-solid'; ?> fa-<?php echo esc_attr($icon); ?>"></i>
										</div>
										<a href="<?php echo esc_url($link['url']); ?>"
											target="<?php echo esc_attr($link['target'] ?? '_self'); ?>"><?php echo esc_html($link['title']); ?></a>
									</div>
									<?php endif; ?>
									<?php endforeach; ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>