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

// Image Logic
$thumbnail_id = $product->get_image_id();
$main_image_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'full') : wc_placeholder_img_src();

// Hover Image Logic
$hover_image = get_field('image_on_hover', $product_id);
$hover_image_url = '';

if ($hover_image) {
    if (is_array($hover_image) && isset($hover_image['url'])) {
        $hover_image_url = $hover_image['url'];
    } elseif (is_string($hover_image)) {
        $hover_image_url = $hover_image;
    } elseif (is_numeric($hover_image)) {
         $hover_image_url = wp_get_attachment_image_url($hover_image, 'full');
    }
}

// Fallback to main image if no hover image is set
if (empty($hover_image_url)) {
    $hover_image_url = $main_image_url;
}

?>

<div <?php wc_product_class('product-item', $product); ?> data-aos="fade-left" data-aos-delay="<?php echo esc_attr($aos_delay); ?>" data-aos-duration="1000">
    <div class="img">
        <a class="img-ratio ratio:pt-[358_320] zoom-img main-img" href="<?php echo get_permalink($product_id); ?>">
            <img class="lozad" data-src="<?php echo esc_url($main_image_url); ?>" alt="<?php the_title_attribute(); ?>" />
        </a>
        <div class="img-hover">
            <a class="img-ratio ratio:pt-[358_320] zoom-img hover-img" href="<?php echo get_permalink($product_id); ?>">
                <img class="lozad" data-src="<?php echo esc_url($hover_image_url); ?>" alt="<?php the_title_attribute(); ?>" />
            </a>
        </div>
    </div>
    
    <div class="content mt-5">
        <h3 class="title body-2 font-medium font-secondary mb-4">
            <a href="<?php echo get_permalink($product_id); ?>">
                <?php echo $product->get_name(); ?>
            </a>
        </h3>

        <?php if ($sku) : ?>
            <div class="sku hidden"><?php echo esc_html($sku); ?></div>
        <?php endif; ?>

        <div class="wrap-price flex items-center gap-2 mb-4">
            <?php if ( $product->is_on_sale() ) : ?>
                <span class="price-new body-1 font-bold"><?php echo wc_price( $product->get_sale_price() ); ?></span>
                <span class="price-old font-normal text-Utility-500 line-through"><?php echo wc_price( $product->get_regular_price() ); ?></span>
            <?php else : ?>
                <span class="price-new body-1 font-bold"><?php echo wc_price( $product->get_price() ); ?></span>
            <?php endif; ?>
        </div>
        
        <div class="button-add-to-cart">
            <?php 
            $add_to_cart_url = $product->add_to_cart_url();
            $label = $product->add_to_cart_text();
            
            // Determine additional classes
            $args = array(); 
            // Check if product is simple to add ajax class usually handled by woo, but let's replicate
            $class = 'btn btn-add-cart';
            if ( $product->is_purchasable() && $product->is_in_stock() ) {
                $class .= ' add_to_cart_button';
                if ( $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ) {
                   $class .= ' ajax_add_to_cart';
                }
            }
            ?>
            <a href="<?php echo esc_url($add_to_cart_url); ?>" class="<?php echo esc_attr($class); ?>" data-product_id="<?php echo $product_id; ?>" data-product_sku="<?php echo $sku; ?>" aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>" rel="nofollow">
                <span><?php echo esc_html($label); ?></span>
                <div class="icon"> <i class="fa-thin fa-cart-shopping"></i></div>
            </a>
        </div>
    </div>
</div>


