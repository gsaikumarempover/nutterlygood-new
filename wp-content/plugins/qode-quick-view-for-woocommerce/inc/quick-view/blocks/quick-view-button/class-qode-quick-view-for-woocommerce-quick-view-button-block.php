<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Quick_View_For_WooCommerce_Quick_View_Button_Block' ) ) {
	class Qode_Quick_View_For_WooCommerce_Quick_View_Button_Block extends Qode_Quick_View_For_WooCommerce_Block {
		private static $instance;

		public function __construct() {
			// Set block data.
			$this->set_block_name( 'quick-view-button' );

			$this->set_block_options(
				array(
					'render_callback' => array( $this, 'dynamic_render_callback' ),
					'attributes'      => array(
						'item_id'     => array(
							'type'    => 'number',
							'default' => '',
						),
						'button_type' => array(
							'type'    => 'string',
							'default' => 'icon-with-text',
						),
					),
				)
			);

			parent::__construct();
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Quick_View_For_WooCommerce_Quick_View_Button_Block
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function dynamic_render_callback( $shortcode_atts ) {

			if ( class_exists( 'Qode_Quick_View_For_WooCommerce_Quick_View_Button_Shortcode' ) ) {
				ob_start();

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Qode_Quick_View_For_WooCommerce_Quick_View_Button_Shortcode::call_shortcode( $shortcode_atts );

				$html = ob_get_clean();
			} else {
				$html = esc_html__( 'Quick view Button shortcode does not exist', 'qode-quick-view-for-woocommerce' );
			}

			return $html;
		}
	}

	Qode_Quick_View_For_WooCommerce_Quick_View_Button_Block::get_instance();
}
