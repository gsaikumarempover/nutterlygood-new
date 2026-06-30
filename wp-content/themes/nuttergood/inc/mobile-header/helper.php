<?php

if ( ! function_exists( 'greenpath_load_page_mobile_header' ) ) {
	/**
	 * Function which loads page template module
	 */
	function greenpath_load_page_mobile_header() {
		// Include mobile header template
		echo apply_filters( 'greenpath_filter_mobile_header_template', greenpath_get_template_part( 'mobile-header', 'templates/mobile-header' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	add_action( 'greenpath_action_page_header_template', 'greenpath_load_page_mobile_header' );
}

if ( ! function_exists( 'greenpath_register_mobile_navigation_menus' ) ) {
	/**
	 * Function which registers navigation menus
	 */
	function greenpath_register_mobile_navigation_menus() {
		$navigation_menus = apply_filters( 'greenpath_filter_register_mobile_navigation_menus', array( 'mobile-navigation' => esc_html__( 'Mobile Navigation', 'nuttergood' ) ) );

		if ( ! empty( $navigation_menus ) ) {
			register_nav_menus( $navigation_menus );
		}
	}

	add_action( 'greenpath_action_after_include_modules', 'greenpath_register_mobile_navigation_menus' );
}
