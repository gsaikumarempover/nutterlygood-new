<?php

if ( ! function_exists( 'greenpath_core_is_page_content_bottom_enabled' ) ) {
	/**
	 * Function that check is module enabled
	 *
	 * @return bool
	 */
	function greenpath_core_is_page_content_bottom_enabled() {
		return 'no' !== greenpath_core_get_post_value_through_levels( 'qodef_enable_page_content_bottom' ) && is_active_sidebar( 'qodef-content-bottom' );
	}
}

if ( ! function_exists( 'greenpath_core_load_page_content_bottom' ) ) {
	/**
	 * Function which loads page template module
	 */
	function greenpath_core_load_page_content_bottom() {

		if ( greenpath_core_is_page_content_bottom_enabled() ) {
			// Include content bottom template
			echo apply_filters( 'greenpath_core_filter_content_bottom_template', greenpath_core_get_template_part( 'content-bottom', 'templates/content-bottom' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	add_action( 'greenpath_action_page_footer_template', 'greenpath_core_load_page_content_bottom', 5 ); // Permission 5 is set in order to be before footer area
}

if ( ! function_exists( 'greenpath_core_register_content_bottom_sidebar' ) ) {
	/**
	 * Register content bottom sidebar
	 */
	function greenpath_core_register_content_bottom_sidebar() {

		// Sidebar config variables
		$config = apply_filters(
			'greenpath_core_filter_content_bottom_sidebar_config',
			array(
				'title_tag'   => 'h6',
				'title_class' => 'qodef-widget-title',
			)
		);

		register_sidebar(
			array(
				'id'            => 'qodef-content-bottom',
				'name'          => esc_html__( 'Content Bottom Area', 'greenpath-core' ),
				'description'   => esc_html__( 'Widgets added here will appear in content bottom', 'greenpath-core' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s" data-area="content-bottom">',
				'after_widget'  => '</div>',
				'before_title'  => '<' . esc_attr( $config['title_tag'] ) . ' class="' . esc_attr( $config['title_class'] ) . '">',
				'after_title'   => '</' . esc_attr( $config['title_tag'] ) . '>',
			)
		);
	}

	add_action( 'widgets_init', 'greenpath_core_register_content_bottom_sidebar' );
}

if ( ! function_exists( 'greenpath_core_content_bottom_set_admin_options_map_position' ) ) {
	/**
	 * Function that set dashboard admin options map position for this module
	 *
	 * @param int $position
	 * @param string $map
	 *
	 * @return int
	 */
	function greenpath_core_content_bottom_set_admin_options_map_position( $position, $map ) {

		if ( 'content-bottom' === $map ) {
			$position = 19;
		}

		return $position;
	}

	add_filter( 'greenpath_core_filter_admin_options_map_position', 'greenpath_core_content_bottom_set_admin_options_map_position', 10, 2 );
}

if ( ! function_exists( 'greenpath_core_set_content_bottom_area_inner_classes' ) ) {
	/**
	 * Function that return classes for page content bottom area
	 *
	 * @param string $classes
	 *
	 * @return string
	 */
	function greenpath_core_set_content_bottom_area_inner_classes( $classes ) {
		$is_grid_enabled = 'no' !== greenpath_core_get_post_value_through_levels( 'qodef_set_content_bottom_area_in_grid' );

		if ( ! $is_grid_enabled ) {
			$classes = 'qodef-content-full-width';
		}

		return $classes;
	}

	add_filter( 'greenpath_core_filter_content_bottom_inner_classes', 'greenpath_core_set_content_bottom_area_inner_classes' );
}

if ( ! function_exists( 'greenpath_core_set_page_content_bottom_area_styles' ) ) {
	/**
	 * Function that generates module inline styles
	 *
	 * @param string $style
	 *
	 * @return string
	 */
	function greenpath_core_set_page_content_bottom_area_styles( $style ) {
		$styles           = array();
		$background_color = greenpath_core_get_post_value_through_levels( 'qodef_content_bottom_area_background_color' );
		$background_image = greenpath_core_get_post_value_through_levels( 'qodef_content_bottom_area_background_image' );

		if ( ! empty( $background_color ) ) {
			$styles['background-color'] = $background_color;
		}

		if ( ! empty( $background_image ) ) {
			$styles['background-image'] = 'url(' . esc_url( wp_get_attachment_image_url( $background_image, 'full' ) ) . ')';
		}

		if ( ! empty( $styles ) ) {
			$style .= qode_framework_dynamic_style( '#qodef-page-content-bottom', $styles );
		}

		$inner_styles = array();

		$padding_top      = greenpath_core_get_post_value_through_levels( 'qodef_content_bottom_area_padding_top' );
		$padding_bottom   = greenpath_core_get_post_value_through_levels( 'qodef_content_bottom_area_padding_bottom' );
		$side_padding     = greenpath_core_get_post_value_through_levels( 'qodef_content_bottom_area_side_padding' );
		$top_border_color = greenpath_core_get_post_value_through_levels( 'qodef_content_bottom_area_top_border_color' );
		$top_border_width = greenpath_core_get_post_value_through_levels( 'qodef_content_bottom_area_top_border_width' );
		$top_border_style = greenpath_core_get_post_value_through_levels( 'qodef_content_bottom_area_top_border_style' );

		if ( '' !== $padding_top ) {
			if ( qode_framework_string_ends_with_space_units( $padding_top, true ) ) {
				$inner_styles['padding-top'] = $padding_top;
			} else {
				$inner_styles['padding-top'] = intval( $padding_top ) . 'px';
			}
		}

		if ( '' !== $padding_bottom ) {
			if ( qode_framework_string_ends_with_space_units( $padding_bottom, true ) ) {
				$inner_styles['padding-bottom'] = $padding_bottom;
			} else {
				$inner_styles['padding-bottom'] = intval( $padding_bottom ) . 'px';
			}
		}

		if ( '' !== $side_padding ) {
			if ( qode_framework_string_ends_with_space_units( $side_padding, true ) ) {
				$inner_styles['padding-left']  = $side_padding . '!important';
				$inner_styles['padding-right'] = $side_padding . '!important';
			} else {
				$inner_styles['padding-left']  = intval( $side_padding ) . 'px !important';
				$inner_styles['padding-right'] = intval( $side_padding ) . 'px !important';
			}
		}

		if ( ! empty( $top_border_color ) ) {
			$inner_styles['border-top-color'] = $top_border_color;

			if ( '' === $top_border_width ) {
				$inner_styles['border-top-width'] = '1px';
			}
		}

		if ( '' !== $top_border_width ) {
			$inner_styles['border-top-width'] = intval( $top_border_width ) . 'px';
		}

		if ( ! empty( $top_border_style ) ) {
			$inner_styles['border-top-style'] = $top_border_style;
		}

		if ( ! empty( $inner_styles ) ) {
			$style .= qode_framework_dynamic_style( '#qodef-page-content-bottom > .qodef-m-inner', $inner_styles );
		}

		return $style;
	}

	add_filter( 'greenpath_filter_add_inline_style', 'greenpath_core_set_page_content_bottom_area_styles' );
}
