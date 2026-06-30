<?php

if ( ! class_exists( 'Nuttergood_Qode_Product_List_Icons' ) ) {
	/**
	 * Renders QODE wishlist, quick view, and compare icons inside GreenPath product list cards.
	 */
	class Nuttergood_Qode_Product_List_Icons {
		private static $instance;

		/**
		 * @var array<string, string>
		 */
		private static $list_settings = array(
			'enable_wishlist'        => 'yes',
			'enable_quickview'       => 'yes',
			'enable_compare_product' => 'yes',
		);

		/**
		 * @var bool
		 */
		private static $in_product_list = false;

		/**
		 * @var array<int, bool>
		 */
		private static $rendered_wishlist_ids = array();

		public function __construct() {
			$hooks = array(
				'greenpath_core_action_product_list_item_additional_content',
				'greenpath_action_product_list_item_additional_content',
			);

			foreach ( $hooks as $hook ) {
				add_action( $hook, array( $this, 'render_wishlist' ), 15 );
				add_action( $hook, array( $this, 'render_quick_view' ), 16 );
				add_action( $hook, array( $this, 'render_compare' ), 17 );
			}

			add_filter( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_wrapper_classes', array( $this, 'add_greenpath_theme_class' ), 10, 2 );
			add_filter( 'qode_quick_view_for_woocommerce_filter_quick_view_button_wrapper_classes', array( $this, 'add_greenpath_theme_class' ), 10, 1 );
			add_filter( 'qode_compare_for_woocommerce_filter_compare_button_holder_classes', array( $this, 'add_compare_button_classes' ), 10, 2 );
			add_filter( 'qode_compare_for_woocommerce_filter_compare_button', array( $this, 'format_compare_button' ), 10, 2 );
			add_filter( 'qode_quick_view_for_woocommerce_filter_quick_view_button_icon', array( $this, 'replace_quick_view_icon' ), 10, 2 );

			add_action( 'elementor/frontend/widget/before_render', array( $this, 'capture_settings' ), 10, 1 );
		}

		/**
		 * Sync icon visibility with GreenPath product list shortcode settings.
		 *
		 * @param array<string, string> $settings Shortcode or widget settings.
		 */
		public static function set_list_settings( $settings ) {
			if ( ! is_array( $settings ) ) {
				return;
			}

			foreach ( array( 'enable_wishlist', 'enable_quickview', 'enable_compare_product' ) as $key ) {
				if ( isset( $settings[ $key ] ) ) {
					self::$list_settings[ $key ] = $settings[ $key ];
				}
			}
		}

		/**
		 * @return Nuttergood_Qode_Product_List_Icons
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * @param \Elementor\Widget_Base $widget Elementor widget instance.
		 */
		public function capture_settings( $widget ) {
			if ( 'greenpath_core_product_list' !== $widget->get_name() ) {
				return;
			}

			$settings = $widget->get_settings_for_display();

			self::$list_settings = array(
				'enable_wishlist'        => is_user_logged_in() ? ( $settings['enable_wishlist'] ?? 'yes' ) : 'no',
				'enable_quickview'       => $settings['enable_quickview'] ?? 'yes',
				'enable_compare_product' => 'no',
			);
		}

		/**
		 * @param string $key Setting key.
		 *
		 * @return bool
		 */
		private function is_enabled( $key ) {
			return 'no' !== ( self::$list_settings[ $key ] ?? 'yes' );
		}

		public function render_wishlist() {
			if ( ! is_user_logged_in() || ! $this->is_enabled( 'enable_wishlist' ) || ! class_exists( 'Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Shortcode' ) ) {
				return;
			}

			$product_id = (int) get_the_ID();
			if ( $product_id && isset( self::$rendered_wishlist_ids[ $product_id ] ) ) {
				return;
			}

			if ( $product_id ) {
				self::$rendered_wishlist_ids[ $product_id ] = true;
			}

			echo Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Shortcode::call_shortcode(
				array(
					'item_id'         => get_the_ID(),
					'button_type'     => 'icon',
					'button_behavior' => 'view',
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		public function render_quick_view() {
			if ( ! $this->is_enabled( 'enable_quickview' ) || ! class_exists( 'Qode_Quick_View_For_WooCommerce_Quick_View_Button_Shortcode' ) ) {
				return;
			}

			self::$in_product_list = true;

			echo Qode_Quick_View_For_WooCommerce_Quick_View_Button_Shortcode::call_shortcode(
				array(
					'item_id' => get_the_ID(),
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			self::$in_product_list = false;
		}

		public function render_compare() {
			// Compare is disabled on product cards site-wide.
			return;

			if ( ! $this->is_enabled( 'enable_compare_product' ) || ! class_exists( 'Qode_Compare_For_WooCommerce_Compare_Button_Shortcode' ) ) {
				return;
			}

			self::$in_product_list = true;

			echo Qode_Compare_For_WooCommerce_Compare_Button_Shortcode::call_shortcode(
				array(
					'item_id'     => get_the_ID(),
					'button_type' => 'solid',
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			self::$in_product_list = false;
		}

		/**
		 * @param array<int, string> $classes Wrapper classes.
		 *
		 * @return array<int, string>
		 */
		public function add_greenpath_theme_class( $classes ) {
			if ( is_array( $classes ) ) {
				$classes[] = 'qodef-greenpath-theme';
			}

			return $classes;
		}

		/**
		 * @param array<int, string> $holder_classes Compare button classes.
		 * @param array<string, mixed> $atts         Shortcode attributes.
		 *
		 * @return array<int, string>
		 */
		public function add_compare_button_classes( $holder_classes, $atts ) {
			if ( ! self::$in_product_list || ! is_array( $holder_classes ) ) {
				return $holder_classes;
			}

			$holder_classes[] = 'qcfw-position--shortcode';

			return $holder_classes;
		}

		/**
		 * @param string               $html Compare button markup.
		 * @param array<string, mixed> $atts Shortcode attributes.
		 *
		 * @return string
		 */
		public function format_compare_button( $html, $atts ) {
			if ( ! self::$in_product_list || empty( $html ) ) {
				return $html;
			}

			$html = str_replace( 'class="qcfw-button-text"', 'class="qcfw-button-text qodef--hide"', $html );

			if ( function_exists( 'greenpath_get_svg_icon' ) && false === strpos( $html, 'qcfw-button-icon' ) ) {
				$icon_markup = '<span class="qcfw-button-icon">' . greenpath_get_svg_icon( 'compare' ) . '</span>';
				$html        = str_replace( '<span class="qcfw-spinner-icon">', $icon_markup . '<span class="qcfw-spinner-icon">', $html );
			}

			return $html;
		}

		/**
		 * Use GreenPath fill-based eye icon instead of the plugin stroke icon.
		 *
		 * @param string $icon_html Icon markup.
		 * @param int    $item_id   Product ID.
		 *
		 * @return string
		 */
		public function replace_quick_view_icon( $icon_html, $item_id ) {
			if ( ! self::$in_product_list || ! function_exists( 'greenpath_get_svg_icon' ) ) {
				return $icon_html;
			}

			return greenpath_get_svg_icon( 'eye' );
		}
	}

	Nuttergood_Qode_Product_List_Icons::get_instance();
}