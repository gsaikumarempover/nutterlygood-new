<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$dimensions = function_exists( 'wc_format_dimensions' ) ? wc_format_dimensions( $product->get_dimensions( false ) ) : $product->get_dimensions();

echo wp_kses_post( apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_dimensions', $dimensions, $product, $item_id ) );
