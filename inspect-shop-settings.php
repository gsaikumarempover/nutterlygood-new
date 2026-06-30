<?php
require __DIR__ . '/wp-load.php';

$shop_id = get_option( 'woocommerce_shop_page_id' );
echo "shop_id={$shop_id}\n";

$keys = array(
	'qodef_woo_product_list_columns',
	'qodef_woo_product_list_sidebar_layout',
	'qodef_woo_product_list_custom_sidebar',
	'qodef_product_list_item_layout',
	'qodef_woo_product_list_products_per_page',
	'qodef_woo_product_list_sidebar_grid_gutter',
);

foreach ( $keys as $key ) {
	$val = greenpath_core_get_option_value( 'admin', $key, '(not set)' );
	echo "{$key}=" . print_r( $val, true ) . "\n";
}

if ( $shop_id ) {
	$post = get_post( $shop_id );
	echo 'shop_title=' . $post->post_title . "\n";
	echo 'shop_content_len=' . strlen( $post->post_content ) . "\n";
	echo substr( $post->post_content, 0, 1500 ) . "\n";
}