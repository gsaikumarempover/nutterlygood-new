<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$products = wc_get_products( array( 'limit' => 6, 'status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ) );

foreach ( $products as $p ) {
	$mode     = get_post_meta( $p->get_id(), '_ng_card_image_mode', true );
	$primary  = nuttergood_farmley_get_card_primary_image_id( $p );
	$hover    = nuttergood_farmley_get_product_hover_image_id( $p, $primary );
	$pfile    = get_post_meta( $primary, '_wp_attached_file', true );
	$hfile    = $hover ? get_post_meta( $hover, '_wp_attached_file', true ) : '';
	echo $p->get_name() . " [{$mode}]" . PHP_EOL;
	echo "  primary: {$pfile}" . PHP_EOL;
	echo "  hover:   {$hfile}" . PHP_EOL;
}