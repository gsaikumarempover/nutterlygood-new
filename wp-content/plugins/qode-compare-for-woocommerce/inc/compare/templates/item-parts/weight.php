<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$weight = ! empty( $product->get_weight() ) ? ( wc_format_localized_decimal( $product->get_weight() ) . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) ) ) : '/';

echo wp_kses_post( apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_weight', $weight, $product, $item_id ) );
