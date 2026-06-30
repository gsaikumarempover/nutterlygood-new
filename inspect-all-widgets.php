<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$widget_ids = array(
	'greenpath_core_woo_product_search-2',
	'qode_compare_for_woocommerce_compare_counter-8',
	'greenpath_core_svg_icon-14',
	'greenpath_core_qode_wishlist-2',
	'greenpath_core_woo_side_area_cart-2',
	'greenpath_core_svg_icon-25',
	'greenpath_core_svg_icon-16',
	'greenpath_core_svg_icon-17',
	'greenpath_core_svg_icon-18',
);

foreach ( $widget_ids as $wid ) {
	if ( preg_match( '/^(.+)-(\d+)$/', $wid, $m ) ) {
		$base = $m[1];
		$num  = $m[2];
		$opt  = get_option( 'widget_' . $base, array() );
		echo "$wid: " . ( isset( $opt[ $num ] ) ? 'exists' : 'MISSING' ) . PHP_EOL;
		if ( isset( $opt[ $num ] ) ) {
			echo '  ' . substr( print_r( $opt[ $num ], true ), 0, 200 ) . PHP_EOL;
		}
	}
}