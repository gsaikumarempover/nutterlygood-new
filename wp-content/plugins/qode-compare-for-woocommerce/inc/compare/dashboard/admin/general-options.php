<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_compare_for_woocommerce_add_general_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function qode_compare_for_woocommerce_add_general_options( $page ) {

		if ( $page ) {

			$welcome_section = $page->add_section_element(
				array(
					'layout'      => 'welcome',
					'name'        => 'qode_compare_for_woocommerce_global_plugins_options_welcome_section',
					'title'       => esc_html__( 'Welcome to Qode Compare for WooCommerce', 'qode-compare-for-woocommerce' ),
					'description' => esc_html__( 'It\'s time to set up the Compare feature on your website', 'qode-compare-for-woocommerce' ),
					'icon'        => QODE_COMPARE_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/img/icon.png',
				)
			);
		}
	}

	add_action( 'qode_compare_for_woocommerce_action_default_options_init', 'qode_compare_for_woocommerce_add_general_options' );
	add_action( 'qode_compare_for_woocommerce_action_comparison_table_before_options_map', 'qode_compare_for_woocommerce_add_general_options' );
}
