<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_get_quick_view_button_label' ) ) {
	/**
	 * Function that return quick view item label
	 *
	 * @param array $atts - Shortcode options
	 *
	 * @return string
	 */
	function qode_quick_view_for_woocommerce_get_quick_view_button_label( $atts ) {
		$button_label_option = qode_quick_view_for_woocommerce_get_option_value( 'admin', 'qode_quick_view_for_woocommerce_button_label' );
		$button_label        = ! empty( $button_label_option ) ? $button_label_option : esc_html__( 'Quick View', 'qode-quick-view-for-woocommerce' );

		return apply_filters( 'qode_quick_view_for_woocommerce_filter_quick_view_button_label', $button_label, $atts['item_id'] );
	}
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_add_rest_api_quick_view_button_global_variables' ) ) {
	/**
	 * Extend main rest api variables with new case
	 *
	 * @param array $global_par - list of variables
	 * @param string $namespace_par - rest namespace url
	 *
	 * @return array
	 */
	function qode_quick_view_for_woocommerce_add_rest_api_quick_view_button_global_variables( $global_par, $namespace_par ) {
		$global_par['quickViewRestRouteName'] = 'quick-view';
		$global_par['quickViewRestRoute']     = $namespace_par . '/' . $global_par['quickViewRestRouteName'];

		return $global_par;
	}

	add_filter( 'qode_quick_view_for_woocommerce_filter_rest_api_global_variables', 'qode_quick_view_for_woocommerce_add_rest_api_quick_view_button_global_variables', 10, 2 );
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_add_rest_api_quick_view_button_route' ) ) {
	/**
	 * Extend main rest api routes with new case
	 *
	 * @param array $routes - list of rest routes
	 *
	 * @return array
	 */
	function qode_quick_view_for_woocommerce_add_rest_api_quick_view_button_route( $routes ) {
		$routes['quick-view'] = array(
			'route'    => 'quick-view',
			'methods'  => WP_REST_Server::READABLE,
			'callback' => 'qode_quick_view_for_woocommerce_button_callback',
			'args'     => array(
				'item_id'        => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return is_int( $param ) ? $param : absint( $param );
					},
					'description'       => esc_html__( 'ID is int value', 'qode-quick-view-for-woocommerce' ),
				),
				'security_token' => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return esc_attr( $param );
					},
				),
			),
		);

		return $routes;
	}

	add_filter( 'qode_quick_view_for_woocommerce_filter_rest_api_routes', 'qode_quick_view_for_woocommerce_add_rest_api_quick_view_button_route' );
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_button_callback' ) ) {
	/**
	 * Function that add item into quick view content
	 *
	 * @return void
	 */
	function qode_quick_view_for_woocommerce_button_callback() {
		if ( empty( $_GET ) || ! isset( $_GET['security_token'] ) ) {
			qode_quick_view_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-quick-view-for-woocommerce' ) );
		} else {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['security_token'] ) ), 'wp_rest' ) ) {
				qode_quick_view_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-quick-view-for-woocommerce' ) );
				wp_die( esc_html__( 'You are not authorized.', 'qode-quick-view-for-woocommerce' ) );
			}

			$error             = false;
			$response_message  = '';
			$item_id           = isset( $_GET['item_id'] ) ? absint( $_GET['item_id'] ) : '';
			$page_id           = isset( $_GET['page_id'] ) ? absint( $_GET['page_id'] ) : '';
			$prev_item_id      = isset( $_GET['prev_item_id'] ) ? absint( $_GET['prev_item_id'] ) : '';
			$next_item_id      = isset( $_GET['next_item_id'] ) ? absint( $_GET['next_item_id'] ) : '';
			$quick_view_type   = isset( $_GET['quick_view_type'] ) ? sanitize_text_field( wp_unslash( $_GET['quick_view_type'] ) ) : 'pop-up';
			$loop_params       = array( 'item_id' => $item_id );
			$navigation_params = array(
				'item_id'         => $item_id,
				'prev_item_id'    => $prev_item_id,
				'next_item_id'    => $next_item_id,
				'quick_view_type' => $quick_view_type,
			);

			if ( empty( $item_id ) ) {
				$error            = true;
				$response_message = esc_html__( 'Item ID is invalid.', 'qode-quick-view-for-woocommerce' );
			}

			if ( $error ) {
				qode_quick_view_for_woocommerce_get_ajax_status( 'error', $response_message );
			} else {
				ob_start();

				// Load quick view additional template parts and options.
				do_action( 'qode_quick_view_for_woocommerce_action_before_quick_view_templates_load', $quick_view_type, $item_id, $page_id );

				// Load quick view template parts.
				Qode_Quick_View_For_WooCommerce_Module::get_instance()->include_quick_view_templates();

				// Load quick view additional template parts and options.
				do_action( 'qode_quick_view_for_woocommerce_action_after_quick_view_templates_load', $quick_view_type );

				// Include product template.
				qode_quick_view_for_woocommerce_template_part( 'quick-view', 'templates/loop', '', $loop_params );

				// Include product navigation template.
				do_action( 'qode_quick_view_for_woocommerce_action_include_navigation', $navigation_params );

				$html         = ob_get_contents();
				$product_type = wc_get_product( $item_id )->get_type();

				ob_end_clean();

				$response_data = array(
					'html'            => $html,
					'product_type'    => $product_type,
					'quick_view_type' => $quick_view_type,
				);

				qode_quick_view_for_woocommerce_get_ajax_status( 'success', esc_html__( 'Item is added', 'qode-quick-view-for-woocommerce' ), $response_data );
			}
		}
		die();
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
}
