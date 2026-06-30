<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

echo wp_kses_post( apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_sku', $product->get_sku(), $product, $item_id ) );
