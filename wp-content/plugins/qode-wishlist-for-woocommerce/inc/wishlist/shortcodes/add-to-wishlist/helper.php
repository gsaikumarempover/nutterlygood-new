<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_add_to_wishlist_label' ) ) {
	/**
	 * Function that return wishlist item label
	 *
	 * @param array $atts - shortcode attributes
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_get_add_to_wishlist_label( $atts ) {
		$is_item_added = qode_wishlist_for_woocommerce_check_is_wishlist_item_added( $atts['item_id'] );
		$label         = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_label' );
		$added_label   = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_added_to_wishlist_label' );
		$remove_label  = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_remove_from_wishlist_label' );
		$browse_label  = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_browse_wishlist_label' );
		$behavior      = ! empty( $atts['button_behavior'] ) ? $atts['button_behavior'] : qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_behavior' );

		$add_label   = ! empty( $label ) ? $label : esc_html__( 'Add to wishlist', 'qode-wishlist-for-woocommerce' );
		$added_label = ! empty( $added_label ) ? $added_label : esc_html__( 'Add to wishlist', 'qode-wishlist-for-woocommerce' );

		$button_label = $add_label;

		if ( $is_item_added ) {
			$button_label = $added_label;

			if ( 'remove' === $behavior ) {
				$button_label = ! empty( $remove_label ) ? $remove_label : esc_html__( 'Remove from list', 'qode-wishlist-for-woocommerce' );
			} elseif ( 'view' === $behavior ) {
				$button_label = ! empty( $browse_label ) ? $browse_label : esc_html__( 'Browse wishlist', 'qode-wishlist-for-woocommerce' );
			}
		}

		return apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_button_label', $button_label, $atts['item_id'], $is_item_added );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_add_to_wishlist_styles' ) ) {
	/**
	 * Function that return module inline styles
	 *
	 * @param string $before_selector
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_get_add_to_wishlist_styles( $before_selector = '' ) {
		$styles = array();
		$style  = '';

		$wrapper_selector = apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_styles_wrapper_selector', '.qwfw-add-to-wishlist-wrapper' );
		$main_selector    = apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_styles_selector', array( '.qwfw-add-to-wishlist-wrapper .qwfw-shortcode' ) );

		if ( ! empty( trim( $before_selector ) ) ) {
			foreach ( $main_selector as $selector_key => $selector_value ) {
				$main_selector[ $selector_key ] = $before_selector . ' ' . $selector_value;
			}
		}

		$wrapper_loop_styles   = array();
		$wrapper_single_styles = array();

		$thumb_top_offset_loop  = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_loop_thumb_top_offset' );
		$thumb_side_offset_loop = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_loop_thumb_side_offset' );

		$thumb_top_offset_single    = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_single_thumb_top_offset' );
		$thumb_bottom_offset_single = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_single_thumb_bottom_offset' );
		$thumb_side_offset_single   = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_single_thumb_side_offset' );

		// Wrapper loop styles.
		if ( ! empty( $thumb_top_offset_loop ) ) {
			if ( qode_wishlist_for_woocommerce_string_ends_with_allowed_units( $thumb_top_offset_loop ) ) {
				$wrapper_loop_styles['--qwfw-atw-thumb-top-offset'] = $thumb_top_offset_loop;
			} else {
				$wrapper_loop_styles['--qwfw-atw-thumb-top-offset'] = (int) $thumb_top_offset_loop . 'px';
			}
		}

		if ( ! empty( $thumb_side_offset_loop ) ) {
			if ( qode_wishlist_for_woocommerce_string_ends_with_allowed_units( $thumb_side_offset_loop ) ) {
				$wrapper_loop_styles['--qwfw-atw-thumb-side-offset'] = $thumb_side_offset_loop;
			} else {
				$wrapper_loop_styles['--qwfw-atw-thumb-side-offset'] = (int) $thumb_side_offset_loop . 'px';
			}
		}

		if ( ! empty( $wrapper_loop_styles ) ) {
			$style .= qode_wishlist_for_woocommerce_dynamic_style(
				array(
					$wrapper_selector . '.qwfw--loop',
				),
				$wrapper_loop_styles
			);
		}

		// Wrapper single styles.
		if ( ! empty( $thumb_top_offset_single ) ) {
			if ( qode_wishlist_for_woocommerce_string_ends_with_allowed_units( $thumb_top_offset_single ) ) {
				$wrapper_single_styles['--qwfw-atw-thumb-top-offset'] = $thumb_top_offset_single;
			} else {
				$wrapper_single_styles['--qwfw-atw-thumb-top-offset'] = (int) $thumb_top_offset_single . 'px';
			}
		}

		if ( ! empty( $thumb_bottom_offset_single ) ) {
			if ( qode_wishlist_for_woocommerce_string_ends_with_allowed_units( $thumb_bottom_offset_single ) ) {
				$wrapper_single_styles['--qwfw-atw-thumb-bottom-offset'] = $thumb_bottom_offset_single;
			} else {
				$wrapper_single_styles['--qwfw-atw-thumb-bottom-offset'] = (int) $thumb_bottom_offset_single . 'px';
			}
		}

		if ( ! empty( $thumb_side_offset_single ) ) {
			if ( qode_wishlist_for_woocommerce_string_ends_with_allowed_units( $thumb_side_offset_single ) ) {
				$wrapper_single_styles['--qwfw-atw-thumb-side-offset'] = $thumb_side_offset_single;
			} else {
				$wrapper_single_styles['--qwfw-atw-thumb-side-offset'] = (int) $thumb_side_offset_single . 'px';
			}
		}

		if ( ! empty( $wrapper_single_styles ) ) {
			$style .= qode_wishlist_for_woocommerce_dynamic_style(
				array(
					$wrapper_selector . '.qwfw--single',
				),
				$wrapper_single_styles
			);
		}

		// Button styles.
		$icon_size    = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_size' );
		$color        = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_color' );
		$hover_color  = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_hover_color' );
		$active_color = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_active_color' );

		if ( ! empty( $icon_size ) ) {
			$styles['--qwfw-atw-icon-size'] = (int) $icon_size . 'px';
		}

		if ( ! empty( $color ) ) {
			$styles['--qwfw-atw-color'] = $color;
		}

		if ( ! empty( $hover_color ) ) {
			$styles['--qwfw-atw-hover-color'] = $hover_color;
		}

		if ( ! empty( $active_color ) ) {
			$styles['--qwfw-atw-active-color'] = $active_color;
		}

		if ( ! empty( $styles ) ) {
			foreach ( $main_selector as $selector ) {
				$style .= qode_wishlist_for_woocommerce_dynamic_style( $selector, $styles );
			}
		}

		// Button area styles.
		$area_styles        = array();
		$area_hover_styles  = array();
		$area_active_styles = array();

		$area_padding             = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_padding' );
		$area_bg_color            = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_background_color' );
		$area_hover_bg_color      = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_background_hover_color' );
		$area_active_bg_color     = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_background_active_color' );
		$area_border_width        = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_border_width' );
		$area_border_style        = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_border_style' );
		$area_border_radius       = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_border_radius' );
		$area_border_color        = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_border_color' );
		$area_hover_border_color  = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_border_hover_color' );
		$area_active_border_color = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_border_active_color' );

		if ( ! empty( $area_padding ) ) {
			$area_styles['--qwfw-atw-padding'] = $area_padding;
		}

		if ( ! empty( $area_bg_color ) ) {
			$area_styles['--qwfw-atw-background-color'] = $area_bg_color;
		}

		if ( ! empty( $area_border_width ) ) {
			if ( qode_wishlist_for_woocommerce_string_ends_with_allowed_units( $area_border_width ) ) {
				$area_styles['border-width'] = $area_border_width;
			} else {
				$area_styles['border-width'] = (int) $area_border_width . 'px';
			}
		}

		if ( ! empty( $area_border_style ) ) {
			$area_styles['border-style'] = $area_border_style;
		}

		if ( ! empty( $area_border_color ) ) {
			$area_styles['border-color'] = $area_border_color;

			if ( empty( $area_border_width ) ) {
				$area_styles['border-width'] = '1px';
			}
		}

		if ( ! empty( $area_border_radius ) ) {
			if ( qode_wishlist_for_woocommerce_string_ends_with_allowed_units( $area_border_radius ) ) {
				$area_styles['--qwfw-atw-border-radius'] = $area_border_radius;
			} else {
				$area_styles['--qwfw-atw-border-radius'] = (int) $area_border_radius . 'px';
			}
		}

		// Button hover styles.
		if ( ! empty( $area_hover_bg_color ) ) {
			$area_hover_styles['--qwfw-atw-hover-background-color'] = $area_hover_bg_color;
		}

		if ( ! empty( $area_hover_border_color ) ) {
			$area_hover_styles['border-color'] = $area_hover_border_color;
		}

		// Button active styles.
		if ( ! empty( $area_active_bg_color ) ) {
			$area_active_styles['--qwfw-atw-active-background-color'] = $area_active_bg_color;
		}

		if ( ! empty( $area_active_border_color ) ) {
			$area_active_styles['border-color'] = $area_active_border_color;
		}

		if ( ! empty( $area_styles ) ) {
			foreach ( $main_selector as $selector ) {
				$style .= qode_wishlist_for_woocommerce_dynamic_style( $selector, $area_styles );
			}
		}

		if ( ! empty( $area_hover_styles ) ) {
			foreach ( $main_selector as $selector ) {
				$style .= qode_wishlist_for_woocommerce_dynamic_style(
					array(
						$selector . ':hover',
					),
					$area_hover_styles
				);
			}
		}

		if ( ! empty( $area_active_styles ) ) {
			foreach ( $main_selector as $selector ) {
				$style .= qode_wishlist_for_woocommerce_dynamic_style(
					array(
						$selector . '.qwfw--added',
					),
					$area_active_styles
				);
			}
		}

		// Button icon styles.
		$icon_styles = array();

		$fill_color   = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_fill_color' );
		$stroke_color = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_stroke_color' );

		if ( ! empty( $fill_color ) ) {
			$icon_styles['fill'] = $fill_color;
		}

		if ( ! empty( $stroke_color ) ) {
			$icon_styles['stroke'] = $stroke_color;
		}

		if ( ! empty( $icon_styles ) ) {
			foreach ( $main_selector as $selector ) {
				$style .= qode_wishlist_for_woocommerce_dynamic_style( $selector . ' .qwfw-m-icon svg', $icon_styles );
			}
		}

		$icon_hover_styles = array();

		$fill_hover_color   = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_fill_hover_color' );
		$stroke_hover_color = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_stroke_hover_color' );

		if ( ! empty( $fill_hover_color ) ) {
			$icon_hover_styles['fill'] = $fill_hover_color;
		}

		if ( ! empty( $stroke_hover_color ) ) {
			$icon_hover_styles['stroke'] = $stroke_hover_color;
		}

		if ( ! empty( $icon_hover_styles ) ) {
			foreach ( $main_selector as $selector ) {
				$style .= qode_wishlist_for_woocommerce_dynamic_style( $selector . ':hover .qwfw-m-icon svg', $icon_hover_styles );
			}
		}

		$icon_active_styles = array();

		$fill_active_color   = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_fill_active_color' );
		$stroke_active_color = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_stroke_active_color' );

		if ( ! empty( $fill_active_color ) ) {
			$icon_active_styles['fill'] = $fill_active_color;
		}

		if ( ! empty( $stroke_active_color ) ) {
			$icon_active_styles['stroke'] = $stroke_active_color;
		}

		if ( ! empty( $icon_active_styles ) ) {
			foreach ( $main_selector as $selector ) {
				$style .= qode_wishlist_for_woocommerce_dynamic_style( $selector . '.qwfw--added .qwfw-m-icon svg', $icon_active_styles );
			}
		}

		return $style;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_set_add_to_wishlist_styles' ) ) {
	/**
	 * Function that generates module inline styles
	 *
	 * @param string $style
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_set_add_to_wishlist_styles( $style ) {
		$style .= qode_wishlist_for_woocommerce_get_add_to_wishlist_styles();

		return $style;
	}

	add_filter( 'qode_wishlist_for_woocommerce_filter_add_inline_style', 'qode_wishlist_for_woocommerce_set_add_to_wishlist_styles' );
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_set_add_to_wishlist_editor_styles' ) ) {
	/**
	 * Function that generates module editor inline styles
	 *
	 * @param string $style
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_set_add_to_wishlist_editor_styles( $style ) {
		$style .= qode_wishlist_for_woocommerce_get_add_to_wishlist_styles( '.editor-styles-wrapper' );

		return $style;
	}

	add_filter( 'qode_wishlist_for_woocommerce_filter_add_inline_editor_style', 'qode_wishlist_for_woocommerce_set_add_to_wishlist_editor_styles' );
}
