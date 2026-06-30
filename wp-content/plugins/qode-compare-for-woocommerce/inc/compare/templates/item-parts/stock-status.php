<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$stock_status_class = 'qcfw--in-stock';
$stock_status_label = esc_html__( 'In stock', 'qode-compare-for-woocommerce' );
$stock_status_title = '';

if ( ! $product->is_in_stock() ) {
	$stock_status_class = 'qcfw--out-of-stock';
	$stock_status_label = esc_html__( 'Out of stock', 'qode-compare-for-woocommerce' );
}

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo apply_filters(
	'qode_compare_for_woocommerce_filter_comparison_table_item_stock_status',
	sprintf(
		'<span class="qcfw-m-item-stock %s" title="%s">%s</span>',
		$stock_status_class,
		$stock_status_title,
		$stock_status_label
	),
	$product,
	$item_id
);
