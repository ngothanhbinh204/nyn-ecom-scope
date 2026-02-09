<?php

function log_dump($data)
{
	// Use the PHP ob_start function to capture the output of the var_dump function
	ob_start();
	var_dump($data);
	$dump = ob_get_clean();

	// Use the PHP highlight_string function to highlight the syntax
	$highlighted = highlight_string("<?php\n" . $dump . "\n?>", true);

	// Remove the PHP tags and wrap the highlighted code in a <pre> tag
	$formatted = '<pre>' . substr($highlighted, 27, -8) . '</pre>';

	// Add custom CSS styles for the .php and .hlt classes
	$custom_css = 'pre {position: static;
		background: #ffffff80;
		// max-height: 50vh;
		width: 100vw;
	}
	pre::-webkit-scrollbar{
	width: 1rem;}';

	// Wrap the custom CSS in a <style> tag
	$formatted_css = '<style>' . $custom_css . '</style>';
	echo ($formatted_css . $formatted);
}

function empty_content($str)
{
	return trim(str_replace('&nbsp;', '', strip_tags($str, '<img>'))) == '';
}

add_action('facetwp_scripts', function () {
?>
	<script>
		(function($) {
			// On start of the facet refresh, but not on first page load
			$(document).on('facetwp-refresh', function() {
				if (FWP.loaded) {
					$('.facetwp-template').prepend('<div class="loading-text"><span class="loader"></span></div>');
					$('html, body').animate({
						scrollTop: $('section.section-product-list.list-product').offset().top - 200
					}, 500);
				}
			});

			// On finishing the facet refresh
			$(document).on('facetwp-loaded', function() {
				$('.facetwp-template .loading-text').remove();
				window.lozad.observe()
			});

		})(jQuery);
		(function($) {
			if (window.matchMedia('(max-width: 1023.98px)').matches) {
				// Disable auto-refresh for all facets
				// document.addEventListener('facetwp-loaded', function() {
				// 	FWP.auto_refresh = false;
				// });

				// Handle the Apply button click
				document.querySelector('.list-filter-product .filter').addEventListener('click', function() {
					$('.list-filter-product').removeClass('active');
					FWP.refresh();
					console.log('End Filter');
					// Scroll to product list section with offset
					$('html, body').animate({
						scrollTop: $('section.section-product-list.list-product').offset().top - 200
					}, 500);
				});
			}
		})(jQuery);
		(function($) {
			$(document).on('change', '.wrap-filter-select .facetwp-type-sort select', function() {
				FWP.refresh();
			});
		})(fUtil);
	</script>
	<style>
		.facetwp-template {
			position: relative;
		}

		.loading-text {
			display: flex;
			padding-top: 50px;
			height: 100%;
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			z-index: 10;
			background-color: #fff;
			border-radius: 20px;
			backdrop-filter: blur(10px);
		}

		@media (max-width: 1023.98px) {
			.loading-text {
				position: fixed;
				top: 0;
				left: 0;
				width: 100vw;
				height: 100vh;
				z-index: 10;
				background-color: #fff;
				border-radius: 0;
				display: flex;
				align-items: center;
				justify-content: center;
			}
		}

		.loader {
			width: 48px;
			height: 48px;
			display: block;
			margin: 15px auto;
			position: relative;
			color: #FFF;
			box-sizing: border-box;
			animation: rotation 1s linear infinite;
		}

		.loader::after,
		.loader::before {
			content: '';
			box-sizing: border-box;
			position: absolute;
			width: 24px;
			height: 24px;
			top: 50%;
			left: 50%;
			transform: scale(0.5) translate(0, 0);
			background-color: #222222;
			border-radius: 50%;
			animation: animloader 1s infinite ease-in-out;
		}

		.loader::before {
			background-color: #b72126;
			transform: scale(0.5) translate(-48px, -48px);
		}

		@keyframes rotation {
			0% {
				transform: rotate(0deg);
			}

			100% {
				transform: rotate(360deg);
			}
		}

		@keyframes animloader {
			50% {
				transform: scale(1) translate(-50%, -50%);
			}
		}
	</style>
<?php
}, 100);
?>
<?php
add_filter('shortcode_atts_wpcf7', 'custom_shortcode_atts_wpcf7_filter', 10, 3);
function custom_shortcode_atts_wpcf7_filter($out, $pairs, $atts)
{
	$my_attr = 'vi_tri';

	if (isset($atts[$my_attr])) {
		$out[$my_attr] = $atts[$my_attr];
	}

	return $out;
}

