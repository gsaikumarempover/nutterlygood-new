<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$ids = array( 7521, 7517, 7525, 7529, 7547, 7551 );
foreach ( $ids as $pid ) {
	$p = wc_get_product( $pid );
	if ( ! $p ) {
		continue;
	}
	$thumb = get_post_thumbnail_id( $pid );
	$url   = wp_get_attachment_image_url( $thumb, 'woocommerce_thumbnail' );
	$full  = wp_get_attachment_image_url( $thumb, 'full' );
	$file  = get_post_meta( $thumb, '_wp_attached_file', true );
	$gal   = $p->get_gallery_image_ids();
	$gurl  = $gal ? wp_get_attachment_image_url( $gal[0], 'woocommerce_thumbnail' ) : '';
	$gfile = $gal ? get_post_meta( $gal[0], '_wp_attached_file', true ) : '';
	echo $p->get_name() . PHP_EOL;
	echo "  thumb file: {$file}" . PHP_EOL;
	echo "  thumb url:  {$url}" . PHP_EOL;
	echo "  hover file: {$gfile}" . PHP_EOL;
	echo "  hover url:  {$gurl}" . PHP_EOL;
}