<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$uploads = WP_CONTENT_DIR . '/uploads/2026/06/';
$webps   = glob( $uploads . '*.webp' );
$legacy  = array();
foreach ( $webps as $path ) {
	$name = basename( $path );
	if ( preg_match( '/-1\.webp$/', $name ) ) {
		continue;
	}
	$legacy[] = $name;
}
sort( $legacy );

$products = wc_get_products( array( 'limit' => -1, 'status' => 'publish' ) );
foreach ( $products as $p ) {
	$slug  = $p->get_slug();
	$title = $p->get_name();
	$guess = str_replace( ' ', '-', $title ) . '.webp';
	$hit   = in_array( $guess, $legacy, true ) ? $guess : '';
	if ( ! $hit ) {
		foreach ( $legacy as $w ) {
			$stem = pathinfo( $w, PATHINFO_FILENAME );
			if ( stripos( $title, str_replace( '-', ' ', $stem ) ) !== false || stripos( $stem, str_replace( ' ', '-', strtolower( $title ) ) ) !== false ) {
				$hit = $w;
				break;
			}
		}
	}
	echo "{$slug} | {$title} | legacy:{$hit}" . PHP_EOL;
}