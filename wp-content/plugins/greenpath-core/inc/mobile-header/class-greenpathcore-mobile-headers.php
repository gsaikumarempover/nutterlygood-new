<?php

class GreenPathCore_Mobile_Headers {
	private static $instance;
	private $layout_meta;
	private $layouts;
	private $mobile_header_object;

	public function __construct() {

		// Includes header layouts
		$this->include_elements();

		// Set module variables
		add_action( 'wp', array( $this, 'set_variables' ) ); // wp hook is set because we need to wait global wp_query object to instance in order to get page id

		// Overrides default header template of theme
		add_action( 'wp', array( $this, 'render_template' ) );

		// Add module body classes
		add_filter( 'body_class', array( $this, 'add_body_classes' ) );

		//Add widget areas
		add_action( 'widgets_init', array( $this, 'add_header_widget_areas' ) );

		//Generates menu typography styles
		add_filter( 'greenpath_filter_add_inline_style', array( $this, 'set_menu_typography_styles' ) );
	}

	/**
	 * @return GreenPathCore_Mobile_Headers
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function get_layout_meta() {
		return $this->layout_meta;
	}

	public function set_layout_meta( $layout_meta ) {
		$this->layout_meta = $layout_meta;
	}

	public function get_layouts() {
		return $this->layouts;
	}

	public function set_layouts( $layouts ) {
		$this->layouts = $layouts;
	}

	public function get_mobile_header_object() {
		return $this->mobile_header_object;
	}

	public function set_mobile_header_object( $mobile_header_object ) {
		$this->mobile_header_object = $mobile_header_object;
	}

	function include_elements() {

		foreach ( glob( GREENPATH_CORE_INC_PATH . '/mobile-header/dashboard/*/*.php' ) as $admin ) {
			include_once $admin;
		}
		foreach ( glob( GREENPATH_CORE_INC_PATH . '/mobile-header/layouts/*/include.php' ) as $layout ) {
			include_once $layout;
		}
	}

	function set_variables() {
		$layout_meta = greenpath_core_get_post_value_through_levels( 'qodef_mobile_header_layout' );
		$layouts     = apply_filters( 'greenpath_core_filter_register_mobile_header_layouts', array() );

		$this->set_layout_meta( $layout_meta );
		$this->set_layouts( $layouts );

		if ( ! empty( $layout_meta ) && ! empty( $layouts ) ) {
			foreach ( $layouts as $key => $value ) {
				if ( $layout_meta === $key ) {

					$this->set_mobile_header_object( $value::get_instance() );
				}
			}
		}
	}

	function load_template() {
		// template is properly escaped inside html file
		echo $this->get_mobile_header_object()->load_template(); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}

	function render_template() {
		$header_object = $this->get_mobile_header_object();

		if ( ! empty( $header_object ) ) {
			$template_hook = $header_object->is_whole_header_override() ? 'greenpath_filter_mobile_header_template' : 'greenpath_filter_mobile_header_content_template';

			add_filter( $template_hook, array( $this, 'load_template' ), 11 );
		}
	}

	function add_body_classes( $classes ) {
		$header_layout = greenpath_core_get_post_value_through_levels( 'qodef_mobile_header_layout' );
		$classes[]     = ! empty( $header_layout ) ? 'qodef-mobile-header--' . $header_layout : '';

		$classes[] = 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_mobile_header_scroll_appearance' ) ? 'qodef-mobile-header-appearance--sticky' : '';

		if ( qode_framework_is_installed( 'woocommerce' ) && ( greenpath_core_is_woo_page( 'archive' ) || 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_mobile_enable_product_search' ) ) ) {
			$classes[] = 'qodef-mobile-header--search-enabled';
		}

		return $classes;
	}

	function add_header_widget_areas() {
		register_sidebar(
			array(
				'id'            => 'qodef-mobile-header-widget-area-one',
				'name'          => esc_html__( 'Mobile Header - Area One', 'greenpath-core' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s qodef-mobile-header-widget-area-one" data-area="mobile-header-one">',
				'after_widget'  => '</div>',
				'description'   => esc_html__( 'Widgets added here will appear in mobile header widget area', 'greenpath-core' ),
			)
		);

		register_sidebar(
			array(
				'id'            => 'qodef-mobile-header-widget-area-two',
				'name'          => esc_html__( 'Mobile Header - Area Two', 'greenpath-core' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s qodef-mobile-header-widget-area-two" data-area="mobile-header-two">',
				'after_widget'  => '</div>',
				'description'   => esc_html__( 'Widgets added here will appear in mobile header widget area', 'greenpath-core' ),
			)
		);

		register_sidebar(
			array(
				'id'            => 'qodef-mobile-header-widget-area-three',
				'name'          => esc_html__( 'Mobile Header - Area Three', 'greenpath-core' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s qodef-mobile-header-widget-area-three" data-area="mobile-header-three">',
				'after_widget'  => '</div>',
				'description'   => esc_html__( 'Widgets added here will appear in mobile header widget area', 'greenpath-core' ),
			)
		);
	}

	function set_menu_typography_styles( $style ) {
		$header_selector         = apply_filters( 'greenpath_core_filter_nav_menu_mobile_header_selector', '.qodef-mobile-header-navigation' );
		$first_lvl_styles        = greenpath_core_get_typography_styles( 'qodef_mobile_1st_lvl' );
		$first_lvl_hover_styles  = greenpath_core_get_typography_hover_styles( 'qodef_mobile_1st_lvl' );
		$second_lvl_styles       = greenpath_core_get_typography_styles( 'qodef_mobile_2nd_lvl' );
		$second_lvl_hover_styles = greenpath_core_get_typography_hover_styles( 'qodef_mobile_2nd_lvl' );

		if ( ! empty( $first_lvl_styles ) ) {
			$style .= qode_framework_dynamic_style( $header_selector . ' > ul > li > a', $first_lvl_styles );

			if ( isset( $first_lvl_styles['color'] ) && ! empty( $first_lvl_styles['color'] ) ) {
				$style .= qode_framework_dynamic_style( $header_selector . ' ul li .qodef-menu-item-arrow', array( 'color' => $first_lvl_styles['color'] ) );
			}
		}

		if ( ! empty( $first_lvl_hover_styles ) ) {
			$style .= qode_framework_dynamic_style( $header_selector . ' > ul > li > a:hover', $first_lvl_hover_styles );

			if ( isset( $first_lvl_hover_styles['color'] ) && ! empty( $first_lvl_hover_styles['color'] ) ) {
				$style .= qode_framework_dynamic_style( $header_selector . ' ul li .qodef-menu-item-arrow:hover', array( 'color' => $first_lvl_hover_styles['color'] ) );
			}
		}

		$first_lvl_active_color = greenpath_core_get_option_value( 'admin', 'qodef_mobile_1st_lvl_active_color' );

		if ( ! empty( $first_lvl_active_color ) ) {
			$first_lvl_active_styles = array(
				'color' => $first_lvl_active_color,
			);

			$style .= qode_framework_dynamic_style(
				array(
					$header_selector . ' > ul > li.current-menu-ancestor > a',
					$header_selector . ' > ul > li.current-menu-item > a',
				),
				$first_lvl_active_styles
			);
		}

		if ( ! empty( $second_lvl_styles ) ) {
			$style .= qode_framework_dynamic_style( $header_selector . ' ul li ul li > a', $second_lvl_styles );

			if ( isset( $second_lvl_styles['color'] ) && ! empty( $second_lvl_styles['color'] ) ) {
				$style .= qode_framework_dynamic_style( $header_selector . ' ul li ul li .qodef-menu-item-arrow', array( 'color' => $second_lvl_styles['color'] ) );
			}
		}

		if ( ! empty( $second_lvl_hover_styles ) ) {
			$style .= qode_framework_dynamic_style( $header_selector . ' ul li ul li > a:hover', $second_lvl_hover_styles );

			if ( isset( $second_lvl_hover_styles['color'] ) && ! empty( $second_lvl_hover_styles['color'] ) ) {
				$style .= qode_framework_dynamic_style( $header_selector . ' ul li ul li .qodef-menu-item-arrow:hover', array( 'color' => $second_lvl_hover_styles['color'] ) );
			}
		}

		$second_lvl_active_color = greenpath_core_get_option_value( 'admin', 'qodef_mobile_2nd_lvl_active_color' );

		if ( ! empty( $second_lvl_active_color ) ) {
			$second_lvl_active_styles = array(
				'color' => $second_lvl_active_color,
			);

			$style .= qode_framework_dynamic_style(
				array(
					$header_selector . ' ul li ul li.current-menu-ancestor > a',
					$header_selector . ' ul li ul li.current-menu-item > a',
				),
				$second_lvl_active_styles
			);
		}

		return $style;
	}
}

GreenPathCore_Mobile_Headers::get_instance();
