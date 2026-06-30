<?php

if ( ! class_exists( 'GreenPathCore_WooCommerce_YITH_Color_and_Label_Variations' ) ) {
	class GreenPathCore_WooCommerce_YITH_Color_and_Label_Variations {
		private static $instance;

		public function __construct() {

			if ( qode_framework_is_installed( 'yith-color-and-label-variations' ) ) {
				// Init
				add_action( 'after_setup_theme', array( $this, 'init' ) );
			}
		}

		/**
		 * @return GreenPathCore_WooCommerce_YITH_Color_and_Label_Variations
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function init() {

			if ( ! is_admin() && function_exists( 'YITH_WCCL_Frontend' ) ) {
				// Unset default templates modules
				$this->unset_templates_modules();

				// Add new WooCommerce templates
				$this->add_templates();
			}
		}

		function unset_templates_modules() {

			// remove Variations on shop page
			remove_action( 'woocommerce_loop_add_to_cart_link', array( YITH_WCCL_Frontend(), 'add_select_options' ), 100 );
		}

		function add_templates() {

			// add WCCL to content
			add_action( 'greenpath_action_product_list_item_additional_content', array( YITH_WCCL_Frontend(), 'print_select_options' ), 1 );
			add_action( 'greenpath_core_action_product_list_item_additional_content', array( YITH_WCCL_Frontend(), 'print_select_options' ), 1 );
		}
	}

	GreenPathCore_WooCommerce_YITH_Color_and_Label_Variations::get_instance();
}
