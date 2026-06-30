<?php
/**
 * GreenPath-style header/menu: cream top bar, white logo row, green nav row,
 * categories dropdown colors, simplified main menu, slider height 660px.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/file.php';

function ng_hmg_log( $msg ) {
	echo $msg . PHP_EOL;
}

// --- 1. Run existing GreenPath header widget/menu setup ---
require __DIR__ . '/fix-header-greenpath.php';

// --- 2. Override header colors + enable GreenPath top bar (not Farmley promo) ---
$opts = get_option( 'greenpath_core_options', array() );
if ( ! is_array( $opts ) ) {
	$opts = array();
}

$header_opts = array(
	'qodef_top_area_header'                          => 'yes',
	'qodef_top_area_header_in_grid'                  => 'yes',
	'qodef_top_area_header_skin'                     => 'none',
	'qodef_top_area_header_height'                   => '40',
	'qodef_top_area_header_side_padding'             => '60',
	'qodef_top_area_header_background_color'         => '#FCF4EB',
	'qodef_top_area_header_border_color'             => '#E8E0D6',
	'qodef_top_area_header_border_width'             => '1',
	'qodef_top_area_header_border_style'             => 'solid',
	'qodef_header_layout'                            => 'standard-extended',
	'qodef_standard_extended_show_extended_dropdown' => 'yes',
	'qodef_standard_extended_extended_dropdown_opener_label' => 'Shop by Categories',
	'qodef_standard_extended_bottom_skin'            => 'light',
	'qodef_standard_extended_header_in_grid'         => 'yes',
	'qodef_standard_extended_header_height'          => '156',
	'qodef_standard_extended_header_top_background_color'    => '#FFFFFF',
	'qodef_standard_extended_header_top_border_color'        => '#E8E8E8',
	'qodef_standard_extended_header_top_border_width'        => '1',
	'qodef_standard_extended_header_top_border_style'        => 'solid',
	'qodef_standard_extended_header_bottom_background_color' => '#0C533D',
	'qodef_standard_extended_header_background_color'        => '',
	'qodef_logo_height'                              => '48',
	'qodef_logo_height_mobile'                       => '68',
	'qodef_show_header_widget_areas'                 => 'yes',
);
$opts = array_merge( $opts, $header_opts );
update_option( 'greenpath_core_options', $opts );

$page_id = (int) get_option( 'page_on_front' );
foreach ( $header_opts as $key => $val ) {
	update_post_meta( $page_id, $key, $val );
}
ng_hmg_log( 'Header colors: cream top bar, white logo row, green menu row.' );

// --- 3. GreenPath-style main menu (categories only in dropdown, not main nav) ---
$shop_url    = get_permalink( wc_get_page_id( 'shop' ) );
$about_url   = get_permalink( 3431 );
$contact_url = get_permalink( 3437 );
$home_url    = home_url( '/' );

function ng_hmg_clear_menu( $menu_id ) {
	$items = wp_get_nav_menu_items( $menu_id );
	if ( $items ) {
		foreach ( $items as $item ) {
			wp_delete_post( $item->ID, true );
		}
	}
}

function ng_hmg_add_items( $menu_id, $items ) {
	$order = 1;
	foreach ( $items as $item ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'    => $item['title'],
				'menu-item-url'      => $item['url'],
				'menu-item-status'   => 'publish',
				'menu-item-type'     => 'custom',
				'menu-item-position' => $order++,
			)
		);
	}
}

$main_menu = array(
	array( 'title' => 'Home', 'url' => $home_url ),
	array( 'title' => 'Shop', 'url' => $shop_url ),
	array( 'title' => 'About Us', 'url' => $about_url ),
	array( 'title' => 'Contact Us', 'url' => $contact_url ),
);

ng_hmg_clear_menu( 76 );
ng_hmg_add_items( 76, $main_menu );
ng_hmg_log( 'Main menu rebuilt (GreenPath style: Home, Shop, About, Contact).' );

// --- 4. RevSlider height 660px ---
global $wpdb;
$slider = $wpdb->get_row( "SELECT id, params FROM {$wpdb->prefix}revslider_sliders WHERE alias = 'main-home' LIMIT 1" );
if ( $slider ) {
	$params = json_decode( $slider->params, true );
	if ( is_array( $params ) ) {
		if ( ! isset( $params['size'] ) ) {
			$params['size'] = array();
		}
		if ( ! isset( $params['size']['height'] ) ) {
			$params['size']['height'] = array();
		}
		$params['size']['height']['d'] = '660px';
		$params['size']['height']['n'] = '560px';
		$params['size']['height']['t'] = '520px';
		$params['size']['height']['m'] = '480px';
		if ( ! isset( $params['size']['editorCache'] ) ) {
			$params['size']['editorCache'] = array();
		}
		$params['size']['editorCache']['d'] = 660;
		$wpdb->update(
			$wpdb->prefix . 'revslider_sliders',
			array( 'params' => wp_json_encode( $params ) ),
			array( 'id' => $slider->id ),
			array( '%s' ),
			array( '%d' )
		);
		ng_hmg_log( 'RevSlider main-home height set to 660px (desktop).' );
	}
}

delete_option( 'greenpath_core_dynamic_styles' );
wp_cache_flush();
ng_hmg_log( '=== Done. Hard refresh http://localhost/nutterlyGood/ ===' );