<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
$table_items_option = (array) qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_table_items' );
$variations_meta_on = in_array( 'variations', $table_items_option, true );
$product_name       = $variations_meta_on && $product->is_type( 'variation' ) ? get_the_title( $product->get_parent_id() ) : $product->get_name();

if ( $product_permalink ) {
	$product_name = sprintf( '<a class="qwfw-e-item-name" href="%s">%s</a>', esc_url( apply_filters( 'qode_wishlist_for_woocommerce_filter_wishlist_table_item_link', $product_permalink, $product, $item_id ) ), $product_name );
}

echo wp_kses_post( apply_filters( 'qode_wishlist_for_woocommerce_filter_wishlist_table_item_name', $product_name, $product, $item_id ) );
