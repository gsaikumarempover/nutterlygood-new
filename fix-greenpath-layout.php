<?php
/**
 * Restore GreenPath demo layout for Nutterly Good:
 * - Extended header + larger logo
 * - GreenPath-style centered RevSlider (3 slides, 886px)
 * - Full 7-section homepage from demo
 * - Category thumbnails + banner images
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$admin = get_user_by( 'login', 'nutterlygood' );
if ( $admin ) {
	wp_set_current_user( $admin->ID );
}

function ng_log( $msg ) {
	echo $msg . PHP_EOL;
}

function ng_get_upload_url( $rel ) {
	$upload = wp_upload_dir();
	return trailingslashit( $upload['baseurl'] ) . ltrim( $rel, '/' );
}

function ng_get_upload_path( $rel ) {
	$upload = wp_upload_dir();
	return trailingslashit( $upload['basedir'] ) . ltrim( $rel, '/' );
}

function ng_get_attachment_by_file( $rel ) {
	global $wpdb;
	$id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value=%s LIMIT 1",
			ltrim( $rel, '/' )
		)
	);
	return $id ? (int) $id : 0;
}

function ng_ensure_media_attachment( $rel, $title = '' ) {
	$rel = ltrim( $rel, '/' );
	$existing = ng_get_attachment_by_file( $rel );
	if ( $existing ) {
		return $existing;
	}
	$path = ng_get_upload_path( $rel );
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$filetype = wp_check_filetype( basename( $path ), null );
	$attachment = array(
		'post_mime_type' => $filetype['type'],
		'post_title'     => $title ? $title : sanitize_file_name( pathinfo( $path, PATHINFO_FILENAME ) ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);
	$attach_id = wp_insert_attachment( $attachment, $path );
	if ( ! is_wp_error( $attach_id ) ) {
		$meta = wp_generate_attachment_metadata( $attach_id, $path );
		wp_update_attachment_metadata( $attach_id, $meta );
	}
	return is_wp_error( $attach_id ) ? 0 : (int) $attach_id;
}

function ng_media_ref( $rel, $title = '' ) {
	$id  = ng_ensure_media_attachment( $rel, $title );
	$url = ng_get_upload_url( $rel );
	return array(
		'url'    => $url,
		'id'     => $id,
		'size'   => '',
		'alt'    => $title,
		'source' => 'library',
	);
}

function ng_fix_header() {
	$opts = get_option( 'greenpath_core_options', array() );
	if ( ! is_array( $opts ) ) {
		$opts = array();
	}

	$opts['qodef_header_layout']              = 'standard-extended';
	$opts['qodef_top_area_header']            = 'yes';
	$opts['qodef_top_area_header_in_grid']    = 'yes';
	$opts['qodef_top_area_header_skin']       = 'none';
	$opts['qodef_logo_height']                = '100';
	$opts['qodef_logo_height_mobile']         = '75';
	$opts['qodef_standard_extended_header_in_grid'] = 'yes';
	$opts['qodef_standard_extended_header_background_color'] = 'rgb(252,244,235)';
	$opts['qodef_header_custom_widget_area_one'] = 'extended-header-one';
	$opts['qodef_header_custom_widget_area_two'] = 'extended-header-two';

	update_option( 'greenpath_core_options', $opts );

	$page_id = (int) get_option( 'page_on_front' );
	$page_meta = array(
		'qodef_header_layout'              => 'standard-extended',
		'qodef_top_area_header'            => 'yes',
		'qodef_top_area_header_in_grid'    => 'yes',
		'qodef_top_area_header_skin'       => 'none',
		'qodef_logo_height'                => '100',
		'qodef_standard_extended_header_in_grid' => 'yes',
		'qodef_standard_extended_header_background_color' => 'rgb(252,244,235)',
		'qodef_header_custom_widget_area_one' => 'extended-header-one',
		'qodef_header_custom_widget_area_two' => 'extended-header-two',
		'qodef_page_title_enable_top_border' => 'no',
	);
	foreach ( $page_meta as $key => $val ) {
		update_post_meta( $page_id, $key, $val );
	}

	ng_log( 'Header set to standard-extended, logo 100px, top area enabled.' );
}

function ng_build_greenpath_slider() {
	$export_file = __DIR__ . '/export/revslider/main-home-extracted/slider_export.txt';
	$data        = json_decode( file_get_contents( $export_file ), true );

	$data['title'] = 'Nutterly Good Home';
	$data['alias'] = 'main-home';

	$slides_copy = array(
		array(
			'kicker'   => 'Flavor & Freshness',
			'headline' => "Your Everyday Treat of Tasty Goodness.",
			'bg'       => '2026/06/slider/ng-slide-1-flavor-freshness.jpg',
		),
		array(
			'kicker'   => 'Premium Dry Fruits',
			'headline' => 'Handpicked Almonds, Cashews & More',
			'bg'       => '2026/06/slider/ng-slide-2-dry-fruits.jpg',
		),
		array(
			'kicker'   => 'Crunchy Chips & Mixes',
			'headline' => 'Wholesome Snacking, Reimagined',
			'bg'       => '2026/06/slider/ng-slide-3-chips-mixes.jpg',
		),
	);

	$template_slide = $data['slides'][0];
	$new_slides     = array();

	foreach ( $slides_copy as $index => $copy ) {
		$slide = json_decode( wp_json_encode( $template_slide ), true );
		$slide['id']          = (string) ( $index + 1 );
		$slide['slide_order'] = (string) ( $index + 1 );

		$attach_id = ng_ensure_media_attachment( $copy['bg'], 'Slider BG ' . ( $index + 1 ) );
		$bg_url    = ng_get_upload_url( $copy['bg'] );

		$slide['params']['bg']['type']            = 'image';
		$slide['params']['bg']['image']           = $bg_url;
		$slide['params']['bg']['imageId']         = $attach_id;
		$slide['params']['bg']['imageLib']        = 'medialibrary';
		$slide['params']['bg']['imageWidth']      = 1920;
		$slide['params']['bg']['imageHeight']     = 886;
		$slide['params']['bg']['imageSourceType'] = 'full';
		$slide['params']['version']               = '6.6.20';

		// Layer 0 = Amatic SC kicker (centered).
		if ( isset( $slide['layers']['0'] ) ) {
			$slide['layers']['0']['text'] = $copy['kicker'];
			if ( isset( $slide['layers']['0']['idle']['color']['d'] ) ) {
				$slide['layers']['0']['idle']['color']['d']['v'] = '#B99531';
				$slide['layers']['0']['idle']['color']['n']['v'] = '#B99531';
				$slide['layers']['0']['idle']['color']['t']['v'] = '#B99531';
				$slide['layers']['0']['idle']['color']['m']['v'] = '#B99531';
			}
		}

		// Layer 4 = Manrope headline (centered).
		if ( isset( $slide['layers']['4'] ) ) {
			$slide['layers']['4']['text'] = $copy['headline'];
			if ( isset( $slide['layers']['4']['idle']['color']['d'] ) ) {
				$slide['layers']['4']['idle']['color']['d']['v'] = '#0C533D';
				$slide['layers']['4']['idle']['color']['n']['v'] = '#0C533D';
				$slide['layers']['4']['idle']['color']['t']['v'] = '#0C533D';
				$slide['layers']['4']['idle']['color']['m']['v'] = '#0C533D';
			}
		}

		// Decorative side images — use Nutterly Good nut/fruit PNGs from revslider import.
		$deco_left  = 'revslider/main-home/h1-rev-img-21.png';
		$deco_right = 'revslider/main-home/h1-rev-img-31.png';
		if ( isset( $slide['layers']['1'] ) ) {
			$slide['layers']['1']['media']['imageUrl'] = ng_get_upload_url( $deco_left );
		}
		if ( isset( $slide['layers']['3'] ) ) {
			$slide['layers']['3']['media']['imageUrl'] = ng_get_upload_url( $deco_right );
		}

		// Remove any extra text layers from prior broken fix.
		unset( $slide['layers']['5'], $slide['layers']['6'], $slide['layers'][5], $slide['layers'][6] );

		$new_slides[] = $slide;
	}

	$data['slides'] = $new_slides;
	$data['params']['size']['height']['d'] = '886px';
	$data['params']['size']['height']['n'] = '640px';
	$data['params']['size']['height']['t'] = '770px';
	$data['params']['size']['height']['m'] = '800px';
	$data['params']['general']['lazyLoad'] = 'none';
	if ( isset( $data['params']['nav']['bullets']['presets']['bullet_color'] ) ) {
		$data['params']['nav']['bullets']['presets']['bullet_color'] = '#B99531';
	}
	$data['params']['general']['slideshow']['stopAtSlide'] = count( $new_slides );

	return $data;
}

function ng_import_slider( $slider_data ) {
	$rs_includes = array(
		'admin/includes/license.class.php',
		'admin/includes/addons.class.php',
		'admin/includes/template.class.php',
		'admin/includes/functions-admin.class.php',
		'admin/includes/folder.class.php',
		'admin/includes/import.class.php',
	);
	foreach ( $rs_includes as $inc ) {
		$p = WP_PLUGIN_DIR . '/revslider/' . $inc;
		if ( file_exists( $p ) ) {
			require_once $p;
		}
	}
	if ( ! class_exists( 'RevSliderSliderImport' ) ) {
		ng_log( 'RevSlider import class missing.' );
		return false;
	}

	$work_dir = WP_CONTENT_DIR . '/uploads/ng-rev-import';
	wp_mkdir_p( $work_dir );
	file_put_contents( $work_dir . '/slider_export.txt', wp_json_encode( $slider_data ) );
	if ( file_exists( __DIR__ . '/export/revslider/main-home-extracted/navigation.txt' ) ) {
		file_put_contents( $work_dir . '/navigation.txt', file_get_contents( __DIR__ . '/export/revslider/main-home-extracted/navigation.txt' ) );
	}

	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->prefix}revslider_sliders" );
	$wpdb->query( "DELETE FROM {$wpdb->prefix}revslider_slides" );
	$wpdb->query( "DELETE FROM {$wpdb->prefix}revslider_static_slides" );

	$zip_path = $work_dir . '/main-home-ng.zip';
	if ( file_exists( $zip_path ) ) {
		unlink( $zip_path );
	}
	$zip = new ZipArchive();
	$zip->open( $zip_path, ZipArchive::CREATE );
	$zip->addFile( $work_dir . '/slider_export.txt', 'slider_export.txt' );
	if ( file_exists( $work_dir . '/navigation.txt' ) ) {
		$zip->addFile( $work_dir . '/navigation.txt', 'navigation.txt' );
	}
	$zip->close();

	$import = new RevSliderSliderImport();
	$result = $import->import_slider( true, $zip_path );

	update_option(
		'revslider-global-settings',
		array(
			'forceLazyLoading' => 'none',
			'lazyonbg'         => false,
		)
	);

	if ( ! empty( $result['success'] ) ) {
		ng_log( 'RevSlider restored: 3 slides, GreenPath centered layout, 886px height.' );
		return true;
	}
	ng_log( 'RevSlider import failed: ' . wp_json_encode( $result ) );
	return false;
}

function ng_category_svg( $label, $emoji_path = '' ) {
	$color = '#0C533D';
	$gold  = '#B99531';
	return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" width="120" height="120"><circle cx="60" cy="60" r="56" fill="' . $color . '" opacity="0.12"/><circle cx="60" cy="60" r="48" fill="#FCF4EB" stroke="' . $gold . '" stroke-width="3"/><text x="60" y="68" text-anchor="middle" font-family="Manrope,Arial,sans-serif" font-size="14" font-weight="700" fill="' . $color . '">' . esc_html( $label ) . '</text></svg>';
}

function ng_assign_category_images( $cats ) {
	$map = array(
		'dry-fruits'        => array( 'file' => '2026/06/slider/ng-slide-2-dry-fruits.jpg', 'svg' => 'Dry Fruits' ),
		'almonds'           => array( 'file' => '2026/06/Premium-Classic-Almonds-1.webp', 'svg' => 'Almonds' ),
		'cashews'           => array( 'file' => '2026/06/Premium-Classic-Cashews-1.webp', 'svg' => 'Cashews' ),
		'khishmish'         => array( 'file' => '2026/06/Kala-Khatta-Kishmish-1.webp', 'svg' => 'Khishmish' ),
		'cranberry'         => array( 'file' => '2026/06/Premium-Classic-Cranberry-1.webp', 'svg' => 'Cranberry' ),
		'walnuts'           => array( 'file' => '2026/06/Chilean-Walnuts-1.webp', 'svg' => 'Walnuts' ),
		'chips'             => array( 'file' => '2026/06/slider/ng-slide-3-chips-mixes.jpg', 'svg' => 'Chips' ),
		'mixes'             => array( 'file' => '2026/06/Protein-Mix-1.webp', 'svg' => 'Mixes' ),
		'brittles'          => array( 'file' => '2026/06/Chocolate-Brittle-1.webp', 'svg' => 'Brittles' ),
		'mouth-fresheners'  => array( 'file' => '2026/06/slider/ng-slide-4-mouth-fresheners.jpg', 'svg' => 'Mouth Fresh' ),
	);

	foreach ( $map as $slug => $info ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term ) {
			continue;
		}
		$attach_id = ng_ensure_media_attachment( $info['file'], $term->name . ' category' );
		if ( $attach_id ) {
			update_term_meta( $term->term_id, 'thumbnail_id', $attach_id );
		}
		update_term_meta( $term->term_id, 'qodef_product_category_alternate_svg', ng_category_svg( $info['svg'] ) );
		ng_log( "Category image set: $slug (attach $attach_id)" );
	}
}

function ng_restore_homepage( $cats ) {
	$json_file = __DIR__ . '/export/home-elementor-36.json';
	if ( ! file_exists( $json_file ) ) {
		ng_log( 'Homepage JSON missing.' );
		return;
	}

	$elements = json_decode( file_get_contents( $json_file ), true );
	if ( ! is_array( $elements ) ) {
		ng_log( 'Invalid homepage JSON.' );
		return;
	}

	$shop_url = get_permalink( wc_get_page_id( 'shop' ) );

	// Nutterly Good banner images mapped to use-case visuals.
	$banner_main   = ng_media_ref( '2026/06/slider/ng-slide-2-dry-fruits.jpg', 'Dry Fruits Banner' );
	$banner_stamp1 = ng_media_ref( '2023/09/h1-img-1.png', 'Decorative stamp' );
	$banner_mid    = ng_media_ref( '2026/06/slider/ng-slide-3-chips-mixes.jpg', 'Chips & Mixes Banner' );
	$banner_small  = ng_media_ref( '2026/06/slider/ng-slide-4-mouth-fresheners.jpg', 'Mouth Freshners Banner' );
	$banner_stamp2 = ng_media_ref( '2023/09/h1-img-8.png', 'Decorative stamp 2' );
	$countdown_bg  = ng_media_ref( '2026/06/slider/ng-slide-1-flavor-freshness.jpg', 'Countdown background' );

	$cat_slugs = 'dry-fruits,almonds,cashews,khishmish,cranberry,walnuts,chips,mixes,brittles,mouth-fresheners';
	$cat_ids   = implode(
		', ',
		array_filter(
			array(
				$cats['dry_fruits'] ?? 0,
				$cats['almonds'] ?? 0,
				$cats['cashews'] ?? 0,
				$cats['khishmish'] ?? 0,
				$cats['cranberry'] ?? 0,
				$cats['walnuts'] ?? 0,
				$cats['chips'] ?? 0,
				$cats['mixes'] ?? 0,
				$cats['brittles'] ?? 0,
				$cats['mouth'] ?? 0,
			)
		)
	);

	$walker = function ( &$items ) use ( &$walker, $banner_main, $banner_stamp1, $banner_mid, $banner_small, $banner_stamp2, $countdown_bg, $cat_slugs, $cat_ids, $shop_url ) {
		foreach ( $items as &$el ) {
			if ( empty( $el['widgetType'] ) && ! empty( $el['settings'] ) ) {
				if ( ! empty( $el['settings']['background_image']['url'] ) && strpos( $el['settings']['background_image']['url'], 'h1-rev-img-4' ) !== false ) {
					$el['settings']['background_image'] = $countdown_bg;
				}
			}

			if ( ! empty( $el['widgetType'] ) ) {
				$w  = $el['widgetType'];
				$id = $el['id'] ?? '';
				if ( 'greenpath_core_section_title' === $w ) {
					if ( isset( $el['settings']['title'] ) ) {
						if ( stripos( $el['settings']['title'], 'Fresh' ) !== false ) {
							$el['settings']['title'] = 'Fresh, Tasty, and Wholesome';
						} elseif ( stripos( $el['settings']['title'], 'Featured' ) !== false ) {
							$el['settings']['title'] = 'Featured Products';
						}
					}
				}
				if ( 'greenpath_core_product_list' === $w ) {
					$el['settings']['tax']       = 'product_cat';
					$el['settings']['tax_slug']  = ( isset( $el['settings']['enable_custom_filter'] ) && 'yes' === $el['settings']['enable_custom_filter'] ) ? 'mixes' : 'dry-fruits';
					$el['settings']['additional_params'] = 'tax';
					if ( isset( $el['settings']['behavior'] ) && 'slider' === $el['settings']['behavior'] ) {
						$el['settings']['posts_per_page'] = '8';
					}
				}
				if ( 'greenpath_core_product_category_list' === $w ) {
					$el['settings']['additional_params']   = 'slug';
					$el['settings']['taxonomy_slugs']      = $cat_slugs;
					$el['settings']['taxonomy_ids']        = $cat_ids;
					$el['settings']['use_alternate_image'] = 'no';
					$el['settings']['posts_per_page']    = '10';
					$el['settings']['columns']           = '10';
				}
				if ( 'greenpath_core_banner' === $w ) {
					if ( '192bbec' === $id ) {
						$el['settings']['image']      = $banner_main;
						$el['settings']['stamp']      = $banner_stamp1;
						$el['settings']['title']      = 'Premium Dry Fruits Start Here!';
						$el['settings']['text_field'] = 'Welcome to Nutterly Good';
						$el['settings']['text']       = 'Start Shopping';
						$el['settings']['link_url']   = $shop_url;
					} elseif ( '9f9672e' === $id ) {
						$el['settings']['image']      = $banner_mid;
						$el['settings']['title']      = 'Crunchy Chips & Mixes';
						$el['settings']['text_field'] = 'Wholesome snacking';
						$el['settings']['text']       = 'Shop Now';
						$el['settings']['link_url']   = $shop_url;
					} elseif ( '62f9b1e' === $id ) {
						$el['settings']['image']    = $banner_small;
						$el['settings']['stamp']    = $banner_stamp2;
						$el['settings']['link_url'] = $shop_url;
					}
				}
				if ( 'greenpath_core_custom_font' === $w ) {
					$el['settings']['title']  = 'Premium Quality Snacks';
					$el['settings']['color']  = '#0C533D';
				}
				if ( 'text-editor' === $w && isset( $el['settings']['editor'] ) ) {
					$el['settings']['editor'] = '<p>100% Natural Goodness</p>';
				}
				if ( 'slider_revolution' === $w ) {
					$el['settings']['revslidertitle'] = 'Nutterly Good Home';
					$el['settings']['shortcode']      = '[rev_slider alias="main-home" slidertitle="Nutterly Good Home"][/rev_slider]';
				}
			}

			if ( ! empty( $el['elements'] ) ) {
				$walker( $el['elements'] );
			}
		}
	};
	$walker( $elements );

	// Drop blog section (section 7) — keep first 6 content sections like current site.
	if ( count( $elements ) > 6 ) {
		$last = end( $elements );
		if ( ! empty( $last['elements'] ) ) {
			$has_blog = false;
			$check = function ( $items ) use ( &$check, &$has_blog ) {
				foreach ( $items as $item ) {
					if ( ( $item['widgetType'] ?? '' ) === 'greenpath_core_blog_list' ) {
						$has_blog = true;
					}
					if ( ! empty( $item['elements'] ) ) {
						$check( $item['elements'] );
					}
				}
			};
			$check( $last['elements'] );
			if ( $has_blog ) {
				array_pop( $elements );
				ng_log( 'Removed blog section from homepage.' );
			}
		}
	}

	$page_id = (int) get_option( 'page_on_front' );
	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
	delete_post_meta( $page_id, '_elementor_css' );

	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	ng_log( 'Homepage restored to GreenPath 7-section layout (blog removed).' );
}

// --- Categories ---
$dry_fruits = (int) ( get_term_by( 'slug', 'dry-fruits', 'product_cat' )->term_id ?? 0 );
$almonds    = (int) ( get_term_by( 'slug', 'almonds', 'product_cat' )->term_id ?? 0 );
$cashews    = (int) ( get_term_by( 'slug', 'cashews', 'product_cat' )->term_id ?? 0 );
$khishmish  = (int) ( get_term_by( 'slug', 'khishmish', 'product_cat' )->term_id ?? 0 );
$cranberry  = (int) ( get_term_by( 'slug', 'cranberry', 'product_cat' )->term_id ?? 0 );
$walnuts    = (int) ( get_term_by( 'slug', 'walnuts', 'product_cat' )->term_id ?? 0 );
$chips      = (int) ( get_term_by( 'slug', 'chips', 'product_cat' )->term_id ?? 0 );
$mixes      = (int) ( get_term_by( 'slug', 'mixes', 'product_cat' )->term_id ?? 0 );
$brittles   = (int) ( get_term_by( 'slug', 'brittles', 'product_cat' )->term_id ?? 0 );
$mouth      = (int) ( get_term_by( 'slug', 'mouth-fresheners', 'product_cat' )->term_id ?? 0 );

$cats = compact( 'dry_fruits', 'almonds', 'cashews', 'khishmish', 'cranberry', 'walnuts', 'chips', 'mixes', 'brittles', 'mouth' );

ng_log( '=== Fix GreenPath Layout Start ===' );
ng_fix_header();
ng_assign_category_images( $cats );
$slider_data = ng_build_greenpath_slider();
ng_import_slider( $slider_data );
ng_restore_homepage( $cats );
wp_cache_flush();
ng_log( '=== Done. Hard refresh http://localhost/nutterlyGood/ (Ctrl+F5) ===' );