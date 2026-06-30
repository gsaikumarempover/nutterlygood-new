<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$q = new WP_Query(
	array(
		'post_type'      => 'product',
		'posts_per_page' => 20,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);

foreach ( $q->posts as $p ) {
	$prod = wc_get_product( $p->ID );
	if ( ! $prod ) {
		continue;
	}
	echo $p->ID . ' | ' . $p->post_title . ' | mrp=' . get_post_meta( $p->ID, '_ng_mrp', true ) . ' | offer=' . get_post_meta( $p->ID, '_ng_offer_price', true ) . ' | wc=' . $prod->get_regular_price() . '/' . $prod->get_sale_price() . PHP_EOL;
}