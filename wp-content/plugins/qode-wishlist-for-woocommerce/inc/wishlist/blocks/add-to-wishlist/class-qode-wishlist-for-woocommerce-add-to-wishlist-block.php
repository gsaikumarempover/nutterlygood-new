<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Block' ) ) {
	class Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Block extends Qode_Wishlist_For_WooCommerce_Block {
		private static $instance;

		public function __construct() {
			// Set block data.
			$this->set_block_name( 'add-to-wishlist' );

			$this->set_block_options(
				array(
					'render_callback' => array( $this, 'dynamic_render_callback' ),
					'attributes'      => array(
						'item_id'         => array(
							'type'    => 'number',
							'default' => '',
						),
						'button_behavior' => array(
							'type'    => 'string',
							'default' => '',
						),
						'button_type'     => array(
							'type'    => 'string',
							'default' => '',
						),
						'show_count'      => array(
							'type'    => 'string',
							'default' => '',
						),
					),
				)
			);

			parent::__construct();
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Block
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function dynamic_render_callback( $shortcode_atts ) {

			if ( class_exists( 'Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Shortcode' ) ) {
				ob_start();

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Shortcode::call_shortcode( $shortcode_atts );

				$html = ob_get_clean();
			} else {
				$html = esc_html__( 'Add to Wishlist shortcode does not exist.', 'qode-wishlist-for-woocommerce' );
			}

			return $html;
		}
	}

	Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Block::get_instance();
}
