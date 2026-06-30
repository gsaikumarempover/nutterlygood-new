<?php
define( 'SAVEQUERIES', true );
define( 'WP_USE_THEMES', true );

$start = microtime( true );
require __DIR__ . '/wp-load.php';

ob_start();
require ABSPATH . 'wp-blog-header.php';
$html = ob_get_clean();

$total = microtime( true ) - $start;
global $wpdb;

$queries = is_array( $wpdb->queries ) ? $wpdb->queries : array();
$query_time = 0;
foreach ( $queries as $q ) {
	$query_time += (float) $q[1];
}

preg_match_all( '/<script[^>]+src=["\']([^"\']+)["\']/', $html, $scripts );
preg_match_all( '/<link[^>]+href=["\']([^"\']+\.css[^"\']*)["\']/', $html, $styles );
preg_match_all( '/<img[^>]+src=["\']([^"\']+)["\']/', $html, $images );

echo "Total PHP time: " . round( $total, 3 ) . "s\n";
echo "DB queries: " . count( $queries ) . " (" . round( $query_time, 3 ) . "s)\n";
echo "HTML size: " . number_format( strlen( $html ) ) . " bytes\n";
echo "CSS files: " . count( $styles[1] ) . "\n";
echo "JS files: " . count( $scripts[1] ) . "\n";
echo "IMG tags: " . count( $images[1] ) . "\n";
echo "\nActive plugins:\n";
foreach ( get_option( 'active_plugins', array() ) as $p ) {
	echo "  - $p\n";
}
echo "\nSlowest queries (>0.01s):\n";
usort(
	$queries,
	static function ( $a, $b ) {
		return $b[1] <=> $a[1];
	}
);
$shown = 0;
foreach ( $queries as $q ) {
	if ( $q[1] < 0.01 ) {
		break;
	}
	echo round( $q[1], 4 ) . 's  ' . substr( preg_replace( '/\s+/', ' ', $q[0] ), 0, 120 ) . "\n";
	$shown++;
	if ( $shown >= 10 ) {
		break;
	}
}
echo "\nScripts:\n";
foreach ( array_unique( $scripts[1] ) as $s ) {
	echo "  $s\n";
}