<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Wishlist_For_WooCommerce_Wishlist_Table_Block' ) ) {
	class Qode_Wishlist_For_WooCommerce_Wishlist_Table_Block extends Qode_Wishlist_For_WooCommerce_Block {
		private static $instance;

		public function __construct() {
			// Set block data.
			$this->set_block_name( 'wishlist-table' );

			$this->set_block_options(
				array(
					'render_callback' => array( $this, 'dynamic_render_callback' ),
				)
			);

			parent::__construct();
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Wishlist_For_WooCommerce_Wishlist_Table_Block
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function dynamic_render_callback( $shortcode_atts ) {

			if ( class_exists( 'Qode_Wishlist_For_WooCommerce_Wishlist_Table_Shortcode' ) ) {
				ob_start();

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Qode_Wishlist_For_WooCommerce_Wishlist_Table_Shortcode::call_shortcode( $shortcode_atts );

				$html = ob_get_clean();
			} else {
				$html = esc_html__( 'Wishlist Table shortcode does not exist.', 'qode-wishlist-for-woocommerce' );
			}

			return $html;
		}
	}

	Qode_Wishlist_For_WooCommerce_Wishlist_Table_Block::get_instance();
}
