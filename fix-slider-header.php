<?php
/**
 * Logo size, remove Best Deal, 600px GreenPath-style slider, uniform category thumbs.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

function ng_log( $msg ) {
	echo $msg . PHP_EOL;
}

function ng_upload_url( $rel ) {
	$upload = wp_upload_dir();
	return trailingslashit( $upload['baseurl'] ) . ltrim( $rel, '/' );
}

function ng_upload_path( $rel ) {
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

function ng_ensure_media( $rel, $title = '' ) {
	$rel = ltrim( $rel, '/' );
	$existing = ng_get_attachment_by_file( $rel );
	if ( $existing ) {
		return $existing;
	}
	$path = ng_upload_path( $rel );
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$filetype   = wp_check_filetype( basename( $path ), null );
	$attach_id  = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => $title ? $title : sanitize_file_name( pathinfo( $path, PATHINFO_FILENAME ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$path
	);
	if ( ! is_wp_error( $attach_id ) ) {
		$meta = wp_generate_attachment_metadata( $attach_id, $path );
		wp_update_attachment_metadata( $attach_id, $meta );
	}
	return is_wp_error( $attach_id ) ? 0 : (int) $attach_id;
}

// --- 1. Logo size ---
$logo_h = '48';
$opts   = get_option( 'greenpath_core_options', array() );
if ( ! is_array( $opts ) ) {
	$opts = array();
}
$opts['qodef_logo_height']        = $logo_h;
$opts['qodef_logo_height_mobile'] = '68';
update_option( 'greenpath_core_options', $opts );

$page_id = (int) get_option( 'page_on_front' );
update_post_meta( $page_id, 'qodef_logo_height', $logo_h );
update_post_meta( $page_id, 'qodef_logo_height_mobile', '68' );
ng_log( "Logo height set to {$logo_h}px." );

// --- 2. Remove Best Deal from header bottom bar ---
$sidebars = get_option( 'sidebars_widgets', array() );
$sidebars['extended-header-two'] = array();
update_option( 'sidebars_widgets', $sidebars );
ng_log( 'Best Deal widget removed from header menu section.' );

// --- 3. Uniform category thumbnails ---
$cat_map = array(
	'dry-fruits'       => '2026/06/category-thumbs/ng-cat-dry-fruits.jpg',
	'almonds'          => '2026/06/category-thumbs/ng-cat-almonds.jpg',
	'cashews'          => '2026/06/category-thumbs/ng-cat-cashews.jpg',
	'khishmish'        => '2026/06/category-thumbs/ng-cat-khishmish.jpg',
	'cranberry'        => '2026/06/category-thumbs/ng-cat-cranberry.jpg',
	'walnuts'          => '2026/06/category-thumbs/ng-cat-walnuts.jpg',
	'chips'            => '2026/06/category-thumbs/ng-cat-chips.jpg',
	'mixes'            => '2026/06/category-thumbs/ng-cat-mixes.jpg',
	'brittles'         => '2026/06/category-thumbs/ng-cat-brittles.jpg',
	'mouth-fresheners' => '2026/06/category-thumbs/ng-cat-mouth-fresheners.jpg',
);
foreach ( $cat_map as $slug => $file ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( ! $term ) {
		continue;
	}
	$attach_id = ng_ensure_media( $file, $term->name . ' category' );
	if ( $attach_id ) {
		update_term_meta( $term->term_id, 'thumbnail_id', $attach_id );
		delete_term_meta( $term->term_id, 'qodef_product_category_alternate_svg' );
		ng_log( "Category thumb: $slug" );
	}
}

// --- 4. RevSlider: 600px, GreenPath layout, new hero + deco images ---
$export_file = __DIR__ . '/export/revslider/main-home-extracted/slider_export.txt';
$data        = json_decode( file_get_contents( $export_file ), true );
$data['title'] = 'Nutterly Good Home';
$data['alias'] = 'main-home';

$slides_copy = array(
	array(
		'kicker'   => 'Flavor & Freshness',
		'headline' => 'Your Everyday Treat of Tasty Goodness.',
		'bg'       => '2026/06/slider/ng-hero-flavor-freshness.jpg',
	),
	array(
		'kicker'   => 'Premium Dry Fruits',
		'headline' => 'Handpicked Almonds, Cashews & More',
		'bg'       => '2026/06/slider/ng-hero-dry-fruits.jpg',
	),
	array(
		'kicker'   => 'Crunchy Chips & Mixes',
		'headline' => 'Wholesome Snacking, Reimagined',
		'bg'       => '2026/06/slider/ng-hero-chips-mixes.jpg',
	),
);

$template_slide = $data['slides'][0];
$new_slides     = array();
$scale          = 600 / 886;

foreach ( $slides_copy as $index => $copy ) {
	$slide = json_decode( wp_json_encode( $template_slide ), true );
	$slide['id']          = (string) ( $index + 1 );
	$slide['slide_order'] = (string) ( $index + 1 );

	$attach_id = ng_ensure_media( $copy['bg'], 'Slider BG ' . ( $index + 1 ) );
	$bg_url    = ng_upload_url( $copy['bg'] );

	$slide['params']['bg']['type']            = 'image';
	$slide['params']['bg']['image']           = $bg_url;
	$slide['params']['bg']['imageId']         = $attach_id;
	$slide['params']['bg']['imageLib']        = 'medialibrary';
	$slide['params']['bg']['imageWidth']      = 1920;
	$slide['params']['bg']['imageHeight']     = 600;
	$slide['params']['bg']['imageSourceType'] = 'full';
	$slide['params']['bg']['fit']             = 'cover';
	$slide['params']['bg']['position']        = 'center bottom';
	$slide['params']['bg']['repeat']          = 'no-repeat';
	$slide['params']['version'] = '6.6.20';
	// Fix ghost text: fade out previous slide (was a:false).
	$slide['params']['slideChange'] = array(
		'in'    => array( 'o' => 0 ),
		'out'   => array( 'o' => 0, 's' => 600 ),
		'title' => '*opacity* Fade In',
		'main'  => 'basic',
		'group' => 'fade',
		'preset'=> 'fade',
	);

	$layer_ids = array( '0', '4' );
	foreach ( $layer_ids as $layer_id ) {
		if ( ! isset( $slide['layers'][ $layer_id ] ) ) {
			continue;
		}
		if ( isset( $slide['layers'][ $layer_id ]['timeline']['frames']['frame_999'] ) ) {
			$slide['layers'][ $layer_id ]['timeline']['frames']['frame_999']['transform']['opacity'] = 0;
			$slide['layers'][ $layer_id ]['timeline']['frames']['frame_999']['timeline']['endWithSlide'] = true;
			$slide['layers'][ $layer_id ]['timeline']['frames']['frame_999']['timeline']['speed']      = 600;
		}
	}

	if ( isset( $slide['layers']['0'] ) ) {
		$slide['layers']['0']['text'] = $copy['kicker'];
		$slide['layers']['0']['position']['horizontal']['d']['v'] = 'center';
		$slide['layers']['0']['position']['horizontal']['n']['v'] = 'center';
		$slide['layers']['0']['position']['horizontal']['t']['v'] = 'center';
		$slide['layers']['0']['position']['horizontal']['m']['v'] = 'center';
		// Middle-top (600px slider): tighter to product band below.
		$slide['layers']['0']['position']['y']['d']['v'] = '-108px';
		$slide['layers']['0']['position']['y']['n']['v'] = '-88px';
		$slide['layers']['0']['position']['y']['t']['v'] = '-72px';
		$slide['layers']['0']['position']['y']['m']['v'] = '-95px';
		$slide['layers']['0']['idle']['fontSize']['d']['v']  = '108px';
		$slide['layers']['0']['idle']['fontSize']['n']['v']  = '92px';
		$slide['layers']['0']['idle']['fontSize']['t']['v']  = '80px';
		$slide['layers']['0']['idle']['fontSize']['m']['v']  = '68px';
		$slide['layers']['0']['idle']['lineHeight']['d']['v'] = '118px';
		$slide['layers']['0']['idle']['lineHeight']['n']['v'] = '100px';
		$slide['layers']['0']['idle']['lineHeight']['t']['v'] = '88px';
		$slide['layers']['0']['idle']['lineHeight']['m']['v'] = '76px';
		$slide['layers']['0']['idle']['textAlign']['d']['v']  = 'center';
		$slide['layers']['0']['idle']['textAlign']['n']['v']  = 'center';
		$slide['layers']['0']['idle']['textAlign']['t']['v']  = 'center';
		$slide['layers']['0']['idle']['textAlign']['m']['v']  = 'center';
		if ( isset( $slide['layers']['0']['idle']['color']['d'] ) ) {
			$slide['layers']['0']['idle']['color']['d']['v'] = '#B99531';
			$slide['layers']['0']['idle']['color']['n']['v'] = '#B99531';
			$slide['layers']['0']['idle']['color']['t']['v'] = '#B99531';
			$slide['layers']['0']['idle']['color']['m']['v'] = '#B99531';
		}
	}

	if ( isset( $slide['layers']['4'] ) ) {
		$slide['layers']['4']['text'] = $copy['headline'];
		$slide['layers']['4']['position']['horizontal']['d']['v'] = 'center';
		$slide['layers']['4']['position']['horizontal']['n']['v'] = 'center';
		$slide['layers']['4']['position']['horizontal']['t']['v'] = 'center';
		$slide['layers']['4']['position']['horizontal']['m']['v'] = 'center';
		$slide['layers']['4']['position']['y']['d']['v'] = '-48px';
		$slide['layers']['4']['position']['y']['n']['v'] = '-38px';
		$slide['layers']['4']['position']['y']['t']['v'] = '-32px';
		$slide['layers']['4']['position']['y']['m']['v'] = '-42px';
		$slide['layers']['4']['idle']['fontSize']['d']['v']  = '20px';
		$slide['layers']['4']['idle']['fontSize']['n']['v']  = '18px';
		$slide['layers']['4']['idle']['fontSize']['t']['v']  = '16px';
		$slide['layers']['4']['idle']['fontSize']['m']['v']  = '15px';
		$slide['layers']['4']['idle']['textAlign']['d']['v']  = 'center';
		$slide['layers']['4']['idle']['textAlign']['n']['v']  = 'center';
		$slide['layers']['4']['idle']['textAlign']['t']['v']  = 'center';
		$slide['layers']['4']['idle']['textAlign']['m']['v']  = 'center';
		if ( isset( $slide['layers']['4']['idle']['color']['d'] ) ) {
			$slide['layers']['4']['idle']['color']['d']['v'] = '#0C533D';
			$slide['layers']['4']['idle']['color']['n']['v'] = '#0C533D';
			$slide['layers']['4']['idle']['color']['t']['v'] = '#0C533D';
			$slide['layers']['4']['idle']['color']['m']['v'] = '#0C533D';
		}
	}

	// Remove floating deco layers that reused global product media.
	unset( $slide['layers']['1'], $slide['layers']['3'], $slide['layers'][1], $slide['layers'][3] );
	unset( $slide['layers']['5'], $slide['layers']['6'], $slide['layers'][5], $slide['layers'][6] );
	$new_slides[] = $slide;
}

$data['slides'] = $new_slides;
$data['params']['size']['height']['d']         = '600px';
$data['params']['size']['height']['n']         = '480px';
$data['params']['size']['height']['t']         = '520px';
$data['params']['size']['height']['m']         = '540px';
$data['params']['size']['editorCache']['d']    = 600;
$data['params']['size']['editorCache']['n']    = 480;
$data['params']['size']['editorCache']['t']    = 520;
$data['params']['size']['editorCache']['m']    = 540;
$data['params']['general']['lazyLoad']         = 'none';
$data['params']['general']['slideshow']['stopAtSlide'] = count( $new_slides );
if ( isset( $data['params']['nav']['bullets']['presets']['bullet_color'] ) ) {
	$data['params']['nav']['bullets']['presets']['bullet_color'] = '#B99531';
}

// Import slider.
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

if ( class_exists( 'RevSliderSliderImport' ) ) {
	$work_dir = WP_CONTENT_DIR . '/uploads/ng-rev-import';
	wp_mkdir_p( $work_dir );
	file_put_contents( $work_dir . '/slider_export.txt', wp_json_encode( $data ) );
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
	ng_log( ! empty( $result['success'] ) ? 'RevSlider updated: 600px, 3 slides, bottom-product layout.' : 'RevSlider import failed.' );
}

update_option(
	'revslider-global-settings',
	array(
		'forceLazyLoading' => 'none',
		'lazyonbg'         => false,
	)
);

// --- 5. Homepage Elementor: category list uses photos not SVG ---
$elements = json_decode( get_post_meta( $page_id, '_elementor_data', true ), true );
if ( is_array( $elements ) ) {
	$walk = function ( &$items ) use ( &$walk ) {
		foreach ( $items as &$el ) {
			if ( ( $el['widgetType'] ?? '' ) === 'greenpath_core_product_category_list' ) {
				$el['settings']['use_alternate_image']   = 'no';
				$el['settings']['custom_image_width']    = '360';
				$el['settings']['custom_image_height']   = '360';
			}
			if ( ! empty( $el['elements'] ) ) {
				$walk( $el['elements'] );
			}
		}
	};
	$walk( $elements );
	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
	delete_post_meta( $page_id, '_elementor_css' );
	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	ng_log( 'Homepage category slider set to uniform photo thumbs.' );
}

delete_option( 'greenpath_core_dynamic_styles' );
wp_cache_flush();
ng_log( '=== Done. Hard refresh http://localhost/nutterlyGood/ ===' );