<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_add_rest_api_route_wishlist_table_block' ) ) {
	/**
	 * Extend main rest api routes with new case
	 *
	 * @param array $routes - list of rest routes
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_add_rest_api_route_wishlist_table_block( $routes ) {

		$routes['render-wishlist-table'] = array(
			'route'               => 'render-wishlist-table',
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'qode_wishlist_for_woocommerce_render_wishlist_table_item_callback',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
			'args'                => array(),
		);

		return $routes;
	}

	add_filter( 'qode_wishlist_for_woocommerce_filter_rest_api_routes', 'qode_wishlist_for_woocommerce_add_rest_api_route_wishlist_table_block' );
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_render_wishlist_table_item_callback' ) ) {
	/**
	 * Function that render Wishlist Table element
	 *
	 * @return void
	 */
	function qode_wishlist_for_woocommerce_render_wishlist_table_item_callback() {

		if ( ! is_user_logged_in() ) {
			qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-wishlist-for-woocommerce' ) );
		} else {

			if ( class_exists( 'Qode_Wishlist_For_WooCommerce_Wishlist_Table_Shortcode' ) ) {
				ob_start();

				// Set additional hook to initialize default premium hooks for this module.
				do_action( 'qode_wishlist_for_woocommerce_action_render_wishlist_table_block' );

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Qode_Wishlist_For_WooCommerce_Wishlist_Table_Shortcode::call_shortcode();

				$html = ob_get_clean();

				qode_wishlist_for_woocommerce_get_ajax_status( 'success', esc_html__( 'Item is successfully returned.', 'qode-wishlist-for-woocommerce' ), $html );
			} else {
				qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'Wishlist Table shortcode does not exist.', 'qode-wishlist-for-woocommerce' ) );
			}
		}
		die();
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
}
