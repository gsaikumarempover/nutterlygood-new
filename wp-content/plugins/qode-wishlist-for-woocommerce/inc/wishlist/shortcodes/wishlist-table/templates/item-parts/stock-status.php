<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$stock_status_class = 'qwfw--in-stock';
$stock_status_label = esc_html__( 'In stock', 'qode-wishlist-for-woocommerce' );
$stock_status_title = '';

if ( ! $product->is_in_stock() ) {
	$stock_status_class = 'qwfw--out-of-stock';
	$stock_status_label = esc_html__( 'Out of stock', 'qode-wishlist-for-woocommerce' );

	$notify_me = 'yes' === qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_enable_back_in_stock_email_notification' );

	if ( $notify_me ) {
		$stock_status_title  = esc_attr__( 'Notify user when product is back in stock', 'qode-wishlist-for-woocommerce' );
		$stock_status_label .= '<span class="qwfw-e-item-stock-icon">' . qode_wishlist_for_woocommerce_get_svg_icon( 'notify' ) . '</span>';
	}
}

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo apply_filters(
	'qode_wishlist_for_woocommerce_filter_wishlist_table_item_stock_status',
	sprintf(
		'<span class="qwfw-e-item-stock %s" title="%s">%s</span>',
		$stock_status_class,
		$stock_status_title,
		$stock_status_label
	),
	$product,
	$item_id
);
