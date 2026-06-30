<?php

if ( ! function_exists( 'greenpath_core_register_fullscreen_menu' ) ) {
	/**
	 * Function which add additional main menu navigation into global list
	 *
	 * @param array $menus
	 *
	 * @return array
	 */
	function greenpath_core_register_fullscreen_menu( $menus ) {
		$menus['fullscreen-menu-navigation'] = esc_html__( 'Fullscreen Navigation', 'greenpath-core' );

		return $menus;
	}

	add_filter( 'greenpath_filter_register_navigation_menus', 'greenpath_core_register_fullscreen_menu' );
}

if ( ! function_exists( 'greenpath_core_add_fullscreen_menu_body_classes' ) ) {
	/**
	 * Function that add additional class name into global class list for body tag
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function greenpath_core_add_fullscreen_menu_body_classes( $classes ) {
		$hide_logo = greenpath_core_get_post_value_through_levels( 'qodef_fullscreen_menu_hide_logo' );

		if ( 'yes' === $hide_logo ) {
			$classes[] = 'qodef-fullscreen-menu--hide-logo';
		}

		return $classes;
	}

	add_filter( 'body_class', 'greenpath_core_add_fullscreen_menu_body_classes' );
}

if ( ! function_exists( 'greenpath_core_set_fullscreen_menu_typography_styles' ) ) {
	/**
	 * Function that generates module inline styles
	 *
	 * @param string $style
	 *
	 * @return string
	 */
	function greenpath_core_set_fullscreen_menu_typography_styles( $style ) {
		$area_styles      = array();
		$background_color = greenpath_core_get_post_value_through_levels( 'qodef_fullscreen_menu_background_color' );
		$background_image = greenpath_core_get_post_value_through_levels( 'qodef_fullscreen_menu_background_image' );

		if ( ! empty( $background_color ) ) {
			$area_styles['background-color'] = $background_color;
		}

		if ( ! empty( $background_image ) ) {
			$area_styles['background-image'] = 'url(' . esc_url( wp_get_attachment_image_url( $background_image, 'full' ) ) . ')';
		}

		if ( ! empty( $area_styles ) ) {
			$style .= qode_framework_dynamic_style( '#qodef-fullscreen-area', $area_styles );
		}

		$first_lvl_styles        = greenpath_core_get_typography_styles( 'qodef_fullscreen_1st_lvl' );
		$first_lvl_hover_styles  = greenpath_core_get_typography_hover_styles( 'qodef_fullscreen_1st_lvl' );
		$second_lvl_styles       = greenpath_core_get_typography_styles( 'qodef_fullscreen_2nd_lvl' );
		$second_lvl_hover_styles = greenpath_core_get_typography_hover_styles( 'qodef_fullscreen_2nd_lvl' );

		if ( ! empty( $first_lvl_styles ) ) {
			$style .= qode_framework_dynamic_style( '.qodef-fullscreen-menu > ul > li > a', $first_lvl_styles );
		}

		if ( ! empty( $first_lvl_hover_styles ) ) {
			$style .= qode_framework_dynamic_style( '.qodef-fullscreen-menu > ul > li > a:hover', $first_lvl_hover_styles );
		}

		$first_lvl_active_color = greenpath_core_get_option_value( 'admin', 'qodef_fullscreen_1st_lvl_active_color' );

		if ( ! empty( $first_lvl_active_color ) ) {
			$first_lvl_active_styles = array(
				'color' => $first_lvl_active_color,
			);

			$style .= qode_framework_dynamic_style(
				array(
					'.qodef-fullscreen-menu > ul >li.current-menu-ancestor > a',
					'.qodef-fullscreen-menu > ul >li.current-menu-item > a',
				),
				$first_lvl_active_styles
			);
		}

		if ( ! empty( $second_lvl_styles ) ) {
			$style .= qode_framework_dynamic_style( '.qodef-fullscreen-menu .qodef-drop-down-second-inner ul li > a', $second_lvl_styles );
		}

		if ( ! empty( $second_lvl_hover_styles ) ) {
			$style .= qode_framework_dynamic_style( '.qodef-fullscreen-menu .qodef-drop-down-second-inner ul li > a:hover', $second_lvl_hover_styles );
		}

		$second_lvl_active_color = greenpath_core_get_option_value( 'admin', 'qodef_fullscreen_2nd_lvl_active_color' );

		if ( ! empty( $second_lvl_active_color ) ) {
			$second_lvl_active_styles = array(
				'color' => $second_lvl_active_color,
			);

			$style .= qode_framework_dynamic_style(
				array(
					'.qodef-fullscreen-menu .qodef-drop-down-second ul li.current-menu-ancestor > a',
					'.qodef-fullscreen-menu .qodef-drop-down-second ul li.current-menu-item > a',
				),
				$second_lvl_active_styles
			);
		}

		return $style;
	}

	add_filter( 'greenpath_filter_add_inline_style', 'greenpath_core_set_fullscreen_menu_typography_styles' );
}
