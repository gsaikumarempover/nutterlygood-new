<?php

if ( ! function_exists( 'greenpath_core_is_page_footer_enabled' ) ) {
	/**
	 * Function that check is module enabled
	 *
	 * @param bool $is_enabled
	 *
	 * @return bool
	 */
	function greenpath_core_is_page_footer_enabled( $is_enabled ) {
		$option = 'no' !== greenpath_core_get_post_value_through_levels( 'qodef_enable_page_footer' );

		if ( ! $option ) {
			$is_enabled = false;
		}

		return $is_enabled;
	}

	add_filter( 'greenpath_filter_enable_page_footer', 'greenpath_core_is_page_footer_enabled' );
}

if ( ! function_exists( 'greenpath_core_set_footer_holder_classes' ) ) {
	/**
	 * Function that return classes for page footer area
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function greenpath_core_set_footer_holder_classes( $classes ) {

		if ( 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_enable_uncovering_footer' ) ) {
			$classes[] = 'qodef--uncover';
		}

		$skin = greenpath_core_get_post_value_through_levels( 'qodef_footer_skin' );

		if ( ! empty( $skin ) && 'none' !== $skin ) {
			$classes[] = 'qodef-skin--' . $skin;
		}

		return $classes;
	}

	add_filter( 'greenpath_filter_footer_holder_classes', 'greenpath_core_set_footer_holder_classes' );
}

if ( ! function_exists( 'greenpath_core_is_footer_top_area_enabled' ) ) {
	/**
	 * Function that check if page footer top area widgets are empty
	 *
	 * @param bool $is_enabled
	 *
	 * @return bool
	 */
	function greenpath_core_is_footer_top_area_enabled( $is_enabled ) {
		$option = 'no' !== greenpath_core_get_post_value_through_levels( 'qodef_enable_top_footer_area' );

		if ( ! $option ) {
			$is_enabled = false;
		}

		return $is_enabled;
	}

	add_filter( 'greenpath_filter_enable_footer_top_area', 'greenpath_core_is_footer_top_area_enabled' );
}

if ( ! function_exists( 'greenpath_core_is_footer_bottom_area_enabled' ) ) {
	/**
	 * Function that check if page footer bottom area widgets are empty
	 *
	 * @param bool $is_enabled
	 *
	 * @return bool
	 */
	function greenpath_core_is_footer_bottom_area_enabled( $is_enabled ) {
		$option = 'no' !== greenpath_core_get_post_value_through_levels( 'qodef_enable_bottom_footer_area' );

		if ( ! $option ) {
			$is_enabled = false;
		}

		return $is_enabled;
	}

	add_filter( 'greenpath_filter_enable_footer_bottom_area', 'greenpath_core_is_footer_bottom_area_enabled' );
}

if ( ! function_exists( 'greenpath_core_set_footer_top_area_classes' ) ) {
	/**
	 * Function that return classes for page footer top area
	 *
	 * @param string $classes
	 *
	 * @return string
	 */
	function greenpath_core_set_footer_top_area_classes( $classes ) {
		$is_grid_enabled = 'no' !== greenpath_core_get_post_value_through_levels( 'qodef_set_footer_top_area_in_grid' );

		if ( ! $is_grid_enabled ) {
			$classes = 'qodef-content-full-width';
		}

		$top_area_columns = greenpath_core_get_post_value_through_levels( 'qodef_set_footer_top_area_columns' );

		if ( ! empty( $top_area_columns ) ) {
			$classes .= ' qodef-layout-columns--' . esc_attr( $top_area_columns );
		}

		return $classes;
	}

	add_filter( 'greenpath_filter_footer_top_area_classes', 'greenpath_core_set_footer_top_area_classes' );
}

if ( ! function_exists( 'greenpath_core_set_footer_bottom_area_classes' ) ) {
	/**
	 * Function that return classes for page footer bottom area
	 *
	 * @param string $classes
	 *
	 * @return string
	 */
	function greenpath_core_set_footer_bottom_area_classes( $classes ) {
		$is_grid_enabled = 'no' !== greenpath_core_get_post_value_through_levels( 'qodef_set_footer_bottom_area_in_grid' );

		if ( ! $is_grid_enabled ) {
			$classes = 'qodef-content-full-width';
		}

		return $classes;
	}

	add_filter( 'greenpath_filter_footer_bottom_area_classes', 'greenpath_core_set_footer_bottom_area_classes' );
}

