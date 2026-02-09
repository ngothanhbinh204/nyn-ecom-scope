    </main>
    <footer class="footer bg-Utility-100">
        <div class="footer-top xl:py-16 py-10">
            <div class="container">
                <div class="footer-wrapper grid lg:grid-cols-[4fr_1.4fr_3.5fr_2.6fr] md:grid-cols-2 grid-cols-1 gap-base">
                    <div class="footer-column">
                        <div class="footer-logo rem:w-[358px] mb-12">
                            <a class="img-ratio ratio:pt-[108_358]" href="<?php echo home_url(); ?>">
                                <?php 
                                $footer_logo = get_field('footer_logo', 'options');
                                if ($footer_logo) : ?>
                                    <img class="lozad" data-src="<?php echo esc_url($footer_logo['url']); ?>" alt="<?php echo esc_attr($footer_logo['alt']); ?>" />
                                <?php else : ?>
                                    <img class="lozad" data-src="<?php echo get_template_directory_uri(); ?>/template-woocommerce/img/logo-black.png" alt="" />
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="footer-slogan body-2 font-bold font-secondary rem:h-[52px] bg-Utility-Black px-6 inline-flex items-center justify-center text-white">
                            <p><?php echo get_field('footer_slogan', 'options') ?: 'Lối sống là dấu ấn cá nhân.'; ?></p>
                        </div>
                    </div>
                    <div class="footer-column">
                        <h3 class="title heading-5 font-secondary mb-4 font-bold"><?php echo get_field('footer_col_2_title', 'options') ?: 'Liên hệ'; ?></h3>
                        <?php 
                        wp_nav_menu(array(
                            'theme_location' => 'footer-menu-1',
                            'container'      => false,
                            'menu_class'     => 'footer-menu',
                        ));
                        ?>
                    </div>
                    <div class="footer-column">
                        <h3 class="title heading-5 font-bold font-secondary mb-4"><?php echo get_field('footer_col_3_title', 'options') ?: 'Liên hệ'; ?></h3>
                        <?php 
                        wp_nav_menu(array(
                            'theme_location' => 'footer-menu-2',
                            'container'      => false,
                            'menu_class'     => 'footer-menu',
                        ));
                        ?>
                    </div>
                    <div class="footer-column">
                        <h3 class="title heading-5 font-bold font-secondary mb-4"><?php echo get_field('footer_col_4_title', 'options') ?: 'Liên hệ'; ?></h3>
                        <div class="cta font-normal body-2 font-secondary">
                            <p><?php echo get_field('footer_col_4_desc', 'options') ?: 'Cùng NYN xây dựng một cộng đồng đề cao chất lượng, trải nghiệm được chọn lọc và cảm hứng sống hiện đại.'; ?></p>
                        </div>
                        <div class="footer-social xl:mt-12 mt-6">
                            <div class="label heading-5 font-bold font-secondary mb-6">Theo dõi chúng tôi:</div>
                            <ul class="footer-social-menu">
                                <?php if (have_rows('footer_socials', 'options')) : ?>
                                    <?php while (have_rows('footer_socials', 'options')) : the_row(); 
                                        $link = get_sub_field('link');
                                        $icon = get_sub_field('icon');
                                        $icon_class = (strpos($icon, 'fa-') === 0) ? $icon : 'fa-' . $icon;
                                        ?>
                                        <li> 
                                            <?php if ($link) : ?>
                                            <a href="<?php echo esc_url($link['url']); ?>" target="<?php echo esc_attr($link['target'] ?: '_self'); ?>">
                                                <i class="fa-brands <?php echo esc_attr($icon_class); ?>"></i>
                                            </a>
                                            <?php endif; ?>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <li> <a href="#"><i class="fa-brands fa-facebook-f"></i></a></li>
                                    <li> <a href="#"><i class="fa-brands fa-instagram"></i></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom py-5 border-t border-t-Utility-200 bg-Utility-100">
            <div class="container">
                <div class="footer-bottom-wrapper flex items-center justify-between md:flex-row flex-col text-Utility-Black body-4 font-secondary">
                    <div class="footer-copyright font-light md:text-left text-center">
                        <p><?php echo get_field('footer_copyright', 'options') ?: 'Copyright © 2025 Bản quyền thuộc về NYN.'; ?></p>
                    </div>
                    <div class="footer-policy">
                        <?php 
                        wp_nav_menu(array(
                            'theme_location' => 'footer-policy-menu',
                            'container'      => false,
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="tool-fixed-cta">
            <div class="btn button-to-top">
                <div class="btn-icon">
                    <div class="icon"> </div>
                </div>
            </div>
            <?php if (have_rows('footer_fixed_socials', 'options')) : ?>
                <?php while (have_rows('footer_fixed_socials', 'options')) : the_row(); 
                    $link = get_sub_field('link');
                    $icon = get_sub_field('icon');
                    $content = $link['title'];
                    
                    // Determine icon prefix
                    $prefix = 'fa-light';
                    if (strpos($icon, 'facebook') !== false || strpos($icon, 'messenger') !== false) {
                        $prefix = 'fa-brands';
                    }
                    ?>
                    <?php if ($link) : ?>
                    <a class="btn btn-content <?php echo (strpos($icon, 'message') !== false || strpos($icon, 'messenger') !== false) ? 'btn-social' : ''; ?>" 
                       href="<?php echo esc_url($link['url']); ?>" 
                       target="<?php echo esc_attr($link['target'] ?: '_self'); ?>">
                        <div class="btn-icon">
                            <div class="icon"><i class="<?php echo $prefix; ?> <?php echo esc_attr($icon); ?>"></i></div>
                        </div>
                        <div class="content"><?php echo esc_html($content); ?></div>
                    </a>
                    <?php endif; ?>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </footer>
    <?php wp_footer(); ?>
    <?php echo get_field('field_config_body', 'options'); ?>
</body>

</html>