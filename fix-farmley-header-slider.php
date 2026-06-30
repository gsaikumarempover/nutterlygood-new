<?php
/**
 * Farmley-style header (promo ticker, remove old top bar) + animated hero slider.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

function ng_flog( $msg ) {
	echo $msg . PHP_EOL;
}

function ng_upload_url( $rel ) {
	return trailingslashit( wp_upload_dir()['baseurl'] ) . ltrim( $rel, '/' );
}

function ng_upload_path( $rel ) {
	return trailingslashit( wp_upload_dir()['basedir'] ) . ltrim( $rel, '/' );
}

function ng_ensure_media( $rel, $title = '' ) {
	$rel = ltrim( $rel, '/' );
	global $wpdb;
	$id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value=%s LIMIT 1",
			$rel
		)
	);
	if ( $id ) {
		return (int) $id;
	}
	$path = ng_upload_path( $rel );
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$filetype  = wp_check_filetype( basename( $path ), null );
	$attach_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => $title ? $title : sanitize_file_name( pathinfo( $path, PATHINFO_FILENAME ) ),
			'post_status'    => 'inherit',
		),
		$path
	);
	if ( ! is_wp_error( $attach_id ) ) {
		wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $path ) );
	}
	return is_wp_error( $attach_id ) ? 0 : (int) $attach_id;
}

function ng_clone_text_layer( $source ) {
	$layer = json_decode( wp_json_encode( $source ), true );
	$layer['type'] = 'text';
	unset( $layer['media'], $layer['subtype'] );
	return $layer;
}

function ng_set_layer_class( &$layer, $class ) {
	if ( ! isset( $layer['attributes'] ) || ! is_array( $layer['attributes'] ) ) {
		$layer['attributes'] = array();
	}
	$layer['attributes']['classes'] = $class;
}

function ng_set_layer_top_center( &$layer, $y_desktop, $width = '' ) {
	$y_map = array(
		'd' => $y_desktop,
		'n' => (string) ( (int) $y_desktop - 6 ),
		't' => (string) ( (int) $y_desktop - 12 ),
		'm' => (string) ( (int) $y_desktop - 18 ),
	);
	foreach ( array( 'd', 'n', 't', 'm' ) as $bp ) {
		$layer['position']['horizontal'][ $bp ]['v'] = 'center';
		$layer['position']['vertical'][ $bp ]['v']   = 'top';
		$layer['position']['x'][ $bp ]['v']          = '0';
		$layer['position']['y'][ $bp ]['v']          = $y_map[ $bp ];
		$layer['idle']['textAlign'][ $bp ]['v']      = 'center';
	}
	if ( $width ) {
		$layer['size']['width']['d']['v'] = $width;
		$layer['size']['width']['n']['v'] = '90%';
		$layer['size']['width']['t']['v'] = '92%';
		$layer['size']['width']['m']['v'] = '94%';
	}
}

function ng_set_layer_farmley_anim( &$layer, $delay = 40 ) {
	if ( ! isset( $layer['timeline']['frames'] ) ) {
		return;
	}
	$layer['timeline']['frames']['frame_0'] = array(
		'transform' => array(
			'opacity' => 0,
			'y'       => array(
				'd' => array( 'v' => '60' ),
				'n' => array( 'v' => '40' ),
				't' => array( 'v' => '30' ),
				'm' => array( 'v' => '25' ),
			),
		),
		'timeline'  => array(
			'endWithSlide' => false,
			'alias'        => 'Anim From',
			'preset'       => 'sfb',
			'presetgroup'  => 'slidetrans',
		),
	);
	$layer['timeline']['frames']['frame_1'] = array(
		'transform' => array(
			'opacity' => 1,
			'y'       => array(
				'd' => array( 'v' => '0' ),
				'n' => array( 'v' => '0' ),
				't' => array( 'v' => '0' ),
				'm' => array( 'v' => '0' ),
			),
		),
		'timeline'  => array(
			'ease'           => 'power3.out',
			'speed'          => 1100,
			'start'          => $delay,
			'startRelative'  => $delay,
			'endWithSlide'   => false,
			'alias'          => 'Anim To',
			'preset'         => 'sfb',
			'presetgroup'    => 'slidetrans',
			'frameLength'    => 1100,
		),
	);
	$layer['timeline']['frames']['frame_999'] = array(
		'transform' => array( 'opacity' => 0 ),
		'timeline'  => array(
			'ease'         => 'power2.inOut',
			'speed'        => 600,
			'start'        => 3500,
			'endWithSlide' => true,
			'frameLength'  => 600,
		),
	);
}

// --- 1. Header: remove GreenPath top bar, keep clean Farmley-style main header ---
$opts = get_option( 'greenpath_core_options', array() );
if ( ! is_array( $opts ) ) {
	$opts = array();
}

$opts['qodef_top_area_header']                 = 'no';
$opts['qodef_header_layout']                   = 'standard-extended';
$opts['qodef_logo_height']                     = '48';
$opts['qodef_logo_height_mobile']              = '68';
$opts['qodef_standard_extended_header_background_color'] = '#ffffff';
$opts['qodef_standard_extended_header_bottom_background_color'] = '#0C533D';
$opts['qodef_enable_logo_overflow']            = 'no';
update_option( 'greenpath_core_options', $opts );

$page_id = (int) get_option( 'page_on_front' );
update_post_meta( $page_id, 'qodef_top_area_header', 'no' );
update_post_meta( $page_id, 'qodef_header_layout', 'standard-extended' );
update_post_meta( $page_id, 'qodef_logo_height', '48' );

$sidebars = get_option( 'sidebars_widgets', array() );
$sidebars['qodef-top-area-left']   = array();
$sidebars['qodef-top-area-center'] = array();
$sidebars['qodef-top-area-right']  = array();
$sidebars['extended-header-one']   = array(
	'greenpath_core_woo_product_search-2',
	'qode_compare_for_woocommerce_compare_counter-8',
	'greenpath_core_svg_icon-14',
	'greenpath_core_qode_wishlist-2',
	'greenpath_core_woo_side_area_cart-2',
);
$sidebars['extended-header-two']   = array();
update_option( 'sidebars_widgets', $sidebars );
ng_flog( 'Old top header removed; Farmley promo bar + clean header row active.' );

// --- 2. Farmley hero RevSlider (720px, left text animations, Shop Now) ---
$shop_url = get_permalink( wc_get_page_id( 'shop' ) );

$slides_copy = array(
	array(
		'kicker'   => 'Flavor & Freshness',
		'title'    => 'Your Everyday Treat of Tasty Goodness',
		'sub'      => 'Premium nuts & berries · Handpicked daily',
		'bg'       => '2026/06/slider/hd-heroes/source/ng-hero-01-flavor-freshness-source.jpg',
		'btn'      => 'Shop Dry Fruits',
	),
	array(
		'kicker'   => 'Premium Collection',
		'title'    => 'Handpicked Almonds, Cashews & More',
		'sub'      => 'Roasted not fried · 100% natural ingredients',
		'bg'       => '2026/06/slider/hd-heroes/source/ng-hero-02-dry-fruits-source.jpg',
		'btn'      => 'Explore Bestsellers',
	),
	array(
		'kicker'   => 'Wholesome Snacking',
		'title'    => 'Crunchy Chips & Trail Mixes',
		'sub'      => 'Better-for-you snacks · Reimagined for you',
		'bg'       => '2026/06/slider/hd-heroes/source/ng-hero-03-chips-mixes-source.jpg',
		'btn'      => 'Shop Snacks',
	),
);

$export_file = __DIR__ . '/export/revslider/main-home-extracted/slider_export.txt';
$data        = json_decode( file_get_contents( $export_file ), true );
$data['title'] = 'Nutterly Good Farmley Hero';
$data['alias'] = 'main-home';

$template = $data['slides'][0];
$new_slides = array();

foreach ( $slides_copy as $index => $copy ) {
	$slide = json_decode( wp_json_encode( $template ), true );
	$slide['id']          = (string) ( $index + 1 );
	$slide['slide_order'] = (string) ( $index + 1 );

	$attach_id = ng_ensure_media( $copy['bg'], 'Farmley Hero ' . ( $index + 1 ) );
	$bg_url    = ng_upload_url( $copy['bg'] );

	$slide['params']['bg'] = array_merge(
		$slide['params']['bg'],
		array(
			'type'            => 'image',
			'image'           => $bg_url,
			'imageId'         => $attach_id,
			'imageLib'        => 'medialibrary',
			'imageSourceType' => 'full',
			'fit'             => 'cover',
			'position'        => 'center bottom',
			'repeat'          => 'no-repeat',
		)
	);
	$slide['params']['slideChange'] = array(
		'in'    => array( 'o' => 0 ),
		'out'   => array( 'o' => 0, 's' => 800 ),
		'title' => '*opacity* Fade In',
		'main'  => 'basic',
		'group' => 'fade',
		'preset'=> 'fade',
	);

	$text_tpl = $slide['layers']['4'] ?? null;

	// Kicker — top-center on cream band (above product imagery).
	if ( isset( $slide['layers']['0'] ) ) {
		$slide['layers']['0']['text'] = $copy['kicker'];
		ng_set_layer_top_center( $slide['layers']['0'], '38' );
		$slide['layers']['0']['idle']['fontFamily']              = 'Manrope';
		$slide['layers']['0']['idle']['fontSize']['d']['v']      = '14px';
		$slide['layers']['0']['idle']['lineHeight']['d']['v']    = '20px';
		$slide['layers']['0']['idle']['letterSpacing']['d']['v'] = '3px';
		unset( $slide['layers']['0']['idle']['textTransform'] );
		$slide['layers']['0']['idle']['fontWeight']['d']['v']    = '700';
		$slide['layers']['0']['idle']['color']['d']['v']         = '#B99531';
		ng_set_layer_class( $slide['layers']['0'], 'ng-farmley-kicker' );
		ng_set_layer_farmley_anim( $slide['layers']['0'], 80 );
	}

	// Main title — centered in upper white area.
	if ( isset( $slide['layers']['4'] ) ) {
		$slide['layers']['4']['text'] = $copy['title'];
		ng_set_layer_top_center( $slide['layers']['4'], '66', '860px' );
		$slide['layers']['4']['idle']['fontFamily']           = 'Amatic SC';
		$slide['layers']['4']['idle']['fontSize']['d']['v']   = '64px';
		$slide['layers']['4']['idle']['fontSize']['n']['v']   = '56px';
		$slide['layers']['4']['idle']['fontSize']['t']['v']   = '48px';
		$slide['layers']['4']['idle']['fontSize']['m']['v']   = '40px';
		$slide['layers']['4']['idle']['lineHeight']['d']['v'] = '68px';
		$slide['layers']['4']['idle']['lineHeight']['n']['v'] = '60px';
		$slide['layers']['4']['idle']['lineHeight']['t']['v'] = '52px';
		$slide['layers']['4']['idle']['lineHeight']['m']['v'] = '44px';
		$slide['layers']['4']['idle']['fontWeight']['d']['v'] = '700';
		$slide['layers']['4']['idle']['color']['d']['v']      = '#0C533D';
		ng_set_layer_class( $slide['layers']['4'], 'ng-farmley-title' );
		ng_set_layer_farmley_anim( $slide['layers']['4'], 200 );
	}

	// Subtitle — clone text layer (do not convert image layers).
	if ( $text_tpl ) {
		$slide['layers']['1'] = ng_clone_text_layer( $text_tpl );
		$slide['layers']['1']['uid']   = 1;
		$slide['layers']['1']['alias'] = 'Text-1';
		$slide['layers']['1']['text']  = $copy['sub'];
		ng_set_layer_top_center( $slide['layers']['1'], '148', '720px' );
		$slide['layers']['1']['idle']['fontFamily']           = 'Manrope';
		$slide['layers']['1']['idle']['fontSize']['d']['v']   = '18px';
		$slide['layers']['1']['idle']['lineHeight']['d']['v'] = '28px';
		$slide['layers']['1']['idle']['color']['d']['v']      = '#3d3d3d';
		ng_set_layer_class( $slide['layers']['1'], 'ng-farmley-sub' );
		ng_set_layer_farmley_anim( $slide['layers']['1'], 340 );
	}

	// CTA button — clone text layer.
	if ( $text_tpl ) {
		$slide['layers']['3'] = ng_clone_text_layer( $text_tpl );
		$slide['layers']['3']['uid']   = 3;
		$slide['layers']['3']['alias'] = 'Text-3';
		$slide['layers']['3']['text']  = '<a href="' . esc_url( $shop_url ) . '">' . esc_html( $copy['btn'] ) . '</a>';
		ng_set_layer_top_center( $slide['layers']['3'], '188' );
		$slide['layers']['3']['idle']['fontFamily']         = 'Manrope';
		$slide['layers']['3']['idle']['fontSize']['d']['v'] = '14px';
		$slide['layers']['3']['idle']['color']['d']['v']    = '#ffffff';
		ng_set_layer_class( $slide['layers']['3'], 'ng-farmley-btn' );
		ng_set_layer_farmley_anim( $slide['layers']['3'], 480 );
	}

	unset( $slide['layers']['5'], $slide['layers']['6'], $slide['layers'][5], $slide['layers'][6] );
	$new_slides[] = $slide;
}

$data['slides'] = $new_slides;
$data['params']['size']['height']['d']  = '720px';
$data['params']['size']['height']['n']  = '600px';
$data['params']['size']['height']['t']  = '560px';
$data['params']['size']['height']['m']  = '520px';
$data['params']['size']['editorCache']['d'] = 720;
$data['params']['size']['editorCache']['n'] = 600;
$data['params']['size']['editorCache']['t'] = 560;
$data['params']['size']['editorCache']['m'] = 520;
$data['params']['general']['lazyLoad']  = 'none';
$data['params']['general']['slideshow']['stopAtSlide'] = count( $new_slides );
$data['params']['class'] = 'ng-farmley-hero';
if ( isset( $data['params']['nav']['bullets']['presets']['bullet_color'] ) ) {
	$data['params']['nav']['bullets']['presets']['bullet_color'] = '#B99531';
}

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
	$zip_path = $work_dir . '/main-home-farmley.zip';
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
	ng_flog( ! empty( $result['success'] ) ? 'Farmley hero slider imported (720px, animated text + CTA).' : 'RevSlider import failed.' );
}

// Elementor: add farmley class wrapper on homepage slider.
$elements = json_decode( get_post_meta( $page_id, '_elementor_data', true ), true );
if ( is_array( $elements ) ) {
	$walk = function ( &$items ) use ( &$walk ) {
		foreach ( $items as &$el ) {
			if ( ( $el['widgetType'] ?? '' ) === 'slider_revolution' ) {
				$el['settings']['_css_classes'] = 'ng-farmley-hero';
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
}

delete_option( 'greenpath_core_dynamic_styles' );
wp_cache_flush();
ng_flog( '=== Farmley header + slider done. Hard refresh http://localhost/nutterlyGood/ ===' );