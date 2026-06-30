<?php

abstract class GreenPathCore_Mobile_Header {
	public $overriding_whole_header = false;
	private $layout;
	private $layout_slug = '';
	protected $default_header_height;
	protected $header_height;

	public function __construct() {
		$this->set_header_height();

		add_action( 'greenpath_core_action_before_main_css', array( $this, 'enqueue_additional_assets' ) );
		add_filter( 'greenpath_core_filter_nav_menu_mobile_header_selector', array( $this, 'set_nav_menu_header_selector' ) );
		add_filter( 'greenpath_filter_mobile_header_inner_class', array( $this, 'set_mobile_header_inner_classes' ), 10, 2 );
		add_filter( 'greenpath_filter_add_inline_style', array( $this, 'set_inline_mobile_header_styles' ) );
		add_filter( 'greenpath_core_filter_content_margin_mobile', array( $this, 'get_content_margin' ) );
		add_filter( 'greenpath_core_filter_title_padding_mobile', array( $this, 'get_title_padding' ) );
		add_filter( 'greenpath_filter_localize_main_js', array( $this, 'set_global_javascript_variables' ) );
	}

	public function get_layout() {
		return $this->layout;
	}

	public function set_layout( $layout ) {
		$this->layout = $layout;
	}

	public function get_layout_slug() {
		return $this->layout_slug;
	}

	public function set_layout_slug( $layout_slug ) {
		$this->layout_slug = $layout_slug;
	}

	public function is_whole_header_override() {
		return $this->overriding_whole_header;
	}

	public function set_overriding_whole_header( $overriding_whole_header ) {
		$this->overriding_whole_header = $overriding_whole_header;
	}

	public function enqueue_additional_assets() {
		return false;
	}

	/**
	 * swap dash with underscore
	 *
	 * header slug must be same as folder name, we're using dash as delimiter
	 * options and metaboxes, we're using underscore as delimiter
	 *
	 * @return mixed
	 */
	public function get_formatted_layout() {
		return str_replace( '-', '_', $this->get_layout() );
	}

	public function load_template( $parameters = array() ) {
		$parameters = apply_filters( 'greenpath_core_filter_mobile_header_template', $parameters );

		return greenpath_core_get_template_part( 'mobile-header/layouts/' . $this->get_layout(), 'templates/' . $this->get_layout(), $this->get_layout_slug(), $parameters );
	}

	public function set_nav_menu_header_selector( $selector ) {
		return $selector;
	}

	function set_mobile_header_inner_classes( $class, $layout ) {
		// Check is content in grid
		$class[] = 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_mobile_header_in_grid' ) ? 'qodef-content-grid' : '';

		return $class;
	}

	public function set_inline_mobile_header_styles( $style ) {
		$item_styles = array();

		$height           = greenpath_core_get_post_value_through_levels( 'qodef_' . $this->get_formatted_layout() . '_mobile_header_height' );
		$background_color = greenpath_core_get_post_value_through_levels( 'qodef_' . $this->get_formatted_layout() . '_mobile_header_background_color' );

		if ( ! empty( $height ) ) {
			$item_styles['height'] = intval( $height ) . 'px';
		}

		if ( ! empty( $background_color ) ) {
			$item_styles['background-color'] = $background_color;
			$style                          .= qode_framework_dynamic_style( '.qodef-mobile-header--' . $this->get_layout() . ' #qodef-mobile-header-navigation .qodef-m-inner', array( 'background-color' => $item_styles['background-color'] ) );
		}

		if ( ! empty( $item_styles ) ) {
			$style .= qode_framework_dynamic_style( '.qodef-mobile-header--' . $this->get_layout() . ' #qodef-page-mobile-header', $item_styles );
		}

		$inner_styles = array();

		$side_padding = greenpath_core_get_post_value_through_levels( 'qodef_' . $this->get_formatted_layout() . '_mobile_header_side_padding' );

		if ( '' !== $side_padding ) {
			if ( qode_framework_string_ends_with_space_units( $side_padding ) ) {
				$inner_styles['padding-left']  = $side_padding;
				$inner_styles['padding-right'] = $side_padding;
			} else {
				$inner_styles['padding-left']  = intval( $side_padding ) . 'px';
				$inner_styles['padding-right'] = intval( $side_padding ) . 'px';
			}
		}

		if ( ! empty( $inner_styles ) ) {
			$style .= qode_framework_dynamic_style( '.qodef-mobile-header--' . $this->get_layout() . ' #qodef-page-mobile-header-inner:not(.qodef-content-grid)', $inner_styles );
			$style .= qode_framework_dynamic_style( '.qodef-mobile-header--' . $this->get_layout() . ' .qodef-mobile-header-navigation > ul:not(.qodef-content-grid)', $inner_styles );
		}

		return $style;
	}

	public function content_behind_header() {
		return 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_content_behind_header' );
	}

	public function get_content_margin( $margin ) {

		if ( $this->content_behind_header() ) {
			$margin += $this->header_height;
		}

		return $margin;
	}

	public function get_title_padding( $padding ) {

		if ( $this->content_behind_header() ) {
			$padding += $this->header_height;
		}

		return $padding;
	}

	function set_header_height() {
		$header_height = greenpath_core_get_post_value_through_levels( 'qodef_' . $this->get_formatted_layout() . '_mobile_header_height' );
		$header_height = ! empty( $header_height ) ? intval( $header_height ) : $this->default_header_height;

		$this->header_height = apply_filters( 'greenpath_core_filter_set_mobile_header_height', $header_height );
	}

	function set_global_javascript_variables( $global_vars ) {
		$global_vars['mobileHeaderHeight'] = $this->header_height;

		return $global_vars;
	}
}
