<?php

class GreenPathCore_Standard_Extended_Header extends GreenPathCore_Header {
	private static $instance;

	public function __construct() {
		$this->set_layout( 'standard-extended' );
		$this->set_search_layout( 'covers-header' );
		$this->default_header_height = 156;

		parent::__construct();
	}

	/**
	 * @return GreenPathCore_Standard_Extended_Header
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function set_inline_header_styles( $style ) {
		$styles = array();

		$height = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_header_height' );

		if ( ! empty( $height ) ) {
			$styles['--qode-extended-header-height'] = intval( $height ) . 'px';
		}

		if ( ! empty( $styles ) ) {
			$style .= qode_framework_dynamic_style( '.qodef-header--standard-extended #qodef-page-header', $styles );
		}

		$top_styles = array();

		$top_background_color = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_header_top_background_color' );
		$top_side_padding     = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_header_top_side_padding' );
		$top_border_color     = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_header_top_border_color' );
		$top_border_width     = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_header_top_border_width' );
		$top_border_style     = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_header_top_border_style' );

		if ( ! empty( $top_background_color ) ) {
			$top_styles['background-color'] = $top_background_color;
		}

		if ( '' !== $top_side_padding ) {
			if ( qode_framework_string_ends_with_space_units( $top_side_padding ) ) {
				$top_styles['padding-left']  = $top_side_padding;
				$top_styles['padding-right'] = $top_side_padding;
			} else {
				$top_styles['padding-left']  = intval( $top_side_padding ) . 'px';
				$top_styles['padding-right'] = intval( $top_side_padding ) . 'px';
			}
		}

		if ( ! empty( $top_border_color ) ) {
			$top_styles['border-bottom-color'] = $top_border_color;

			if ( empty( $top_border_width ) ) {
				$top_styles['border-bottom-width'] = '1px';
			}
		}

		if ( ! empty( $top_border_width ) ) {
			$top_styles['border-bottom-width'] = intval( $top_border_width ) . 'px';
		}

		if ( ! empty( $top_border_style ) ) {
			$top_styles['border-bottom-style'] = $top_border_style;
		}

		if ( ! empty( $top_styles ) ) {
			$style .= qode_framework_dynamic_style( '.qodef-header--standard-extended #qodef-page-header .qodef-header-section.qodef--top', $top_styles );
		}

		$bottom_styles = array();

		$bottom_background_color = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_header_bottom_background_color' );
		$bottom_side_padding     = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_header_bottom_side_padding' );
		$bottom_border_color     = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_header_bottom_border_color' );
		$bottom_border_width     = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_header_bottom_border_width' );
		$bottom_border_style     = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_header_bottom_border_style' );

		if ( ! empty( $bottom_background_color ) ) {
			$bottom_styles['background-color'] = $bottom_background_color;
		}

		if ( '' !== $bottom_side_padding ) {
			if ( qode_framework_string_ends_with_space_units( $bottom_side_padding ) ) {
				$bottom_styles['padding-left']  = $bottom_side_padding;
				$bottom_styles['padding-right'] = $bottom_side_padding;
			} else {
				$bottom_styles['padding-left']  = intval( $bottom_side_padding ) . 'px';
				$bottom_styles['padding-right'] = intval( $bottom_side_padding ) . 'px';
			}
		}

		if ( ! empty( $bottom_border_color ) ) {
			$bottom_styles['border-bottom-color'] = $bottom_border_color;

			if ( empty( $bottom_border_width ) ) {
				$bottom_styles['border-bottom-width'] = '1px';
			}
		}

		if ( ! empty( $bottom_border_width ) ) {
			$bottom_styles['border-bottom-width'] = intval( $bottom_border_width ) . 'px';
		}

		if ( ! empty( $bottom_border_style ) ) {
			$bottom_styles['border-bottom-style'] = $bottom_border_style;
		}

		if ( ! empty( $bottom_styles ) ) {
			$style .= qode_framework_dynamic_style( '.qodef-header--standard-extended #qodef-page-header .qodef-header-section.qodef--bottom', $bottom_styles );
		}

		return $style;
	}
}
