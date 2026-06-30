<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_add_quick_view_global_options' ) ) {
	/**
	 * Function that add global options for this module
	 */
	function qode_quick_view_for_woocommerce_add_quick_view_global_options( $page ) {

		if ( $page ) {

			$welcome_section = $page->add_section_element(
				array(
					'layout'      => 'welcome',
					'name'        => 'qode_quick_view_for_woocommerce_global_plugins_options_welcome_section',
					'title'       => esc_html__( 'Welcome to Qode Quick View for WooCommerce', 'qode-quick-view-for-woocommerce' ),
					'description' => esc_html__( 'It\'s time to set up the Quick View feature on your website', 'qode-quick-view-for-woocommerce' ),
					'icon'        => QODE_QUICK_VIEW_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/img/icon.png',
				)
			);

			// Hook to include additional options after module options.
			do_action( 'qode_quick_view_for_woocommerce_action_before_global_options_map', $page );

			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_quick_view_for_woocommerce_enable_quick_view',
					'title'         => esc_html__( 'Enable', 'qode-quick-view-for-woocommerce' ),
					'description'   => esc_html__( 'Display the “Quick View” button in WooCommerce Product Loops (product lists shown on shop pages, archive pages and all other places where the product lists are displayed)', 'qode-quick-view-for-woocommerce' ),
					'default_value' => apply_filters( 'qode_quick_view_for_woocommerce_filter_enable_quick_view_default_value', 'yes' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_quick_view_for_woocommerce_enable_quick_view_on_mobile',
					'title'         => esc_html__( 'Enable on Mobile', 'qode-quick-view-for-woocommerce' ),
					'description'   => esc_html__( 'Display the "Quick View" button on mobile devices', 'qode-quick-view-for-woocommerce' ),
					'default_value' => apply_filters( 'qode_quick_view_for_woocommerce_filter_enable_quick_view_on_mobile_default_value', 'yes' ),
				)
			);

			// Hook to include additional options after module options.
			do_action( 'qode_quick_view_for_woocommerce_action_after_global_options_map', $page );
		}
	}

	add_action( 'qode_quick_view_for_woocommerce_action_default_options_init', 'qode_quick_view_for_woocommerce_add_quick_view_global_options' );
}