add_filter('acf/load_field/key=field_tab_products', 'load_just_categories_filter', 12, 1);
function load_just_categories_filter($field)
{
	$field['taxonomy'] = [];

	// Get top-level categories
	$parent_terms = get_terms(array(
		'taxonomy' => 'product_cat',
		'hide_empty' => false,
		'parent' => 0
	));

	// Process categories recursively
	if (!empty($parent_terms)) {
		foreach ($parent_terms as $term) {
			// Add parent category
			$field['taxonomy'][] = 'product_cat:' . $term->slug;

			// Process child categories recursively
			get_child_terms_recursive($term->term_id, $field['taxonomy']);
		}
	}

	return $field;
}

/**
 * Recursively get all child terms for a parent term and add them to the taxonomy array
 * 
 * @param int $parent_id The parent term ID
 * @param array &$taxonomy_array Reference to the taxonomy array to add terms to
 */
function get_child_terms_recursive($parent_id, &$taxonomy_array)
{
	$children = get_terms(array(
		'taxonomy' => 'product_cat',
		'hide_empty' => false,
		'parent' => $parent_id
	));

	if (!empty($children)) {
		foreach ($children as $child) {
			// Add the child term to the taxonomy array
			$taxonomy_array[] = 'product_cat:' . $child->slug;

			// Recursively get this child's children
			get_child_terms_recursive($child->term_id, $taxonomy_array);
		}
	}
}

function custom_woocommerce_select2_locale()
{
	// Replace 'fr' with your language code (e.g., 'de', 'es', 'it', etc.)
	$locale = 'vi';

	// Enqueue the Select2 translation file
	wp_enqueue_script(
		'select2-locale',
		'//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/' . $locale . '.js',
		array('jquery', 'select2'),
		'4.0.13',
		true
	);

	// Add script to initialize Select2 with the locale
	wp_add_inline_script('select2-locale', '
        jQuery(document).ready(function($) {
            // Set the language for all future Select2 instances
            $.fn.select2.defaults.set("language", "' . $locale . '");
            // Reinitialize existing Select2 fields
            // $(".select2-hidden-accessible").select2();
            // $(".select2").select2();
        });
    ');
}
add_action('wp_enqueue_scripts', 'custom_woocommerce_select2_locale', 100);
function disable_all_form_autocomplete()
{
?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			// More aggressive approach
			function disableAutocomplete() {
				// Apply to forms
				$('form').attr('autocomplete', 'off').attr('autocorrect', 'off').attr('autocapitalize', 'off').attr('spellcheck', 'false');

				// Apply to all input elements
				// $('input, select, textarea').each(function() {
				// 	$(this)
				// 		// .attr('autocomplete', 'new-password') // More effective than 'off'
				// 		.attr('autocorrect', 'off')
				// 		.attr('autocapitalize', 'off')
				// 		.attr('spellcheck', 'false')
				// });
			}

			// Run on page load
			disableAutocomplete();

			// Add CSS to hide autocomplete suggestions
			$('head').append('<style>\
            input:-webkit-autofill,\
            input:-webkit-autofill:hover,\
            input:-webkit-autofill:focus,\
            input:-webkit-autofill:active {\
                -webkit-box-shadow: 0 0 0 30px white inset !important;\
                -webkit-text-fill-color: inherit !important;\
                transition: background-color 5000s ease-in-out 0s;\
            }\
        </style>');
		});
	</script>
<?php
}
add_action('wp_footer', 'disable_all_form_autocomplete', 99);

add_filter('shortcode_atts_wpcf7', 'custom_shortcode_atts_wpcf7_filter_file_pdf_url', 10, 3);
function custom_shortcode_atts_wpcf7_filter_file_pdf_url($out, $pairs, $atts)
{
	$my_attr = 'file-pdf-url';

	if (isset($atts[$my_attr])) {
		$out[$my_attr] = $atts[$my_attr];
	}

	return $out;
}

