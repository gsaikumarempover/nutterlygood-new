<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! empty( $items ) ) {
	// Include section title template.
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo apply_filters(
		'qode_wishlist_for_woocommerce_filter_wishlist_table_section_title',
		qode_wishlist_for_woocommerce_get_template_part( 'wishlist/shortcodes/wishlist-table', 'templates/parts/section-title', '', $params ),
		$params
	);

	// Set additional hook for 3rd party elements.
	do_action( 'qode_wishlist_for_woocommerce_action_before_wishlist_table', $params );

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo apply_filters(
		'qode_wishlist_for_woocommerce_filter_wishlist_table_element',
		sprintf(
			'<table class="qwfw-m-items shop_table">%s</table>',
			apply_filters(
				'qode_wishlist_for_woocommerce_filter_wishlist_table_items',
				qode_wishlist_for_woocommerce_get_template_part( 'wishlist/shortcodes/wishlist-table', 'templates/items', '', $params ),
				$params
			)
		),
		$params
	);

	/**
	 * Hook: qode_wishlist_for_woocommerce_action_after_wishlist_table.
	 *
	 * @hooked Multi Actions module - add_button - 10
	 * @hooked qode_wishlist_for_woocommerce_premium_extend_wishlist_table_with_total_amount - 20
	 * @hooked qode_wishlist_for_woocommerce_premium_extend_wishlist_table_shortcode_with_pagination - 30
	 * @hooked Ask For Estimate module - add_button - 60
	 * @hooked qode_wishlist_for_woocommerce_premium_extend_wishlist_table_shortcode_with_related_products - 100
	 * @hooked qode_frequently_bought_together_for_woocommerce_extend_wishlist_table_shortcode_with_frequently_bought_together_products - 130
	 */
	do_action( 'qode_wishlist_for_woocommerce_action_after_wishlist_table', $params );
} else {
	qode_wishlist_for_woocommerce_template_part( 'wishlist/shortcodes/wishlist-table', 'templates/not-found', '', $params );
}
