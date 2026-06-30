<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$product_name = $product->get_name();

if ( $product_permalink ) {
	$product_name = sprintf( '<a class="qcfw--product-name" href="%s">%s</a>', esc_url( apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_item_link', $product_permalink, $product, $item_id ) ), $product->get_name() );
} else {
	$product_name = '<span class="qcfw--product-name">' . $product->get_name() . '</span>';

}

echo wp_kses_post( apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_item_name', $product_name, $product, $item_id ) );
