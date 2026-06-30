<?php
require __DIR__ . '/wp-load.php';

$products = wc_get_products( array( 'limit' => 8, 'status' => 'publish', 'return' => 'objects' ) );
foreach ( $products as $product ) {
	$raw   = get_post_meta( $product->get_id(), '_ng_farmley_sizes', true );
	$sizes = json_decode( (string) $raw, true );
	$count = is_array( $sizes ) ? count( $sizes ) : 0;
	echo $product->get_name() . " => {$count} sizes\n";
	if ( is_array( $sizes ) ) {
		foreach ( $sizes as $size ) {
			echo '  - ' . ( $size['label'] ?? $size['weight'] ?? '?' ) . ' @ ' . ( $size['price'] ?? '?' ) . "\n";
		}
	}
}