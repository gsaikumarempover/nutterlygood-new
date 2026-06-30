<?php
/**
 * Enable WooCommerce shop + GreenPath four-column filter grid settings.
 */
require __DIR__ . '/wp-load.php';

if ( ! class_exists( 'WooCommerce' ) ) {
	fwrite( STDERR, "WooCommerce is not active.\n" );
	exit( 1 );
}

update_option( 'woocommerce_coming_soon', 'no' );
update_option( 'woocommerce_store_pages_only', 'no' );

$options = get_option( 'greenpath_core_options', array() );
if ( ! is_array( $options ) ) {
	$options = array();
}

$options['qodef_woo_product_list_columns']             = '4';
$options['qodef_woo_product_list_sidebar_layout']      = 'no-sidebar';
$options['qodef_product_list_item_layout']            = 'info-below';
$options['qodef_woo_product_list_products_per_page']  = '12';
$options['qodef_woo_product_list_columns_space']       = 'small';
$options['qodef_woo_product_list_columns_vertical_space'] = 'normal';

update_option( 'greenpath_core_options', $options );

$shop_id = (int) get_option( 'woocommerce_shop_page_id' );
if ( $shop_id ) {
	wp_update_post(
		array(
			'ID'           => $shop_id,
			'post_content' => '',
		)
	);
}

if ( function_exists( 'wp_cache_flush' ) ) {
	wp_cache_flush();
}

echo "Shop setup complete.\n";
echo 'coming_soon=' . get_option( 'woocommerce_coming_soon' ) . "\n";
echo 'columns=' . ( $options['qodef_woo_product_list_columns'] ?? '' ) . "\n";
echo 'shop_url=' . get_permalink( $shop_id ) . "\n";