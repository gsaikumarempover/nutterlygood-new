<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( is_object( $product ) && $product->is_type( 'variation' ) ) {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo apply_filters(
		'qode_wishlist_for_woocommerce_filter_wishlist_table_item_variation',
		wc_get_formatted_variation( $product ),
		$product,
		$item_id
	);
}
