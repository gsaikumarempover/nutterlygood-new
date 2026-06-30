<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Quick_View_For_WooCommerce_Shortcodes' ) ) {
	class Qode_Quick_View_For_WooCommerce_Shortcodes {
		private static $instance;

		public function __construct() {
			// Include shortcode abstract classes.
			add_action( 'qode_quick_view_for_woocommerce_action_framework_before_shortcodes_register', array( $this, 'include_shortcode_classes' ), 5 );

			// Include shortcodes.
			add_action( 'qode_quick_view_for_woocommerce_action_framework_before_shortcodes_register', array( $this, 'include_shortcodes' ) );

			// Register shortcodes.
			// Priority 11 set because include of files is called on default action 10.
			add_action( 'qode_quick_view_for_woocommerce_action_framework_before_shortcodes_register', array( $this, 'register_shortcodes' ), 11 );
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Quick_View_For_WooCommerce_Shortcodes
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function include_shortcode_classes() {
			include_once QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/shortcodes/class-qode-quick-view-for-woocommerce-shortcode.php';
		}

		public function include_shortcodes() {
			$shortcodes = array();

			foreach ( glob( QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/*/shortcodes/*', GLOB_ONLYDIR ) as $shortcode ) {
				$shortcodes[] = $shortcode;
			}

			$additional_shortcodes = apply_filters( 'qode_quick_view_for_woocommerce_filter_include_shortcodes', array() );

			$shortcodes = array_merge( $shortcodes, $additional_shortcodes );

			if ( ! empty( $shortcodes ) ) {
				foreach ( $shortcodes as $shortcode ) {
					foreach ( glob( $shortcode . '/include.php' ) as $shortcode_file ) {
						include_once $shortcode_file;
					}
				}
			}
		}

		public function register_shortcodes() {
			$framework_root = qode_quick_view_for_woocommerce_framework_get_framework_root();
			$shortcodes     = apply_filters( 'qode_quick_view_for_woocommerce_filter_register_shortcodes', $shortcodes = array() );

			if ( ! empty( $shortcodes ) ) {
				foreach ( $shortcodes as $shortcode ) {
					$framework_root->add_shortcode( new $shortcode() );
				}
			}
		}
	}

	Qode_Quick_View_For_WooCommerce_Shortcodes::get_instance();
}
