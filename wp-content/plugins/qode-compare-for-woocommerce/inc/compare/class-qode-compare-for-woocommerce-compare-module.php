<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Compare_For_WooCommerce_Compare_Module' ) ) {

	class Qode_Compare_For_WooCommerce_Compare_Module {
		private static $instance;

		public function __construct() {

			// Make sure the module is enabled by Global options.
			if ( $this->is_enabled() ) {
				// Load modal template.
				add_action( 'wp_footer', array( $this, 'load_modal' ) );

				// Set "Compare" button position (permission 18 is set to be after the module initialization).
				add_action( 'init', array( $this, 'set_button_position' ), 18 );

				// Check if user have comparison items in cache.
				add_action( 'init', array( $this, 'check_user_cached_comparison_items' ), 20 );

				add_filter( 'qode_compare_for_woocommerce_action_before_comparison_table', array( $this, 'add_table_title' ) );
			}
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Compare_For_WooCommerce_Compare_Module
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function is_enabled() {
			return apply_filters( 'qode_compare_for_woocommerce_filter_is_enabled', true, wp_is_mobile() );
		}

		public function add_button() {
			if ( apply_filters( 'qode_compare_for_woocommerce_filter_hide_compare_button', false ) ) {
				return;
			}

			if ( class_exists( 'Qode_Compare_For_WooCommerce_Compare_Button_Shortcode' ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo qode_compare_for_woocommerce_framework_wp_kses_html( 'html', Qode_Compare_For_WooCommerce_Compare_Button_Shortcode::call_shortcode() );
			}
		}

		public function load_modal() {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo qode_compare_for_woocommerce_framework_wp_kses_html( 'html', apply_filters( 'qode_compare_for_woocommerce_filter_modal_template', qode_compare_for_woocommerce_get_template_part( 'compare/templates', 'modal' ) ) );
		}

		public function set_button_position() {
			// Add button on product list.
			$this->set_button_loop_position();

			// Add button on single product.
			$this->set_button_single_position();
		}

		public function set_button_loop_position() {
			if ( 'no' !== qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_show_in_loop' ) ) {
				$button_position     = apply_filters( 'qode_compare_for_woocommerce_filter_compare_button_loop_position', 'after-button' );
				$button_position_map = apply_filters(
					'qode_compare_for_woocommerce_filter_compare_button_loop_position_map',
					array(
						'after-button' => array(
							'hook'     => 'woocommerce_after_shop_loop_item',
							'priority' => 11,
						),
						''             => array(
							'hook'     => 'woocommerce_after_shop_loop_item',
							'priority' => 11,
						),
					)
				);
				$this->add_button_position_logic( $button_position_map[ $button_position ] );
			}
		}


		private function add_button_position_logic( $button_position_map ) {
			$button_position_hook          = $button_position_map['hook'] ?? '';
			$button_position_hook_priority = $button_position_map['priority'] ?? 10;

			if ( is_array( $button_position_hook ) ) {
				foreach ( $button_position_hook as $hook_index => $hook_name ) {
					$hook_priority = $button_position_hook_priority;

					if ( is_array( $button_position_hook_priority ) ) {
						if ( isset( $button_position_hook_priority[ $hook_index ] ) ) {
							$hook_priority = $button_position_hook_priority[ $hook_index ];
						} else {
							$hook_priority = $button_position_hook_priority[0];
						}
					}

					add_action( $hook_name, array( $this, 'add_button' ), intval( $hook_priority ) );
				}
			} else {
				add_action( $button_position_hook, array( $this, 'add_button' ), intval( $button_position_hook_priority ) );
			}
		}

		public function set_button_single_position() {
			if ( 'yes' === qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_show_on_single_product' ) ) {
				$button_position     = apply_filters( 'qode_compare_for_woocommerce_filter_compare_button_single_position', 'after-button' );
				$button_position_map = apply_filters(
					'qode_compare_for_woocommerce_filter_compare_button_single_position_map',
					array(
						'after-button' => array(
							'hook'     => 'woocommerce_single_product_summary',
							'priority' => 31,
						),
						''             => array(
							'hook'     => 'woocommerce_single_product_summary',
							'priority' => 31,
						),
					)
				);

				$this->add_button_position_logic( $button_position_map[ $button_position ] );
			}
		}

		public function check_user_cached_comparison_items() {

			if ( is_user_logged_in() ) {
				qode_compare_for_woocommerce_update_user_comparison_items();
			}
		}

		public function add_table_title() {
			qode_compare_for_woocommerce_template_part( 'compare/templates', 'parts/table-title' );
		}
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_init_compare_module' ) ) {
	/**
	 * Init main compare module instance.
	 */
	function qode_compare_for_woocommerce_init_compare_module() {
		return Qode_Compare_For_WooCommerce_Compare_Module::get_instance();
	}

	add_action( 'init', 'qode_compare_for_woocommerce_init_compare_module', 15 );
	// Permission 15 is set in order to load after option initialization ( init_options method).
}
