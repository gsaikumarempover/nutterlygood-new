<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$product_categories = wc_get_product_category_list( $product->is_type( 'variation' ) ? $product->get_parent_id() : $item_id, apply_filters( 'qode_wishlist_for_woocommerce_filter_wishlist_table_item_category_separator', ', ', $product, $item_id ), '<span class="qwfw-e-item-category">', '</span>' );

if ( ! empty( $product_categories ) ) {
	echo wp_kses_post(
		apply_filters(
			'qode_wishlist_for_woocommerce_filter_wishlist_table_item_category',
			$product_categories,
			$product,
			$item_id
		)
	);
}