function pdf_button_shortcode($atts)
{
	$atts = shortcode_atts(array(
		'pdf_id' => ''
	), $atts);

	$pdf_id = $atts['pdf_id'];
	$url_page = get_page_link_by_template('template_3ds/page-dang-ky-tai-lieu.php');
	$url = $url_page . '?pdf_id=' . esc_attr($pdf_id);
	return '<button class="btn_pdf_in_3d" onclick="window.open(\'' . esc_url($url) . '\',\'_blank\')">' . __('Tải PDF', 'canhcamtheme') . '</button>';
}
add_shortcode('pdf_button', 'pdf_button_shortcode');


add_filter('facetwp_i18n', function ($string) {
	if (isset(FWP()->facet->http_params['lang'])) {
		$lang = FWP()->facet->http_params['lang'];
		$translations = [];
		$translations['vi']['Any'] = 'Tất cả';
		$translations['vi']['Enter keywords'] = 'Nhập từ khóa';
		$translations['vi']['Sort from (z-a)'] = 'Sắp xếp từ (z-a)';
		$translations['vi']['Sort from (a-z)'] = 'Sắp xếp từ (a-z)';
		$translations['vi']['Price from Low to High'] = 'Giá từ thấp đến cao';
		$translations['vi']['Price from High to Low'] = 'Giá từ cao đến thấp';

		if (isset($translations[$lang][$string])) {
			return $translations[$lang][$string];
		}
	}

	return $string;
});


/**
 * Custom validation for phone number length in Contact Form 7.
 *
 * Ensures 'dien_thoai' and 'so_zalo' fields have less than 15 digits.
 */
function custom_filter_wpcf7_is_tel($result, $tel)
{
	$result = preg_match('/^(0|\+84)(\s|\.)?((3[2-9])|(5[689])|(7[06-9])|(8[1-689])|(9[0-46-9]))(\d)(\s|\.)?(\d{3})(\s|\.)?(\d{3})$/', $tel);
	return $result;
}

add_filter('wpcf7_is_tel', 'custom_filter_wpcf7_is_tel', 10, 2);


/**
 * Simple shortcode that outputs an H1 tag
 * 
 * @return string HTML output
 */
function shortcode_form_3ds($atts = [])
{
	$atts = shortcode_atts([
		'is-red' => 'false'
	], $atts);

	$is_red_class = ($atts['is-red'] === 'true') ? ' is-red' : '';

	return '<div class="form-consulting' . $is_red_class . '">' . do_shortcode('[contact-form-7 id="4c38f2e" title="Form Tư Vấn - CC"]') . '</div>';
}
add_shortcode('shortcode_form_3ds', 'shortcode_form_3ds');

