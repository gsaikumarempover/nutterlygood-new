<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_add_general_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function qode_wishlist_for_woocommerce_add_general_options( $page ) {

		if ( $page ) {

			$welcome_section = $page->add_section_element(
				array(
					'layout'      => 'welcome',
					'name'        => 'qode_wishlist_for_woocommerce_global_plugins_options_welcome_section',
					'title'       => esc_html__( 'Welcome to Qode Wishlist for WooCommerce', 'qode-wishlist-for-woocommerce' ),
					'description' => esc_html__( 'It\'s time to set up the Wishlist feature on your website', 'qode-wishlist-for-woocommerce' ),
					'icon'        => QODE_WISHLIST_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/img/icon.png',
				)
			);

			// Hook to include additional options after module options.
			do_action( 'qode_wishlist_for_woocommerce_action_before_general_options_map', $page );

			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_wishlist_for_woocommerce_enable_svg_uploads',
					'title'         => esc_html__( 'Enable Custom SVG Upload', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'There is a potential security risk when enabling uploads of any SVG files. We\'ll try to sanitize the unfiltered files, including the removal of any malicious code & scripts. We recommend enabling this only if you understand the security risks involved.', 'qode-wishlist-for-woocommerce' ),
					'default_value' => 'no',
				)
			);

			// Hook to include additional options after module options.
			do_action( 'qode_wishlist_for_woocommerce_action_after_general_options_map', $page );
		}
	}

	add_action( 'qode_wishlist_for_woocommerce_action_default_options_init', 'qode_wishlist_for_woocommerce_add_general_options' );
}
