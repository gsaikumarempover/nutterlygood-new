<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

global $wp_registered_widgets, $wp_registered_sidebars;

$sidebars = get_option( 'sidebars_widgets', array() );
echo "Top left: " . print_r( $sidebars['qodef-top-area-left'] ?? 'empty', true );
echo "Top right: " . print_r( $sidebars['qodef-top-area-right'] ?? 'empty', true );
echo "Ext one: " . print_r( $sidebars['extended-header-one'] ?? 'empty', true );
echo "Ext two: " . print_r( $sidebars['extended-header-two'] ?? 'empty', true );

$opts = get_option( 'greenpath_core_options', array() );
echo "top_area: " . ( $opts['qodef_top_area_header'] ?? 'unset' ) . PHP_EOL;
echo "header_layout: " . ( $opts['qodef_header_layout'] ?? 'unset' ) . PHP_EOL;

$page_id = 36;
foreach ( array( 'qodef_top_area_header', 'qodef_header_layout', 'qodef_standard_extended_header_background_color', 'qodef_enable_logo_overflow' ) as $k ) {
	echo "page36 $k: " . get_post_meta( $page_id, $k, true ) . PHP_EOL;
}