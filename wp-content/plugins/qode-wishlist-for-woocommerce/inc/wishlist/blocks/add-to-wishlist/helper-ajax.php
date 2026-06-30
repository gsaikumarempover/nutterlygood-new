<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_add_rest_api_route_add_to_wishlist_block' ) ) {
	/**
	 * Extend main rest api routes with new case
	 *
	 * @param array $routes - list of rest routes
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_add_rest_api_route_add_to_wishlist_block( $routes ) {

		$routes['render-add-to-wishlist'] = array(
			'route'               => 'render-add-to-wishlist',
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'qode_wishlist_for_woocommerce_render_add_to_wishlist_item_callback',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
			'args'                => array(
				'item_id'         => array(
					'required'          => false,
					'validate_callback' => function ( $param ) {
						return is_int( $param ) ? $param : (int) $param;
					},
					'description'       => esc_html__( 'ID is int value', 'qode-wishlist-for-woocommerce' ),
				),
				'button_behavior' => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return esc_attr( $param );
					},
					'description'       => esc_html__( 'Type of string', 'qode-wishlist-for-woocommerce' ),
				),
				'button_type'     => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return esc_attr( $param );
					},
					'description'       => esc_html__( 'Type of string', 'qode-wishlist-for-woocommerce' ),
				),
				'show_count'      => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return esc_attr( $param );
					},
					'description'       => esc_html__( 'Type of string', 'qode-wishlist-for-woocommerce' ),
				),
			),
		);

		return $routes;
	}

	add_filter( 'qode_wishlist_for_woocommerce_filter_rest_api_routes', 'qode_wishlist_for_woocommerce_add_rest_api_route_add_to_wishlist_block' );
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_render_add_to_wishlist_item_callback' ) ) {
	/**
	 * Function that render Add to Wishlist element
	 *
	 * @return void
	 */
	function qode_wishlist_for_woocommerce_render_add_to_wishlist_item_callback( $response ) {

		if ( ! isset( $response ) || empty( $response->get_body() ) ) {
			qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-wishlist-for-woocommerce' ) );
		} else {
			$response_data   = json_decode( $response->get_body() );
			$item_id         = isset( $response_data->item_id ) ? intval( $response_data->item_id ) : '';
			$button_behavior = $response_data->button_behavior ?? '';
			$button_type     = $response_data->button_type ?? '';
			$show_count      = $response_data->show_count ?? '';

			if ( class_exists( 'Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Shortcode' ) ) {
				ob_start();

				// Set additional hook to initialize default premium hooks for this module.
				do_action( 'qode_wishlist_for_woocommerce_action_render_add_to_wishlist_block' );

				$shortcode_atts = array(
					'item_id'         => $item_id,
					'button_behavior' => $button_behavior,
					'button_type'     => $button_type,
					'show_count'      => $show_count,
					'prevent_click'   => 'yes',
				);

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Shortcode::call_shortcode( $shortcode_atts );

				$html = ob_get_clean();

				qode_wishlist_for_woocommerce_get_ajax_status( 'success', esc_html__( 'Item is successfully returned.', 'qode-wishlist-for-woocommerce' ), $html );
			} else {
				qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'Add to Wishlist shortcode does not exist.', 'qode-wishlist-for-woocommerce' ) );
			}
		}
		die();
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
}
