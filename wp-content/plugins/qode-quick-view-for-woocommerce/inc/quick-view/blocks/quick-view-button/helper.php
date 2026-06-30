<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_add_rest_api_route_quick_view_button_block' ) ) {
	/**
	 * Extend main rest api routes with new case
	 *
	 * @param array $routes - list of rest routes
	 *
	 * @return array
	 */
	function qode_quick_view_for_woocommerce_add_rest_api_route_quick_view_button_block( $routes ) {

		$routes['render-quick-view-button'] = array(
			'route'               => 'render-quick-view-button',
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'qode_quick_view_for_woocommerce_render_quick_view_button_item_callback',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
			'args'                => array(
				'item_id'     => array(
					'required'          => false,
					'validate_callback' => function ( $param ) {
						return is_int( $param ) ? $param : absint( $param );
					},
					'description'       => esc_html__( 'ID is int value', 'qode-quick-view-for-woocommerce' ),
				),
				'button_type' => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return esc_attr( $param );
					},
					'description'       => esc_html__( 'Type of string', 'qode-quick-view-for-woocommerce' ),
				),
			),
		);

		return $routes;
	}

	add_filter( 'qode_quick_view_for_woocommerce_filter_rest_api_routes', 'qode_quick_view_for_woocommerce_add_rest_api_route_quick_view_button_block' );
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_render_quick_view_button_item_callback' ) ) {
	/**
	 * Function that render Quick View Button element
	 *
	 * @return void
	 */
	function qode_quick_view_for_woocommerce_render_quick_view_button_item_callback( $response ) {

		if ( ! isset( $response ) || empty( $response->get_body() ) ) {
			qode_quick_view_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-quick-view-for-woocommerce' ) );
		} else {
			$response_data = json_decode( $response->get_body() );
			$item_id       = isset( $response_data->item_id ) ? absint( $response_data->item_id ) : '';
			$button_type   = $response_data->button_type ?? 'icon-with-text';

			if ( class_exists( 'Qode_Quick_View_For_WooCommerce_Quick_View_Button_Shortcode' ) ) {
				ob_start();

				// Set additional hook to initialize default premium hooks for this module.
				do_action( 'qode_quick_view_for_woocommerce_action_render_quick_view_button_block' );

				$shortcode_atts = array(
					'item_id'     => $item_id,
					'button_type' => $button_type,
				);

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo qode_quick_view_for_woocommerce_framework_wp_kses_html( 'html', Qode_Quick_View_For_WooCommerce_Quick_View_Button_Shortcode::call_shortcode( $shortcode_atts ) );

				$html = ob_get_clean();

				qode_quick_view_for_woocommerce_get_ajax_status( 'success', esc_html__( 'Item is successfully returned.', 'qode-quick-view-for-woocommerce' ), $html );
			} else {
				qode_quick_view_for_woocommerce_get_ajax_status( 'error', esc_html__( 'Shortcode Quick view Button not exist.', 'qode-quick-view-for-woocommerce' ) );
			}
		}
		die();
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
}
