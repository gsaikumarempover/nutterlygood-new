<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'qode_compare_for_woocommerce_filter_comparison_table_item_remove_link',
	sprintf(
		'<a href="#" class="qcfw-m-remove-button qcfw-spinner-item" aria-label="%s" data-item-id="%s" data-confirm-text="%s" rel="noopener noreferrer">%s%s</a>',
		esc_attr__( 'Remove this item', 'qode-compare-for-woocommerce' ),
		esc_attr( $item_id ),
		esc_attr__( 'Are you sure you want to remove this item?', 'qode-compare-for-woocommerce' ),
		'<span class="qcfw-m-remove-button-icon">' . qode_compare_for_woocommerce_get_svg_icon( 'close' ) . '</span>',
		'<span class="qcfw-m-remove-button-spinner qcfw-spinner-icon">' . qode_compare_for_woocommerce_get_svg_icon( 'spinner' ) . '</span>'
	),
	$product,
	$item_id
);
