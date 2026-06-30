<?php

if ( ! function_exists( 'greenpath_core_include_yith_wishlist_plugin_is_installed' ) ) {
	/**
	 * Function that set case is installed element for framework functionality
	 *
	 * @param bool $installed
	 * @param string $plugin - plugin name
	 *
	 * @return bool
	 */
	function greenpath_core_include_yith_wishlist_plugin_is_installed( $installed, $plugin ) {
		if ( 'yith-wishlist' === $plugin ) {
			return defined( 'YITH_WCWL' );
		}

		return $installed;
	}

	add_filter( 'qode_framework_filter_is_plugin_installed', 'greenpath_core_include_yith_wishlist_plugin_is_installed', 10, 2 );
}

if ( ! function_exists( 'greenpath_core_get_yith_wishlist_shortcode' ) ) {
	/**
	 * Function that print module shortcode
	 *
	 * @return string that contains html content
	 */
	function greenpath_core_get_yith_wishlist_shortcode() {
		if ( qode_framework_is_installed( 'yith-wishlist' ) ) {
			echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
		}
	}
}

if ( ! function_exists( 'greenpath_core_woo_custom_wishlist_remove_link' ) ) {
	/**
	 * Function that overrides the Wishlist remove link title
	 */
	function greenpath_core_woo_custom_wishlist_remove_link() {

		return esc_html__( 'Remove', 'greenpath-core' );
	}

	add_filter( 'yith_wcwl_remove_product_wishlist_message_title', 'greenpath_core_woo_custom_wishlist_remove_link' );
}