if ( ! function_exists( 'greenpath_core_set_footer_sidebars_config' ) ) {
	/**
	 * Function that override default page footer sidebars config
	 *
	 * @param array $config
	 *
	 * @return array
	 */
	function greenpath_core_set_footer_sidebars_config( $config ) {
		$top_area_columns    = greenpath_core_get_post_value_through_levels( 'qodef_set_footer_top_area_columns' );
		$bottom_area_columns = greenpath_core_get_post_value_through_levels( 'qodef_set_footer_bottom_area_columns' );

		if ( ! empty( $top_area_columns ) ) {
			$config['footer_top_sidebars_number'] = $top_area_columns;
		}

		if ( ! empty( $bottom_area_columns ) ) {
			$config['footer_bottom_sidebars_number'] = $bottom_area_columns;
		}

		return $config;
	}

	add_filter( 'greenpath_filter_page_footer_sidebars_config', 'greenpath_core_set_footer_sidebars_config' );
	add_filter( 'greenpath_core_filter_footer_areas_columns_size', 'greenpath_core_set_footer_sidebars_config' );
}

if ( ! function_exists( 'greenpath_core_set_footer_top_area_columns_classes' ) ) {
	/**
	 * Function that set classes for page footer top area columns
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function greenpath_core_set_footer_top_area_columns_classes( $classes ) {
		$gutter_size = greenpath_core_get_post_value_through_levels( 'qodef_set_footer_top_area_grid_gutter' );
		$alignment   = greenpath_core_get_post_value_through_levels( 'qodef_set_footer_top_area_content_alignment' );

		if ( ! empty( $gutter_size ) ) {
			$classes[] = 'qodef-gutter--' . esc_attr( $gutter_size );
		}

		if ( ! empty( $alignment ) ) {
			$classes[] = 'qodef-alignment--' . esc_attr( $alignment );
		}

		return $classes;
	}

	add_filter( 'greenpath_filter_footer_top_area_columns_classes', 'greenpath_core_set_footer_top_area_columns_classes' );
}

if ( ! function_exists( 'greenpath_core_set_footer_top_area_grid_gutter_styles' ) ) {
	/**
	 * Function that returns grid gutter styles
	 *
	 * @return array
	 */
	function greenpath_core_set_footer_top_area_grid_gutter_styles() {
		$styles = greenpath_core_get_gutter_custom_styles( 'qodef_set_footer_top_area_grid_gutter_' );

		return $styles;
	}

	add_filter( 'greenpath_filter_footer_top_area_grid_gutter_styles', 'greenpath_core_set_footer_top_area_grid_gutter_styles' );
}

if ( ! function_exists( 'greenpath_core_set_footer_bottom_area_columns_classes' ) ) {
	/**
	 * Function that set classes for page footer bottom area columns
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function greenpath_core_set_footer_bottom_area_columns_classes( $classes ) {
		$gutter_size = greenpath_core_get_post_value_through_levels( 'qodef_set_footer_bottom_area_grid_gutter' );
		$alignment   = greenpath_core_get_post_value_through_levels( 'qodef_set_footer_bottom_area_content_alignment' );

		if ( ! empty( $gutter_size ) ) {
			$classes[] = 'qodef-gutter--' . esc_attr( $gutter_size );
		}

		if ( ! empty( $alignment ) ) {
			$classes[] = 'qodef-alignment--' . esc_attr( $alignment );
		}

		return $classes;
	}

	add_filter( 'greenpath_filter_footer_bottom_area_columns_classes', 'greenpath_core_set_footer_bottom_area_columns_classes' );
}

if ( ! function_exists( 'greenpath_core_set_footer_bottom_area_grid_gutter_styles' ) ) {
	/**
	 * Function that returns grid gutter styles
	 *
	 * @return array
	 */
	function greenpath_core_set_footer_bottom_area_grid_gutter_styles() {
		$styles = greenpath_core_get_gutter_custom_styles( 'qodef_set_footer_bottom_area_grid_gutter_' );

		return $styles;
	}

	add_filter( 'greenpath_filter_footer_bottom_area_grid_gutter_styles', 'greenpath_core_set_footer_bottom_area_grid_gutter_styles' );
}

if ( ! function_exists( 'greenpath_core_set_custom_footer_widget_area' ) ) {
	/**
	 * This function set custom footer widgets area
	 *
	 * @param string $widget_id
	 * @param string $widget_area
	 * @param string $column
	 *
	 * @return string
	 */
	function greenpath_core_set_custom_footer_widget_area( $widget_id, $widget_area, $column ) {
		$page_id            = qode_framework_get_page_id();
		$custom_widget_id   = 'qodef_footer_' . esc_attr( $widget_area ) . '_area_custom_widget_' . esc_attr( $column );
		$custom_widget_area = get_post_meta( $page_id, $custom_widget_id, true );

		if ( ! empty( $custom_widget_area ) ) {
			return $custom_widget_area;
		}

		return $widget_id;
	}

	add_filter( 'greenpath_filter_footer_widget_area', 'greenpath_core_set_custom_footer_widget_area', 10, 3 );
}

