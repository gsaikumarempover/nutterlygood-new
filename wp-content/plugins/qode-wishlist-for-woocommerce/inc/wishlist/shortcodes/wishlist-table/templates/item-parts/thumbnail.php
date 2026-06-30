<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$product_image = $product->get_image();

if ( ! empty( $product_image ) ) {
	$product_image_permalink = $product->get_permalink();

	if ( $product_image_permalink ) {
		$product_image = sprintf( '<a href="%s">%s</a>', esc_url( apply_filters( 'qode_wishlist_for_woocommerce_filter_wishlist_table_item_link', $product_image_permalink, $product, $item_id ) ), $product_image );
	}

	echo wp_kses_post( apply_filters( 'qode_wishlist_for_woocommerce_filter_wishlist_table_item_thumbnail', $product_image, $product, $item_id ) );
}
