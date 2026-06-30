<?php
require __DIR__ . '/wp-load.php';

$all = wc_get_products( array( 'limit' => 15, 'status' => 'publish', 'orderby' => 'date' ) );
echo 'Total sample: ' . count( $all ) . "\n\n";
foreach ( $all as $p ) {
	echo "ID: {$p->get_id()} | {$p->get_name()} | {$p->get_type()} | price: {$p->get_price()} | regular: {$p->get_regular_price()} | sale: {$p->get_sale_price()}\n";
	$gallery = $p->get_gallery_image_ids();
	echo '  gallery: ' . count( $gallery ) . ' ids: ' . implode( ',', $gallery ) . "\n";
	echo '  weight: ' . $p->get_weight() . "\n";
	echo '  short: ' . substr( wp_strip_all_tags( $p->get_short_description() ), 0, 80 ) . "\n";
	echo '  desc: ' . substr( wp_strip_all_tags( $p->get_description() ), 0, 80 ) . "\n";
	foreach ( $p->get_attributes() as $a ) {
		echo '  attr ' . $a->get_name() . ': ' . implode( ',', $a->get_options() ) . "\n";
	}
	echo "\n";
}