function shortcode_js_slide()
{
?>
	<script type="text/javascript" id="elementor-pro-frontend-js-before">
		/* <![CDATA[ */
		var ElementorProFrontendConfig = {
			"ajaxurl": "https:\/\/3ds.webcanhcam.vn\/wp-admin\/admin-ajax.php",
			"nonce": "80be91f12e",
			"urls": {
				"assets": "https:\/\/3ds.webcanhcam.vn\/wp-content\/plugins\/elementor-pro\/assets\/"
			},
			"i18n": {
				"toc_no_headings_found": "No headings were found on this page."
			},
			"shareButtonsNetworks": {
				"facebook": {
					"title": "Facebook",
					"has_counter": true
				},
				"twitter": {
					"title": "Twitter"
				},
				"google": {
					"title": "Google+",
					"has_counter": true
				},
				"linkedin": {
					"title": "LinkedIn",
					"has_counter": true
				},
				"pinterest": {
					"title": "Pinterest",
					"has_counter": true
				},
				"reddit": {
					"title": "Reddit",
					"has_counter": true
				},
				"vk": {
					"title": "VK",
					"has_counter": true
				},
				"odnoklassniki": {
					"title": "OK",
					"has_counter": true
				},
				"tumblr": {
					"title": "Tumblr"
				},
				"digg": {
					"title": "Digg"
				},
				"skype": {
					"title": "Skype"
				},
				"stumbleupon": {
					"title": "StumbleUpon",
					"has_counter": true
				},
				"mix": {
					"title": "Mix"
				},
				"telegram": {
					"title": "Telegram"
				},
				"pocket": {
					"title": "Pocket",
					"has_counter": true
				},
				"xing": {
					"title": "XING",
					"has_counter": true
				},
				"whatsapp": {
					"title": "WhatsApp"
				},
				"email": {
					"title": "Email"
				},
				"print": {
					"title": "Print"
				}
			},
			"menu_cart": {
				"cart_page_url": "https:\/\/3ds.webcanhcam.vn\/gio-hang\/",
				"checkout_page_url": "https:\/\/3ds.webcanhcam.vn\/thanh-toan\/"
			},
			"facebook_sdk": {
				"lang": "vi",
				"app_id": ""
			},
			"lottie": {
				"defaultAnimationUrl": "https:\/\/3ds.webcanhcam.vn\/wp-content\/plugins\/elementor-pro\/modules\/lottie\/assets\/animations\/default.json"
			}
		};
		/* ]]> */
	</script>
	<script type="text/javascript" id="elementor-frontend-js-before">
		/* <![CDATA[ */
		var elementorFrontendConfig = {
			"environmentMode": {
				"edit": false,
				"wpPreview": false,
				"isScriptDebug": false
			},
			"i18n": {
				"shareOnFacebook": "Chia s\u1ebb tr\u00ean Facebook",
				"shareOnTwitter": "Chia s\u1ebb tr\u00ean Twitter",
				"pinIt": "Ghim n\u00f3",
				"download": "T\u1ea3i xu\u1ed1ng",
				"downloadImage": "T\u1ea3i h\u00ecnh \u1ea3nh",
				"fullscreen": "To\u00e0n m\u00e0n h\u00ecnh",
				"zoom": "Thu ph\u00f3ng",
				"share": "Chia s\u1ebb",
				"playVideo": "Ph\u00e1t video",
				"previous": "Quay v\u1ec1",
				"next": "Ti\u1ebfp theo",
				"close": "\u0110\u00f3ng"
			},
			"is_rtl": false,
			"breakpoints": {
				"xs": 0,
				"sm": 480,
				"md": 768,
				"lg": 1025,
				"xl": 1440,
				"xxl": 1600
			},
			"responsive": {
				"breakpoints": {
					"mobile": {
						"label": "Thi\u1ebft b\u1ecb di \u0111\u1ed9ng",
						"value": 767,
						"default_value": 767,
						"direction": "max",
						"is_enabled": true
					},
					"mobile_extra": {
						"label": "Mobile Extra",
						"value": 880,
						"default_value": 880,
						"direction": "max",
						"is_enabled": false
					},
					"tablet": {
						"label": "M\u00e1y t\u00ednh b\u1ea3ng",
						"value": 1024,
						"default_value": 1024,
						"direction": "max",
						"is_enabled": true
					},
					"tablet_extra": {
						"label": "Tablet Extra",
						"value": 1200,
						"default_value": 1200,
						"direction": "max",
						"is_enabled": false
					},
					"laptop": {
						"label": "Laptop",
						"value": 1366,
						"default_value": 1366,
						"direction": "max",
						"is_enabled": false
					},
					"widescreen": {
						"label": "Trang r\u1ed9ng",
						"value": 2400,
						"default_value": 2400,
						"direction": "min",
						"is_enabled": false
					}
				}
			},
			"version": "3.5.6",
			"is_static": false,
			"experimentalFeatures": {
				"e_dom_optimization": true,
				"e_optimized_assets_loading": true,
				"e_optimized_css_loading": true,
				"a11y_improvements": true,
				"e_import_export": true,
				"e_hidden_wordpress_widgets": true,
				"landing-pages": true,
				"elements-color-picker": true,
				"favorite-widgets": true,
				"admin-top-bar": true,
				"form-submissions": true,
				"video-playlist": true
			},
			"urls": {
				"assets": "https:\/\/3ds.webcanhcam.vn\/wp-content\/plugins\/elementor\/assets\/"
			},
			"settings": {
				"page": [],
				"editorPreferences": []
			},
			"kit": {
				"active_breakpoints": ["viewport_mobile", "viewport_tablet"],
				"global_image_lightbox": "yes",
				"lightbox_enable_counter": "yes",
				"lightbox_enable_fullscreen": "yes",
				"lightbox_enable_zoom": "yes",
				"lightbox_enable_share": "yes",
				"lightbox_title_src": "title",
				"lightbox_description_src": "description"
			},
			"post": {
				"id": 33992,
				"title": "Gi%E1%BA%A3i%20ph%C3%A1p%20in%203D%20l%C4%A9nh%20v%E1%BB%B1c%20%C3%94%20t%C3%B4%20-%203D%20Smart%20Solutions",
				"excerpt": "T\u1ea1o ra nh\u1eefng b\u1ed9 ph\u1eadn, chi ti\u1ebft c\u1ee7a xe \u00f4 t\u00f4 m\u1ed9t c\u00e1ch d\u1ec5 d\u00e0ng v\u00e0 hi\u1ec7u qu\u1ea3. T\u1ed1i \u01b0u qu\u00e1 tr\u00ecnh s\u1ea3n xu\u1ea5t xe \u00f4 t\u00f4. Mang l\u1ea1i c\u00e1c s\u1ea3n ph\u1ea9m ch\u1ea5t l\u01b0\u1ee3ng cao kh\u00f4ng k\u00e9m ph\u01b0\u01a1ng ph\u00e1p truy\u1ec1n th\u1ed1ng.",
				"featuredImage": "https:\/\/3ds.webcanhcam.vn\/wp-content\/uploads\/2021\/02\/151202798_4276698715678034_92518417297108840_n-1.jpg"
			},
			"user": {
				"roles": ["administrator"]
			}
		};
		/* ]]> */
	</script>
	<script type="text/javascript" id="elementor-pro-frontend-js-before">
		/* <![CDATA[ */
		var ElementorProFrontendConfig = {
			"ajaxurl": "https:\/\/3ds.webcanhcam.vn\/wp-admin\/admin-ajax.php",
			"nonce": "80be91f12e",
			"urls": {
				"assets": "https:\/\/3ds.webcanhcam.vn\/wp-content\/plugins\/elementor-pro\/assets\/"
			},
			"i18n": {
				"toc_no_headings_found": "No headings were found on this page."
			},
			"shareButtonsNetworks": {
				"facebook": {
					"title": "Facebook",
					"has_counter": true
				},
				"twitter": {
					"title": "Twitter"
				},
				"google": {
					"title": "Google+",
					"has_counter": true
				},
				"linkedin": {
					"title": "LinkedIn",
					"has_counter": true
				},
				"pinterest": {
					"title": "Pinterest",
					"has_counter": true
				},
				"reddit": {
					"title": "Reddit",
					"has_counter": true
				},
				"vk": {
					"title": "VK",
					"has_counter": true
				},
				"odnoklassniki": {
					"title": "OK",
					"has_counter": true
				},
				"tumblr": {
					"title": "Tumblr"
				},
				"digg": {
					"title": "Digg"
				},
				"skype": {
					"title": "Skype"
				},
				"stumbleupon": {
					"title": "StumbleUpon",
					"has_counter": true
				},
				"mix": {
					"title": "Mix"
				},
				"telegram": {
					"title": "Telegram"
				},
				"pocket": {
					"title": "Pocket",
					"has_counter": true
				},
				"xing": {
					"title": "XING",
					"has_counter": true
				},
				"whatsapp": {
					"title": "WhatsApp"
				},
				"email": {
					"title": "Email"
				},
				"print": {
					"title": "Print"
				}
			},
			"menu_cart": {
				"cart_page_url": "https:\/\/3ds.webcanhcam.vn\/gio-hang\/",
				"checkout_page_url": "https:\/\/3ds.webcanhcam.vn\/thanh-toan\/"
			},
			"facebook_sdk": {
				"lang": "vi",
				"app_id": ""
			},
			"lottie": {
				"defaultAnimationUrl": "https:\/\/3ds.webcanhcam.vn\/wp-content\/plugins\/elementor-pro\/modules\/lottie\/assets\/animations\/default.json"
			}
		};
		/* ]]> */
	</script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/template_3ds/3ds-script/1_common-modules.min.js" id="elementor-common-modules-js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/template_3ds/3ds-script/2_common.min.js" id="elementor-common-js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/template_3ds/3ds-script/3_webpack-pro.runtime.min.js" id="elementor-pro-webpack-runtime-js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/template_3ds/3ds-script/4_webpack.runtime.min.js" id="elementor-webpack-runtime-js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/template_3ds/3ds-script/5_frontend-modules.min.js" id="elementor-frontend-modules-js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/template_3ds/3ds-script/6_frontend.min.js" id="elementor-pro-frontend-js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/template_3ds/3ds-script/7_frontend.min.js" id="elementor-frontend-js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/template_3ds/3ds-script/8_elements-handlers.min.js" id="pro-elements-handlers-js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/template_3ds/3ds-script/9_carousel.1ebc0652cb61e40967b7.bundle.min.js" id="elementor-frontend-modules-js"></script>
<?php
}
add_shortcode('shortcode_js_slide', 'shortcode_js_slide');

