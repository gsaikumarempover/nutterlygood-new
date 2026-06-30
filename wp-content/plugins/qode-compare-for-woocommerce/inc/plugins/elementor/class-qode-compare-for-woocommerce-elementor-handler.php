<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

class Qode_Compare_For_WooCommerce_Elementor_Handler {
	private static $instance;

	public function __construct() {
		add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_scripts' ), 9 );
	}

	/**
	 * Instance of module class
	 *
	 * @return Qode_Compare_For_WooCommerce_Elementor_Handler
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function enqueue_scripts() {
		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters
		wp_enqueue_script( 'qode-compare-for-woocommerce-elementor', QODE_COMPARE_FOR_WOOCOMMERCE_INC_URL_PATH . '/plugins/elementor/assets/js/elementor.js', array( 'jquery', 'elementor-frontend', 'wp-i18n' ) );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_init_elementor_handler' ) ) {
	/**
	 * Function that initialize main page builder handler
	 */
	function qode_compare_for_woocommerce_init_elementor_handler() {
		Qode_Compare_For_WooCommerce_Elementor_Handler::get_instance();
	}

	add_action( 'init', 'qode_compare_for_woocommerce_init_elementor_handler', 1 );
}
