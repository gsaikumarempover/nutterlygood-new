<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$ids = array( 7515, 7585, 7523 );
foreach ( $ids as $id ) {
	$p    = wc_get_product( $id );
	$meta = nuttergood_farmley_get_product_meta( $p );
	echo $id . '|mrp=' . ( $meta['mrp'] ?? '' ) . '|offer=' . ( $meta['offer_price'] ?? '' ) . '|reg=' . $p->get_regular_price() . '|sale=' . $p->get_sale_price() . '|price=' . $p->get_price() . PHP_EOL;
}