function shortcode_css_lp_3ds()
{
	return '<link href="' . get_template_directory_uri() . '/styles/css-lp-3ds.css" rel="stylesheet">';
}
add_shortcode('shortcode_css_lp_3ds', 'shortcode_css_lp_3ds');
function change_cf7_pipes($tag)
{
	if ($tag['basetype'] != 'select' && $tag['basetype'] != 'radio') {
		return $tag;
	}
	$values = [];
	$labels = [];
	foreach ($tag['raw_values'] as $raw_value) {
		$raw_value_parts = explode('|', $raw_value);
		if (count($raw_value_parts) >= 2) {
			$values[] = $raw_value_parts[1];
			$labels[] = $raw_value_parts[0];
		} else {
			$values[] = $raw_value;
			$labels[] = $raw_value;
		}
	}
	$tag['values'] = $values;
	$tag['labels'] = $labels;
	return $tag;
}
add_filter('wpcf7_form_tag', 'change_cf7_pipes', 10);
function shortcode_js_remove_style_3ds()
{
?>
	<script>
		jQuery(document).ready(function($) {
			function updateSpecificInlineFontSizesAndLineHeight() {
				// Select elements potentially having an inline font-size
				$('[style*="font-size"]').each(function() {
					var element = $(this);
					var currentStyle = element.attr('style');
					var newStyle = currentStyle; // Start with the current style
					var fontSizeChanged = false;

					if (currentStyle) {
						// Check if the target font sizes exist
						if (/font-size\s*:\s*(?:14|15|16)px/i.test(currentStyle)) {
							// Replace the target font sizes with 18px
							newStyle = newStyle.replace(/font-size\s*:\s*(?:14|15|16)px\s*;?/gi, 'font-size: 18px;');
							fontSizeChanged = true;
						}

						// If the font size was changed, also set the line-height
						if (fontSizeChanged) {
							// Remove any existing line-height property first
							newStyle = newStyle.replace(/line-height\s*:[^;]+;?/gi, '');
							// Clean up potential extra spaces or semicolons left after removal
							newStyle = newStyle.replace(/\s*;\s*/g, '; ').trim();
							// Append the new line-height, ensuring a preceding semicolon if needed
							if (newStyle.length > 0 && !newStyle.endsWith(';')) {
								newStyle += ';';
							}
							newStyle += ' line-height: 1.4;';
						}

						// Clean up double semicolons and final trim
						newStyle = newStyle.replace(/;;\s*/g, ';').trim();

						// Update the style attribute only if changes were made
						if (newStyle !== currentStyle.trim()) {
							if (newStyle === '' || newStyle === ';') {
								element.removeAttr('style');
							} else {
								element.attr('style', newStyle);
							}
						}
					}
				});
			}
			updateSpecificInlineFontSizesAndLineHeight();
		});
	</script>
<?php
}
add_shortcode('shortcode_js_remove_style_3ds', 'shortcode_js_remove_style_3ds');


