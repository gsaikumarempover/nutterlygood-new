<?php

if ( ! class_exists( 'GreenPathCore_WooCommerce_YITH_Compare' ) ) {
	class GreenPathCore_WooCommerce_YITH_Compare {
		private static $instance;

		public function __construct() {

			if ( qode_framework_is_installed( 'yith-compare' ) ) {
				// Init
				add_action( 'init', array( $this, 'init' ), 25 );
			}
		}

		/**
		 * @return GreenPathCore_WooCommerce_YITH_Compare
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function init() {

			// Unset default templates modules
			$this->unset_templates_modules();

			// Change default templates position
			$this->change_templates_position();

			// Override default templates
			$this->override_templates();
		}

		function unset_templates_modules() {
			// Remove button element on shop pages
			global $yith_woocompare;

			if ( $yith_woocompare->is_frontend() ) {

				remove_action(
					'woocommerce_after_shop_loop_item',
					array(
						$yith_woocompare->obj,
						'add_compare_link',
					),
					20
				);
			}

		}

		function change_templates_position() {
			// Add button element for shop pages

			global $yith_woocompare;

			if ( $yith_woocompare->is_frontend() && 'yes' === get_option( 'yith_woocompare_compare_button_in_products_list', 'no' ) ) {

				add_action(
					'greenpath_action_product_list_item_additional_content',
					array(
						$yith_woocompare->obj,
						'add_compare_link',
					)
				);
				add_action(
					'greenpath_core_action_product_list_item_additional_content',
					array(
						$yith_woocompare->obj,
						'add_compare_link',
					),
					17
				);
				add_action(
					'greenpath_core_action_product_list_item_compare',
					array(
						$yith_woocompare->obj,
						'add_compare_link',
					)
				);
			}
		}

		function override_templates() {

		}
	}

	GreenPathCore_WooCommerce_YITH_Compare::get_instance();
}
