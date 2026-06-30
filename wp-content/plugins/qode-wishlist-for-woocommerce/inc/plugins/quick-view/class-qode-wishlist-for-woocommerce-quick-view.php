<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Wishlist_For_WooCommerce_Quick_View' ) ) {
	class Qode_Wishlist_For_WooCommerce_Quick_View {
		private static $instance;

		public function __construct() {

			if ( qode_wishlist_for_woocommerce_is_installed( 'quick-view' ) ) {

				// Make sure the module is enabled by Global options.
				if ( $this->is_wishlist_enabled() ) {

					// Add button on quick view (permission 18 is set to be after the module initialization).
					add_action( 'init', array( $this, 'set_button_position' ), 18 );
				}
			}
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Wishlist_For_WooCommerce_Quick_View
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function is_wishlist_enabled() {
			return apply_filters( 'qode_wishlist_for_woocommerce_filter_quick_view_has_add_to_wishlist', true );
		}

		public function set_button_position() {
			$button_position = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_button_single_position' );

			$button_position_map = apply_filters(
				'qode_wishlist_for_woocommerce_filter_quick_view_add_to_wishlist_button_position',
				array(
					// button is hooked with priority 25 - woocommerce_template_single_add_to_cart.
					'after-add-to-cart'      => array(
						'hook'     => 'qode_quick_view_for_woocommerce_action_product_summary',
						'priority' => 28,
					),
					// button is hooked with priority 25 - woocommerce_template_single_add_to_cart.
					'before-add-to-cart'     => array(
						'hook'     => 'qode_quick_view_for_woocommerce_action_product_summary',
						'priority' => 23,
					),
					'after-add-to-cart-form' => array(
						'hook'     => 'woocommerce_after_add_to_cart_button',
						'priority' => 10,
					),
					// default smallest hook is 10.
					'on-thumbnail'           => array(
						'hook'     => 'qode_quick_view_for_woocommerce_action_product_image',
						'priority' => 5,
					),
					'after-summary'          => array(
						'hook'     => 'qode_quick_view_for_woocommerce_action_product_summary',
						'priority' => 90,
					),
					'before-summary'         => array(
						'hook'     => 'qode_quick_view_for_woocommerce_action_product_summary',
						'priority' => 2,
					),
					'before-title'           => array(
						'hook'     => 'qode_quick_view_for_woocommerce_action_product_summary',
						'priority' => 2,
					),
				)
			);

			if ( isset( $button_position_map[ $button_position ] ) ) {
				add_action( $button_position_map[ $button_position ]['hook'], array( $this, 'add_button' ), $button_position_map[ $button_position ]['priority'] );
			}
		}

		public function add_button() {

			if ( class_exists( 'Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Shortcode' ) ) {
				$prevent_print   = false;
				$button_position = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_button_single_position' );

				$shortcode_atts = array(
					'single_layout' => 'yes',
				);

				if ( 'after-add-to-cart-form' === $button_position ) {
					$shortcode_atts['custom_class'] = qode_wishlist_for_woocommerce_get_button_classes();
				}

				// Prevent double loading on single product page because we used native WooCommerce hooks on both place.
				if ( ( function_exists( 'wc_get_loop_prop' ) && empty( wc_get_loop_prop( 'qode_quick_view' ) ) ) || is_singular( 'product' ) ) {
					$prevent_print = true;
				}

				if ( ! $prevent_print ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Shortcode::call_shortcode( $shortcode_atts );
				}
			}
		}
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_init_quick_view_module' ) ) {
	/**
	 * Init quick view module instance.
	 */
	function qode_wishlist_for_woocommerce_init_quick_view_module() {
		Qode_Wishlist_For_WooCommerce_Quick_View::get_instance();
	}

	// Permission 15 is set in order to load after option initialization ( init_options method).
	add_action( 'init', 'qode_wishlist_for_woocommerce_init_quick_view_module', 15 );
}
