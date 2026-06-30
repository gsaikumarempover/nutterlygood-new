<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_add_icon_with_text_type_global_option' ) ) {
	/**
	 * This function set button type value for global button type option map
	 */
	function qode_quick_view_for_woocommerce_add_icon_with_text_type_global_option( $button_type_options ) {
		$button_type_options['icon-with-text'] = esc_html__( 'Icon With Text', 'qode-quick-view-for-woocommerce' );

		return $button_type_options;
	}

	add_filter( 'qode_quick_view_for_woocommerce_filter_button_type_option', 'qode_quick_view_for_woocommerce_add_icon_with_text_type_global_option' );
}
