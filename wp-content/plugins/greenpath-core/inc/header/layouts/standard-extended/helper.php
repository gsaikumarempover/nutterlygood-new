<?php

if ( ! function_exists( 'greenpath_core_add_standard_extended_header_global_option' ) ) {
	/**
	 * This function set header type value for global header option map
	 */
	function greenpath_core_add_standard_extended_header_global_option( $header_layout_options ) {
		$header_layout_options['standard-extended'] = array(
			'image' => GREENPATH_CORE_HEADER_LAYOUTS_URL_PATH . '/standard-extended/assets/img/standard-extended-header.png',
			'label' => esc_html__( 'Standard Extended', 'greenpath-core' ),
		);

		return $header_layout_options;
	}

	add_filter( 'greenpath_core_filter_header_layout_option', 'greenpath_core_add_standard_extended_header_global_option' );
}

if ( ! function_exists( 'greenpath_core_register_standard_extended_header_layout' ) ) {
	/**
	 * Function which add header layout into global list
	 *
	 * @param array $header_layouts
	 *
	 * @return array
	 */
	function greenpath_core_register_standard_extended_header_layout( $header_layouts ) {
		$header_layouts['standard-extended'] = 'GreenPathCore_Standard_Extended_Header';

		return $header_layouts;
	}

	add_filter( 'greenpath_core_filter_register_header_layouts', 'greenpath_core_register_standard_extended_header_layout' );
}

if ( ! function_exists( 'greenpath_core_register_extended_dropdown_menu' ) ) {
	/**
	 * Function which add additional main menu navigation into global list
	 *
	 * @param array $menus
	 *
	 * @return array
	 */
	function greenpath_core_register_extended_dropdown_menu( $menus ) {
		$menus['extended-dropdown-menu'] = esc_html__( 'Extended Dropdown', 'greenpath-core' );

		return $menus;
	}

	add_filter( 'greenpath_filter_register_navigation_menus', 'greenpath_core_register_extended_dropdown_menu' );
}

if ( ! function_exists( 'greenpath_core_get_extended_dropdown_menu' ) ) {
	/**
	 * This function is used to wait header-function.php file to init header object and then to init hook registration function above
	 */
	function greenpath_core_get_extended_dropdown_menu() {
		$page_id                = qode_framework_get_page_id();
		$show_extended_dropdown = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_show_extended_dropdown', $page_id );

		if ( $show_extended_dropdown == 'yes' ) {
			$params       = array();
			$opener_title = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_extended_dropdown_opener_label', $page_id );

			$params['opener_title'] = ! empty( $opener_title ) ? $opener_title : esc_html__( 'Shop by Categories', 'greenpath-core' );

			greenpath_core_template_part( 'header/layouts/standard-extended', 'templates/extended-dropdown', '', $params );
		}
	}
}

if ( ! function_exists( 'greenpath_core_set_additional_standard_extended_header_classes' ) ) {
	/**
	 * This function add additional standard extended header area inner classes
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function greenpath_core_set_additional_standard_extended_header_classes( $classes ) {
		$dropdown_opened = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_dropdown_always_opened' );

		if ( ! empty( $dropdown_opened ) && 'yes' === $dropdown_opened ) {
			$classes[] = 'qodef-dropdown-always-opened';
		}

		return $classes;
	}

	add_filter( 'greenpath_core_filter_standard_extended_header_class', 'greenpath_core_set_additional_standard_extended_header_classes' );
}

if ( ! function_exists( 'greenpath_core_set_standard_extended_header_inner_classes' ) ) {
	/**
	 * This function add additional standard extended header area grid classes
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function greenpath_core_set_standard_extended_header_inner_classes( $classes ) {
		$header_grid = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_header_in_grid' );

		if( ! empty( $header_grid ) && 'yes' === $header_grid ) {
			$classes[] = 'qodef-content-grid';
		}

		return $classes;
	}

	add_filter( 'greenpath_core_filter_standard_extended_header_top_inner_class', 'greenpath_core_set_standard_extended_header_inner_classes' );
	add_filter( 'greenpath_core_filter_standard_extended_header_bottom_inner_class', 'greenpath_core_set_standard_extended_header_inner_classes' );
}

if ( ! function_exists( 'greenpath_core_set_standard_extended_header_bottom_classes' ) ) {
	/**
	 * This function sets extended header bottom skin class
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function greenpath_core_set_standard_extended_header_bottom_classes( $classes ) {
		$skin = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_bottom_skin', get_the_ID() );

		if( ! empty( $skin ) ) {
			$classes[] = 'qodef-skin--' . $skin;
		}

		$hide_label = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_hide_label' );

		if ( ! empty( $hide_label ) && 'yes' === $hide_label ) {
			$classes[] = 'qodef-dropdown--hide-label';
		}

		return $classes;
	}

	add_filter( 'greenpath_core_filter_standard_extended_header_bottom_class', 'greenpath_core_set_standard_extended_header_bottom_classes' );
}
