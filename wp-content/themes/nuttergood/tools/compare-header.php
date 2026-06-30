<?php
require dirname( __DIR__, 4 ) . '/wp-load.php';

$pages = array(
	'home'    => (int) get_option( 'page_on_front' ),
	'about'   => 3431,
	'contact' => 3437,
	'shop'    => wc_get_page_id( 'shop' ),
);

$keys = array(
	'qodef_header_custom_widget_area_one',
	'qodef_header_custom_widget_area_two',
	'qodef_sticky_header_custom_widget_area_one',
	'qodef_page_header_layout',
);

foreach ( $pages as $label => $id ) {
	echo "=== {$label} ({$id}) ===\n";
	foreach ( $keys as $key ) {
		$val = get_post_meta( $id, $key, true );
		if ( $val ) {
			echo "{$key} = {$val}\n";
		}
	}
	echo "\n";
}

$urls = array(
	'home'    => home_url( '/' ),
	'about'   => get_permalink( 3431 ),
	'contact' => get_permalink( 3437 ),
	'shop'    => get_permalink( wc_get_page_id( 'shop' ) ),
);

foreach ( $urls as $label => $url ) {
	$html = file_get_contents( $url );
	echo "=== {$label} header markers ===\n";
	$markers = array(
		'ng-farmley-header-search-menu-group',
		'ng-farmley-header-menu-slot',
		'greenpath_core_woo_product_search',
		'qodef-header-logo-link',
		'qodef-header-section qodef--bottom',
	);
	foreach ( $markers as $m ) {
		echo $m . ': ' . ( false !== strpos( $html, $m ) ? 'yes' : 'no' ) . "\n";
	}
	if ( preg_match( '/qodef-header-logo-link[^>]*>.*?max-height|height:\s*(\d+)px[^;]*qodef-header-logo/', $html, $m ) ) {
		echo "logo style match\n";
	}
	if ( preg_match( '/#qodef-page-header \.qodef-header-logo-link \{ height: (\d+)px;/', $html, $m ) ) {
		echo 'logo height inline: ' . $m[1] . "px\n";
	}
	echo "\n";
}