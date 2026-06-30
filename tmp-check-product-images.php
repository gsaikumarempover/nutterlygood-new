<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$q = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => 40, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
foreach ( $q->posts as $p ) {
	$prod = wc_get_product( $p->ID );
	if ( ! $prod ) {
		continue;
	}
	$thumb_id = $prod->get_image_id();
	$url      = $thumb_id ? wp_get_attachment_url( $thumb_id ) : '';
	$ai       = get_post_meta( $p->ID, '_ng_ai_product_image', true );
	$is_png   = $url && str_contains( $url, '.png' );
	$is_ai    = $url && str_contains( $url, 'ai-products' );
	$is_webp  = $url && str_contains( $url, '.webp' );
	echo $p->ID . ' | ' . $p->post_title . ' | ' . basename( (string) $url ) . ' | ai_meta=' . $ai . ' | png=' . ( $is_png ? 'y' : 'n' ) . ' | ai=' . ( $is_ai ? 'y' : 'n' ) . ' | webp=' . ( $is_webp ? 'y' : 'n' ) . PHP_EOL;
}