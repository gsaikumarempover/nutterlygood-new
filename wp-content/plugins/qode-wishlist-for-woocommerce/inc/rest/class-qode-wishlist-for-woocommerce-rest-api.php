<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Wishlist_For_WooCommerce_Rest_API' ) ) {
	/**
	 * Rest API class with configuration
	 */
	class Qode_Wishlist_For_WooCommerce_Rest_API {
		private static $instance;
		private $rest_namespace;

		public function __construct() {
			// Init variables.
			$this->set_rest_namespace( 'qode-wishlist-for-woocommerce/v1' );

			// Localize main script with additional variables.
			add_filter( 'qode_wishlist_for_woocommerce_filter_localize_main_plugin_script', array( $this, 'localize_script' ) );

			// Function that register Rest API routes.
			add_action( 'rest_api_init', array( $this, 'register_rest_api_route' ) );
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Wishlist_For_WooCommerce_Rest_API
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function get_rest_namespace() {
			return $this->rest_namespace;
		}

		public function set_rest_namespace( $rest_namespace ) {
			$this->rest_namespace = $rest_namespace;
		}

		public function localize_script( $global_vars ) {
			$global_vars['restUrl']   = esc_url_raw( rest_url() );
			$global_vars['restNonce'] = wp_create_nonce( 'wp_rest' );

			return apply_filters( 'qode_wishlist_for_woocommerce_filter_rest_api_global_variables', $global_vars, $this->get_rest_namespace() );
		}

		public function register_rest_api_route() {
			$routes = apply_filters( 'qode_wishlist_for_woocommerce_filter_rest_api_routes', array() );

			if ( ! empty( $routes ) ) {
				foreach ( $routes as $route ) {
					$permission_callback = isset( $route['permission_callback'] ) && ! empty( $route['permission_callback'] ) ? $route['permission_callback'] : '__return_true';

					register_rest_route(
						$this->get_rest_namespace(),
						esc_attr( $route['route'] ),
						array(
							'methods'             => $route['methods'],
							'callback'            => $route['callback'],
							'permission_callback' => $permission_callback,
							'args'                => $route['args'],
						)
					);
				}
			}
		}
	}

	Qode_Wishlist_For_WooCommerce_Rest_API::get_instance();
}
