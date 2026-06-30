<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

echo "=== Category term meta ===" . PHP_EOL;
foreach ( array( 'dry-fruits', 'chips', 'mixes' ) as $slug ) {
	$t = get_term_by( 'slug', $slug, 'product_cat' );
	$svg = get_term_meta( $t->term_id, 'qodef_product_category_alternate_svg', true );
	$bg  = get_term_meta( $t->term_id, 'qodef_product_category_svg_bg', true );
	echo "$slug: svg=" . ( $svg ? strlen( $svg ) . 'b' : 'MISSING' ) . " bg=$bg" . PHP_EOL;
}

echo PHP_EOL . "=== Filter items for featured widget ===" . PHP_EOL;
$params = array(
	'tax'      => 'product_cat',
	'tax__in'  => '109, 115, 116, 119, 118',
	'tax_slug' => 'dry-fruits,chips,mixes,brittles,mouth-fresheners',
);
$items = greenpath_get_filter_items( $params );
if ( is_array( $items ) ) {
	foreach ( $items as $item ) {
		echo $item->slug . ' | ' . $item->name . PHP_EOL;
	}
} else {
	echo "NO FILTER ITEMS" . PHP_EOL;
}

echo PHP_EOL . "=== Render category list shortcode snippet ===" . PHP_EOL;
if ( class_exists( 'GreenpathCore_Product_Category_List_Shortcode' ) ) {
	$html = GreenpathCore_Product_Category_List_Shortcode::call_shortcode(
		array(
			'behavior'            => 'columns',
			'use_alternate_image' => 'yes',
			'layout'              => 'info-below',
			'additional_params'   => 'slug',
			'taxonomy_slugs'      => 'dry-fruits,chips,mixes',
			'columns'             => '3',
			'space'               => 'normal',
			'posts_per_page'      => '3',
		)
	);
	echo 'has alternate-image: ' . ( strpos( $html, 'qodef--alternate-image' ) !== false ? 'yes' : 'no' ) . PHP_EOL;
	echo 'has custom-svg: ' . ( strpos( $html, 'qodef-e-custom-svg' ) !== false ? 'yes' : 'no' ) . PHP_EOL;
	echo 'has swiper: ' . ( strpos( $html, 'swiper' ) !== false ? 'yes' : 'no' ) . PHP_EOL;
	echo 'has bg style: ' . ( strpos( $html, 'background-color' ) !== false ? 'yes' : 'no' ) . PHP_EOL;
}