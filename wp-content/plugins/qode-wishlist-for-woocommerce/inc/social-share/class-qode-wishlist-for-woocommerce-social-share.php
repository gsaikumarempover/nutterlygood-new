<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Wishlist_For_WooCommerce_Social_Share' ) ) {
	class Qode_Wishlist_For_WooCommerce_Social_Share {
		private static $instance;

		public function __construct() {

			// Make sure the module is enabled by Global options.
			if ( $this->is_enabled() ) {

				// Add wishlist table shortcode social share items.
				add_action( 'qode_wishlist_for_woocommerce_action_wishlist_table_section_heading_actions', array( $this, 'add_social_share' ), 10 );
				add_action( 'qode_wishlist_for_woocommerce_premium_action_wishlist_table_section_heading_actions', array( $this, 'add_social_share' ) );

				// Show social share items inside wishlist table shortcode - heading section.
				add_filter( 'qode_wishlist_for_woocommerce_filter_show_wishlist_table_section_heading_actions', '__return_true' );

				// Set additional hook for 3rd party elements.
				do_action( 'qode_wishlist_for_woocommerce_action_multi_wishlist_init' );
			}
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Wishlist_For_WooCommerce_Social_Share
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function is_enabled() {
			$enable = 'yes' === qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_enable_share' );

			return apply_filters( 'qode_wishlist_for_woocommerce_filter_is_social_share_enabled', $enable );
		}

		public function add_social_share( $params ) {
			$visibility = qode_wishlist_for_woocommerce_get_wishlist_table_meta( $params['table_name'], 'visibility', $params['all_items'] );

			// Disable social share feature for private wishlists.
			if ( 'private' !== $visibility ) {
				qode_wishlist_for_woocommerce_template_part( 'social-share/templates', 'social-share', '', $params );
			}
		}
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_init_social_share' ) ) {
	/**
	 * Init wishlist social share module instance.
	 */
	function qode_wishlist_for_woocommerce_init_social_share() {
		Qode_Wishlist_For_WooCommerce_Social_Share::get_instance();
	}

	// Permission 15 is set in order to load after option initialization ( init_options method).
	add_action( 'init', 'qode_wishlist_for_woocommerce_init_social_share', 15 );
}
