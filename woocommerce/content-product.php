<?php
defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
	return;
}

// Get the product ID
$product_id = $product->get_id();

// SKU and Variation logic (kept as requested)
$sku = '';
if ($product->is_type('variable')) {
    $default_attributes = $product->get_default_attributes();
    if (!empty($default_attributes)) {
        $variation_id = $product->get_visible_children()[0];
        foreach ($product->get_available_variations() as $variation_array) {
            $match = true;
            foreach ($default_attributes as $attribute_name => $attribute_value) {
                $variation_attribute = 'attribute_' . $attribute_name;
                if (isset($variation_array['attributes'][$variation_attribute]) && $variation_array['attributes'][$variation_attribute] !== $attribute_value) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $variation_id = $variation_array['variation_id'];
                break;
            }
        }
        $variation = wc_get_product($variation_id);
        if ($variation && $variation->get_sku()) {
            $sku = $variation->get_sku();
        }
    }
}
if (empty($sku)) {
    $sku = $product->get_sku();
}

// Logic for custom AOS from global
$aos_delay = isset($GLOBALS['nyn_aos_delay']) ? $GLOBALS['nyn_aos_delay'] : 0;
?>

<div <?php wc_product_class('product-item', $product); ?> data-aos="fade-left" data-aos-delay="<?php echo esc_attr($aos_delay); ?>" data-aos-duration="1000">
    <div class="img"> 
        <a class="img-ratio ratio:pt-[420_320] zoom-img" href="<?php echo get_permalink($product_id); ?>">
            <?php if (has_post_thumbnail($product_id)) : ?>
                <img class="lozad" data-src="<?php echo get_the_post_thumbnail_url($product_id, 'full'); ?>" alt="<?php the_title_attribute(); ?>" />
            <?php else : ?>
                <img class="lozad" data-src="<?php echo get_template_directory_uri(); ?>/template-woocommerce/img/product-1.png" alt="<?php the_title_attribute(); ?>" />
            <?php endif; ?>
        </a>
    </div>
    <div class="content mt-5">
        <h3 class="title body-2 font-bold font-secondary mb-4"> 
            <a href="<?php echo get_permalink($product_id); ?>">
                <?php echo $product->get_name(); ?>
            </a>
        </h3>

        <?php if ($sku) : ?>
            <div class="sku hidden"><?php echo esc_html($sku); ?></div>
        <?php endif; ?>

        <div class="wrap-price font-secondary flex items-center gap-2 mb-4">
            <?php if ( $product->is_on_sale() ) : ?>
                <span class="price-new body-2 font-secondary font-bold"><?php echo wc_price( $product->get_sale_price() ); ?></span>
                <span class="price-old font-bold body-1 font-secondary text-Utility-500 line-through"><?php echo wc_price( $product->get_regular_price() ); ?></span>
            <?php else : ?>
                <span class="price-new body-2 font-secondary font-bold"><?php echo $product->get_price_html(); ?></span>
            <?php endif; ?>
        </div>
        
        <div class="button-add-to-cart">
            <?php 
            $add_to_cart_url = $product->add_to_cart_url();
            $label = $product->add_to_cart_text();
            ?>
            <a href="<?php echo esc_url($add_to_cart_url); ?>" class="btn btn-add-cart ajax_add_to_cart add_to_cart_button" data-product_id="<?php echo $product_id; ?>" data-product_sku="<?php echo $sku; ?>">
                <span><?php echo esc_html($label); ?></span>
                <div class="icon"> <i class="fa-thin fa-cart-shopping"></i></div>
            </a>
        </div>
    </div>
</div>