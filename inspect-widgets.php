<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$blocks = array( 'block-13', 'block-15', 'block-16', 'block-17', 'block-18' );
foreach ( $blocks as $id ) {
	$opt = get_option( 'widget_block' );
	$num = str_replace( 'block-', '', $id );
	echo "block widget $num: ";
	if ( isset( $opt[ $num ] ) ) {
		echo substr( print_r( $opt[ $num ], true ), 0, 300 ) . PHP_EOL;
	} else {
		echo "MISSING\n";
	}
}

$widgets = get_option( 'sidebars_widgets' );
echo "\nInactive: " . print_r( $widgets['wp_inactive_widgets'] ?? array(), true );

// Check if top area renders condition
$page_id = 36;
echo "show_header_widget_areas page: " . get_post_meta( $page_id, 'qodef_show_header_widget_areas', true ) . PHP_EOL;
$opts = get_option( 'greenpath_core_options', array() );
echo "show_header_widget_areas global: " . ( $opts['qodef_show_header_widget_areas'] ?? 'unset' ) . PHP_EOL;
echo "top_area_header global: " . ( $opts['qodef_top_area_header'] ?? 'unset' ) . PHP_EOL;