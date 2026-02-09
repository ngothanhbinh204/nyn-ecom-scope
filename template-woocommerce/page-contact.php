<?php
/**
 * Template Name: Contact Page
 */
get_header();

// Retrieve ACF fields
$page_title = get_field('page_title');
$contact_list = get_field('contact_list');
$contact_form_intro = get_field('contact_form_intro');
$contact_form_shortcode = get_field('contact_form_shortcode');
?>

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
<section class="contact section-py">
    <div class="container">
        <div class="contact-main flex flex-col lg:flex-row gap-20">
            <!-- Left Column: Contact Information -->
            <div class="col-left lg:rem:w-[480px] w-full">
                <?php if ($page_title): ?>
                    <h2 class="heading-1 text-Primary-2 mb-6 uppercase"><?php echo esc_html($page_title); ?></h2>
                <?php endif; ?>
                
                <div class="contact-box">
                    <div class="contact-list flex flex-col gap-5">
                        <?php if ($contact_list): ?>
                            <?php foreach ($contact_list as $item): 
                                $icon = $item['icon'];
                                $title = $item['title'];
                                $content = $item['content'];
                                $link = $item['link'];
                            ?>
                                <div class="contact-item">
                                    <span class="text-lg text-Utility-Black">
                                        <i class="fa-regular fa-<?php echo esc_attr($icon); ?>"></i>
                                    </span>
                                    <div class="contact-wrap flex flex-col gap-2">
                                        <?php if ($title): ?>
                                            <div class="top font-bold">
                                                <p><?php echo esc_html($title); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <div class="bottom font-normal text-Neutral-500">
                                            <?php if ($link): ?>
                                                <a href="<?php echo esc_url($link); ?>"><?php echo wp_kses_post($content); ?></a>
                                            <?php else: ?>
                                                <?php echo wp_kses_post($content); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php 
                        // Display Google Map if available
                        $google_map = get_field('google_map');
                        if ($google_map): 
                        ?>
                            <div class="map">
                                <iframe 
                                    src="<?php echo esc_url($google_map); ?>"
                                    width="800" 
                                    height="600" 
                                    style="border:0;" 
                                    allowfullscreen 
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Contact Form -->
            <div class="col-right flex-1 lg:p-12 p-5 bg-Utility-50 w-full">
                <?php if ($contact_form_intro): ?>
                    <div class="title body-1 text-center font-normal">
                        <?php echo wp_kses_post($contact_form_intro); ?>
                    </div>
                <?php endif; ?>
                
                <div class="my-8">
                    <?php if ($contact_form_shortcode): ?>
                        <?php echo do_shortcode($contact_form_shortcode); ?>
                    <?php else: ?>
                        <!-- Fallback form if no CF7 shortcode -->
                        <form class="contact-form-fallback" action="">
                            <div class="wrap-form grid grid-cols-2 gap-5">
                                <div class="form-group">
                                    <input type="text" name="name" placeholder="Họ và tên">
                                </div>
                                <div class="form-group">
                                    <input type="email" name="email" placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <input type="tel" name="phone" placeholder="Số điện thoại">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="subject" placeholder="Tiêu đề">
                                </div>
                                <div class="form-group textarea w-full col-span-full">
                                    <textarea name="message" placeholder="Nội dung"></textarea>
                                </div>
                            </div>
                            <div class="form-submit mt-5 flex-center">
                                <button class="btn btn-primary" type="submit">
                                    <span>Gửi</span>
                                    <div class="icon">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </div>
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
</main>

<?php get_footer(); ?>