/**
 * Filter WooCommerce notices on the My Account page.
 */
add_action('woocommerce_account_content', 'custom_filter_account_notices_display', 5); // Hook before notices are typically shown

function custom_filter_account_notices_display()
{
	// Check if we are on the 'edit-account' endpoint page
	if (is_account_page() && isset($GLOBALS['wp']->query_vars['edit-account'])) {
		// Check if the user has social login meta (adjust meta key if needed)
		$user_id = get_current_user_id();
	}
}



/**
 * Filter to add 'Tuyển dụng' page to Rank Math Breadcrumbs for tuyen_dung post type.
 */
add_filter('rank_math/frontend/breadcrumb/items', function ($crumbs, $class) {
	// Only modify breadcrumbs for tuyen_dung post type
	$link = get_page_link_by_template('page-recruitment.php');
	if (is_singular('tuyen_dung')) {
		// Create the Tuyen dung link
		$tuyen_dung_link = [
			__('Tuyển dụng', 'canhcamtheme'),
			$link
		];

		// Insert after the Home link (position 1)
		if (count($crumbs) >= 1) {
			array_splice($crumbs, 1, 0, [$tuyen_dung_link]);
		}
	}

	return $crumbs;
}, 10, 2);


/**
 * Get default variation for a variable product
 *
 * @param WC_Product $product
 * @return WC_Product|null Default variation or null if not found
 */
