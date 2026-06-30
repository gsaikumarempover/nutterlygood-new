<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$products = wc_get_products(
	array(
		'limit'  => -1,
		'status' => 'publish',
		'return' => 'ids',
	)
);

echo 'Total products: ' . count( $products ) . PHP_EOL;

$with_list_img = 0;
$hd            = 0;
$legacy        = 0;
$webp          = 0;

foreach ( $products as $id ) {
	$list = get_post_meta( $id, 'qodef_product_list_image', true );
	if ( $list ) {
		++$with_list_img;
	}
	$thumb = get_post_thumbnail_id( $id );
	if ( ! $thumb ) {
		continue;
	}
	$file = (string) get_post_meta( $thumb, '_wp_attached_file', true );
	if ( str_contains( $file, 'ai-products' ) || str_contains( $file, '.png' ) ) {
		++$hd;
	} elseif ( str_contains( $file, '.webp' ) ) {
		++$webp;
	} else {
		++$legacy;
	}
}

echo "qodef_product_list_image set: {$with_list_img}" . PHP_EOL;
echo "Featured: HD/png={$hd} webp={$webp} other={$legacy}" . PHP_EOL;

$i = 0;
foreach ( $products as $id ) {
	if ( $i++ >= 12 ) {
		break;
	}
	$p     = wc_get_product( $id );
	$list  = get_post_meta( $id, 'qodef_product_list_image', true );
	$thumb = get_post_thumbnail_id( $id );
	$tf    = $thumb ? get_post_meta( $thumb, '_wp_attached_file', true ) : '';
	$lf    = $list ? get_post_meta( $list, '_wp_attached_file', true ) : '';
	echo $p->get_name() . " | thumb:{$tf} | list:{$lf}" . PHP_EOL;
}