if ( ! function_exists( 'greenpath_core_set_page_footer_area_styles' ) ) {
	/**
	 * Function that generates module inline styles
	 *
	 * @param string $style
	 *
	 * @return string
	 */
	function greenpath_core_set_page_footer_area_styles( $style ) {
		$footer_area = array( 'top', 'bottom' );

		foreach ( $footer_area as $area ) {
			$styles           = array();
			$background_color = greenpath_core_get_post_value_through_levels( 'qodef_' . $area . '_footer_area_background_color' );
			$background_image = greenpath_core_get_post_value_through_levels( 'qodef_' . $area . '_footer_area_background_image' );
			$top_border_color = greenpath_core_get_post_value_through_levels( 'qodef_' . $area . '_footer_area_top_border_color' );
			$top_border_width = greenpath_core_get_post_value_through_levels( 'qodef_' . $area . '_footer_area_top_border_width' );
			$top_border_style = greenpath_core_get_post_value_through_levels( 'qodef_' . $area . '_footer_area_top_border_style' );

			if ( ! empty( $background_color ) ) {
				$styles['background-color'] = $background_color;
			}

			if ( ! empty( $background_image ) ) {
				$styles['background-image'] = 'url(' . esc_url( wp_get_attachment_image_url( $background_image, 'full' ) ) . ')';
			}

			if ( ! empty( $top_border_color ) ) {
				$styles['border-top-color'] = $top_border_color;

				if ( '' === $top_border_width ) {
					$styles['border-top-width'] = '1px';
				}
			}

			if ( '' !== $top_border_width ) {
				$styles['border-top-width'] = intval( $top_border_width ) . 'px';
			}

			if ( ! empty( $top_border_style ) ) {
				$styles['border-top-style'] = $top_border_style;
			}

			if ( ! empty( $styles ) ) {
				$style .= qode_framework_dynamic_style( '#qodef-page-footer-' . $area . '-area', $styles );
			}

			$inner_styles = array();

			$columns_size     = greenpath_core_get_post_value_through_levels( 'qodef_' . $area . '_footer_area_columns_size' );
			$padding_top      = greenpath_core_get_post_value_through_levels( 'qodef_' . $area . '_footer_area_padding_top' );
			$padding_bottom   = greenpath_core_get_post_value_through_levels( 'qodef_' . $area . '_footer_area_padding_bottom' );
			$side_padding     = greenpath_core_get_post_value_through_levels( 'qodef_' . $area . '_footer_area_side_padding' );

			if ( ! empty( $columns_size ) ) {
				if ( qode_framework_string_ends_with_space_units( $columns_size ) ) {
					$inner_styles['max-width'] = $columns_size;
				} else {
					$inner_styles['max-width'] = intval( $columns_size ) . 'px';
				}
			}

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

			if ( ! empty( $inner_styles ) ) {
				$style .= qode_framework_dynamic_style( '#qodef-page-footer-' . $area . '-area-inner', $inner_styles );
			}

			$widgets_styles = array();
			$margin_bottom  = greenpath_core_get_post_value_through_levels( 'qodef_' . $area . '_footer_area_widgets_margin_bottom' );

			if ( ! empty( $margin_bottom ) ) {
				if ( qode_framework_string_ends_with_space_units( $margin_bottom, true ) ) {
					$widgets_styles['margin-bottom'] = $margin_bottom;
				} else {
					$widgets_styles['margin-bottom'] = intval( $margin_bottom ) . 'px';
				}
			}

			if ( ! empty( $widgets_styles ) ) {
				$style .= qode_framework_dynamic_style( '#qodef-page-footer-' . $area . '-area .widget', $widgets_styles );
			}

			$widgets_title_styles = array();
			$title_margin_bottom  = greenpath_core_get_post_value_through_levels( 'qodef_' . $area . '_footer_area_widgets_title_margin_bottom' );

			if ( ! empty( $title_margin_bottom ) ) {
				if ( qode_framework_string_ends_with_space_units( $title_margin_bottom, true ) ) {
					$widgets_title_styles['margin-bottom'] = $title_margin_bottom;
				} else {
					$widgets_title_styles['margin-bottom'] = intval( $title_margin_bottom ) . 'px';
				}
			}

			if ( ! empty( $widgets_title_styles ) ) {
				$style .= qode_framework_dynamic_style( '#qodef-page-footer-' . $area . '-area .widget .qodef-widget-title', $widgets_title_styles );
			}
		}

		return $style;
	}

	add_filter( 'greenpath_filter_add_inline_style', 'greenpath_core_set_page_footer_area_styles' );
}