function get_default_variation($product)
{
	if (!$product->is_type('variable')) {
		return null;
	}

	$default_attributes = $product->get_default_attributes();
	if (empty($default_attributes)) {
		return null;
	}

	$variation_id = 0;
	$is_default_variation = false;

	foreach ($product->get_available_variations() as $variation_values) {
		foreach ($variation_values['attributes'] as $key => $attribute_value) {
			$attribute_name = str_replace('attribute_', '', $key);
			$default_value = $product->get_variation_default_attribute($attribute_name);
			if ($default_value == $attribute_value) {
				$is_default_variation = true;
			} else {
				$is_default_variation = false;
				break; // Stop this loop to start next main loop
			}
		}
		if ($is_default_variation) {
			$variation_id = $variation_values['variation_id'];
			break; // Stop the main loop
		}
	}

	if ($is_default_variation && $variation_id > 0) {
		return wc_get_product($variation_id);
	}

	return null;
}

function get_content_3ds($id)
{
	$content = get_post_field('post_content', $id);
	if (!empty($content)) {
		$doc = new DOMDocument();
		libxml_use_internal_errors(true);
		$doc->loadHTML('<?xml encoding="utf-8" ?>' . $content);
		libxml_clear_errors();

		// Remove all iframe and script tags
		$tags_to_remove_completely = ['iframe', 'script'];
		foreach ($tags_to_remove_completely as $tag_name) {
			$elements = $doc->getElementsByTagName($tag_name);
			for ($i = $elements->length - 1; $i >= 0; $i--) {
				$element = $elements->item($i);
				if ($element->parentNode) { // Check if parentNode exists
					$element->parentNode->removeChild($element);
				}
			}
		}

		// Handle img tags: remove all except the first one
		$img_elements = $doc->getElementsByTagName('img');
		$img_count = $img_elements->length;
		// Iterate backwards from the last img down to the second img (index 1)
		// This leaves the first image (index 0) intact.
		for ($i = $img_count; $i > 0; $i--) {
			$img_element = $img_elements->item($i);
			if ($img_element->parentNode) { // Check if parentNode exists
				$img_element->parentNode->removeChild($img_element);
			}
		}

		$body_node = $doc->getElementsByTagName('body')->item(0);
		$cleaned_html = '';
		if ($body_node) {
			foreach ($body_node->childNodes as $child) {
				$cleaned_html .= $doc->saveHTML($child);
			}
		}

		echo apply_filters('the_content', $cleaned_html);
	} else {
		echo apply_filters('the_content', '');
	}
}
/**
 * In Contact Form 7, finds all <select> fields and correctly adds the 'selected', 
 * 'hidden', and 'disabled' attributes to the first option if its value is empty.
 * This creates a non-selectable, hidden placeholder.
 *
 * @param string $html The HTML content of the form.
 * @return string The modified HTML content.
 */
function cf7_modify_first_select_option($html)
{
	$html = preg_replace_callback(
		'/<select\b[^>]*>(.*?)<\/select>/s',
		function ($matches) {
			$select_block = $matches[0];
			$modified_block = preg_replace(
				'/(<option\s+value="")(>)/',
				'$1 selected hidden disabled$2', // Correctly inserts attributes inside the tag
				$select_block,
				1
			);

			return $modified_block;
		},
		$html
	);

	return $html;
}

add_filter('wpcf7_form_elements', 'cf7_modify_first_select_option');

?>