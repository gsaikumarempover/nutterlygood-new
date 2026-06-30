<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( is_array( $item ) && isset( $item['date_added'] ) ) {
	echo wp_kses_post(
		apply_filters(
			'qode_wishlist_for_woocommerce_filter_wishlist_table_item_date_added',
			'<span class="qwfw-e-item-added-date">' . sprintf(
				'%s%s',
				'<span class="qwfw-e-item-added-date-label">' . esc_html__( 'Added to the list: ', 'qode-wishlist-for-woocommerce' ) . '</span>',
				'<span class="qwfw-e-item-added-date-value">' . esc_html( date_i18n( wc_date_format(), $item['date_added'] ) ) . '</span>'
			) . '</span>',
			$product,
			$item,
			$item_id
		)
	);
}
