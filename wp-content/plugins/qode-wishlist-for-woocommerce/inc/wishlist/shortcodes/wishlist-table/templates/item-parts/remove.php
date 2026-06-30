<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo apply_filters(
	'qode_wishlist_for_woocommerce_filter_wishlist_table_item_remove_link',
	sprintf(
		'<a href="#" class="qwfw-e-remove-button qwfw-spinner-item" aria-label="%s" data-item-id="%s" data-confirm-text="%s" rel="noopener noreferrer">%s%s</a>',
		esc_attr__( 'Remove this item', 'qode-wishlist-for-woocommerce' ),
		esc_attr( $item_id ),
		esc_attr__( 'Are you sure you want to remove this item?', 'qode-wishlist-for-woocommerce' ),
		'<span class="qwfw-e-remove-button-icon">' . qode_wishlist_for_woocommerce_get_svg_icon( 'close' ) . '</span>',
		'<span class="qwfw-e-remove-button-spinner qwfw-spinner-icon">' . qode_wishlist_for_woocommerce_get_svg_icon( 'spinner' ) . '</span>'
	),
	$product,
	$item_id
);
