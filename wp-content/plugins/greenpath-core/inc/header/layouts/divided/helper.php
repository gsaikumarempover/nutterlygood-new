<?php
if ( ! function_exists( 'greenpath_core_add_divided_header_global_option' ) ) {
	/**
	 * This function set header type value for global header option map
	 */

	function greenpath_core_add_divided_header_global_option( $header_layout_options ) {
		$header_layout_options['divided'] = array(
			'image' => GREENPATH_CORE_HEADER_LAYOUTS_URL_PATH . '/divided/assets/img/divided-header.png',
			'label' => esc_html__( 'Divided', 'greenpath-core' ),
		);

		return $header_layout_options;
	}

	add_filter( 'greenpath_core_filter_header_layout_option', 'greenpath_core_add_divided_header_global_option' );
}


if ( ! function_exists( 'greenpath_core_register_divided_header_layout' ) ) {
	/**
	 * Function which add header layout into global list
	 *
	 * @param array $header_layouts
	 *
	 * @return array
	 */
	function greenpath_core_register_divided_header_layout( $header_layouts ) {
		$header_layout = array(
			'divided' => 'GreenPathCore_Divided_Header',
		);

		$header_layouts = array_merge( $header_layouts, $header_layout );

		return $header_layouts;
	}

	add_filter( 'greenpath_core_filter_register_header_layouts', 'greenpath_core_register_divided_header_layout' );
}

if ( ! function_exists( 'greenpath_core_register_divided_menu' ) ) {
	/**
	 * Function which add additional main menu navigation into global list
	 *
	 * @param array $menus
	 *
	 * @return array
	 */
	function greenpath_core_register_divided_menu( $menus ) {
		$menus['divided-menu-left-navigation']  = esc_html__( 'Divided Left Navigation', 'greenpath-core' );
		$menus['divided-menu-right-navigation'] = esc_html__( 'Divided Right Navigation', 'greenpath-core' );

		return $menus;
	}

	add_filter( 'greenpath_filter_register_navigation_menus', 'greenpath_core_register_divided_menu' );
}
