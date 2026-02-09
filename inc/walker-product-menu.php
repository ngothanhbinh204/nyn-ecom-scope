<?php
class Header_Menu_Walker extends Walker_Nav_Menu
{
	private $tabs_data = [];

	function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( $depth === 0 ) {
			$indent = str_repeat("\t", $depth);
			$output .= "\n$indent<div class=\"dropdown-product-container\">\n";
			$output .= "$indent    <div class=\"close-megamenu\"><i class=\"fa-light fa-xmark\"></i></div>\n";
			$output .= "$indent    <div class=\"column-left\">\n";
			$output .= "$indent        <div class=\"dropdown-product-title\">Sản phẩm</div>\n"; 
			$output .= "$indent        <div class=\"nav-solution-product\">\n";
			$output .= "$indent            <ul>\n";
		}
	}

	// End Level
	function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( $depth === 0 ) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent            </ul>\n";
			$output .= "$indent        </div>\n"; 
			$output .= "$indent    </div>\n"; 
			$output .= "$indent    <div class=\"column-right\">\n";
			
			foreach ($this->tabs_data as $index => $tab) {
				$args_prod = array(
					'post_type'      => 'product',
					'posts_per_page' => 4,
					'status'         => 'publish',
					'tax_query'      => array(
						array(
							'taxonomy' => 'product_cat',
							'field'    => 'term_id',
							'terms'    => $tab['term_id'],
						),
					),
				);
				
				$products = new WP_Query($args_prod);

				$output .= '<div class="tabslet-content-menu" id="'.$tab['id'].'" data-tab="#'.$tab['id'].'">';
				$output .= '<div class="tabslet-content-menu-lists">';
				
				if ($products->have_posts()) {
				while ($products->have_posts()) {
					$products->the_post();

					$product_id = get_the_ID();

					$img_on_menu = get_field('image_on_menu', $product_id);

					if (is_array($img_on_menu)) {
						$img_url = $img_on_menu['sizes']['medium'] ?? $img_on_menu['url'];
					} elseif (is_numeric($img_on_menu)) {
						$img_url = wp_get_attachment_image_url($img_on_menu, 'medium');
					} else {
						$img_url = '';
					}

					if (!$img_url) {
						$img_url = get_the_post_thumbnail_url($product_id, 'medium');
					}

					if (!$img_url && function_exists('wc_placeholder_img_src')) {
						$img_url = wc_placeholder_img_src();
					}

					$output .= '<div class="product-item">';
					$output .= '<div class="img">
									<a class="img-ratio" href="'.get_permalink().'">
										<img class="lozad" data-src="'.esc_url($img_url).'" alt="'.esc_attr(get_the_title()).'" />
									</a>
								</div>';
					$output .= '<div class="content">
									<h3 class="title">
										<a href="'.get_permalink().'">'.esc_html(get_the_title()).'</a>
									</h3>
								</div>';
					$output .= '</div>';
				}
				wp_reset_postdata();
			} else {
				$output .= '<div class="product-item"><div class="content"><p>Chưa có sản phẩm</p></div></div>';
			}

				
				$output .= '</div></div>'; 
			}
			
			$output .= "$indent    </div>\n"; 
			$output .= "$indent</div>\n";
			
			$this->tabs_data = [];
		}
		// Depth 1 -> 2: No </ul> to close
	}

	// Start Element
	function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$indent = ($depth) ? str_repeat("\t", $depth) : '';
		$classes = empty($item->classes) ? array() : (array) $item->classes;
		
		if ( $depth === 0 ) {
			// Level 0: Main Menu Item (e.g. "Sản phẩm")
			if (in_array('menu-item-has-children', $classes)) {
				$classes[] = 'dropdown-product';
			}
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
			$output .= $indent . '<li class="' . esc_attr( $class_names ) . '">';
			$output .= '<a href="' . esc_attr( $item->url ) . '">' . apply_filters( 'the_title', $item->title, $item->ID ) . '</a>';
			
		} elseif ( $depth === 1 ) {
			// Level 1: Group Title (e.g. "Supplements", "Home Fragrance")
			// This item wraps its own children visually
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
			$output .= $indent . '<li class="' . esc_attr( $class_names ) . '">';
			$output .= '<div class="name-category">' . apply_filters( 'the_title', $item->title, $item->ID ) . '</div>';
			
		} elseif ( $depth === 2 ) {
			// Level 2: Actual Clickable Categories (e.g. "NYN Collagen", "Reed Diffuser")
			// These trigger the tabs on the right
			
			$tab_id = 'tab-' . $item->ID;
			
			$output .= $indent . '<a href="' . esc_attr( $item->url ) . '" data-tab="#' . $tab_id . '"><span>' . apply_filters( 'the_title', $item->title, $item->ID ) . '</span></a>';
			
			// Collect data for Right Column if this is a Product Category
			if ( $item->object == 'product_cat' ) {
				$this->tabs_data[] = [
					'id'      => $tab_id,
					'term_id' => $item->object_id,
					'title'   => $item->title
				];
			}
		}
	}

	// End Element
	function end_el( &$output, $item, $depth = 0, $args = null ) {
		// We only close LI for Depth 0 and Depth 1. Depth 2 items are just <a> tags inside Depth 1 LI.
		if ( $depth === 0 || $depth === 1 ) {
			$output .= "</li>\n";
		}
	}
}
