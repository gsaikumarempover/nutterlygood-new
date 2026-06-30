<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$ids = array( 7585, 7583, 7523, 7525, 7515 );
foreach ( $ids as $id ) {
	$p = wc_get_product( $id );
	echo $id . '|gallery=' . implode( ',', $p->get_gallery_image_ids() ) . '|thumb=' . $p->get_image_id() . PHP_EOL;
}