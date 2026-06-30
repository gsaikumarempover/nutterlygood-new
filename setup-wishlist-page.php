<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$wishlist_id = (int) get_option( 'qode_wishlist_for_woocommerce_page_template', 0 );
if ( $wishlist_id <= 0 || ! get_post( $wishlist_id ) ) {
	$wishlist_id = wp_insert_post(
		array(
			'post_title'   => 'Wishlist',
			'post_name'    => 'wishlist',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '[qode_wishlist_for_woocommerce_table]',
		)
	);
}

if ( $wishlist_id && ! is_wp_error( $wishlist_id ) ) {
	$opts = get_option( 'qode_wishlist_for_woocommerce_options', array() );
	if ( ! is_array( $opts ) ) {
		$opts = array();
	}
	$opts['qode_wishlist_for_woocommerce_page_template'] = (string) $wishlist_id;
	update_option( 'qode_wishlist_for_woocommerce_options', $opts );
	echo 'Wishlist page: ' . get_permalink( $wishlist_id ) . PHP_EOL;
}

wp_cache_flush();
echo "Done.\n";