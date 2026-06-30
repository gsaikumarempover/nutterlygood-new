<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

echo wp_kses_post( apply_filters( 'qode_wishlist_for_woocommerce_filter_wishlist_table_item_price', $product->get_price_html(), $product, $item_id ) );
