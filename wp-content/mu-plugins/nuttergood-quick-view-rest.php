<?php
/**
 * Stabilize QODE Quick View REST responses on shared hosting (InfinityFree, etc.).
 *
 * @package NutterlyGood
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'nuttergood_quick_view_is_rest_request' ) ) {
	/**
	 * Whether the current request targets the QODE quick-view REST route.
	 */
	function nuttergood_quick_view_is_rest_request() {
		if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
			return false;
		}

		$route = isset( $GLOBALS['wp']->query_vars['rest_route'] )
			? (string) $GLOBALS['wp']->query_vars['rest_route']
			: '';

		if ( '' === $route && ! empty( $_SERVER['REQUEST_URI'] ) ) {
			$route = (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH );
			$route = preg_replace( '#^.*?/wp-json#', '', $route );
		}

		return false !== strpos( $route, '/qode-quick-view-for-woocommerce/v1/quick-view' );
	}
}

if ( ! function_exists( 'nuttergood_quick_view_prepare_wc' ) ) {
	/**
	 * Bootstrap WooCommerce cart/session for REST quick-view renders.
	 */
	function nuttergood_quick_view_prepare_wc() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		if ( function_exists( 'wc_load_cart' ) ) {
			wc_load_cart();
		}

		if ( WC()->session && ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}
	}
}

if ( ! function_exists( 'nuttergood_quick_view_rest_prepare' ) ) {
	/**
	 * @param WP_REST_Response|WP_HTTP_Response|WP_Error|mixed $response Response.
	 * @param array                                            $handler  Route handler.
	 * @param WP_REST_Request                                  $request  Request.
	 *
	 * @return mixed
	 */
	function nuttergood_quick_view_rest_prepare( $response, $handler, $request ) {
		if ( ! $request instanceof WP_REST_Request ) {
			return $response;
		}

		$route = (string) $request->get_route();
		if ( false === strpos( $route, '/qode-quick-view-for-woocommerce/v1/quick-view' ) ) {
			return $response;
		}

		@ini_set( 'memory_limit', '256M' );
		@set_time_limit( 90 );
		nuttergood_quick_view_prepare_wc();

		return $response;
	}
	add_filter( 'rest_request_before_callbacks', 'nuttergood_quick_view_rest_prepare', 5, 3 );
}

if ( ! function_exists( 'nuttergood_quick_view_replace_rest_callback' ) ) {
	/**
	 * Use a safer REST callback (null-safe product type + WC bootstrap).
	 *
	 * @param array<string, mixed> $routes Plugin REST routes.
	 *
	 * @return array<string, mixed>
	 */
	function nuttergood_quick_view_replace_rest_callback( $routes ) {
		if ( isset( $routes['quick-view'] ) ) {
			$routes['quick-view']['callback'] = 'nuttergood_quick_view_rest_callback';
		}

		return $routes;
	}
	add_filter( 'qode_quick_view_for_woocommerce_filter_rest_api_routes', 'nuttergood_quick_view_replace_rest_callback', 100 );
}

if ( ! function_exists( 'nuttergood_quick_view_rest_callback' ) ) {
	/**
	 * Safer clone of the plugin quick-view REST handler.
	 */
	function nuttergood_quick_view_rest_callback() {
		if ( empty( $_GET ) || ! isset( $_GET['security_token'] ) ) {
			nuttergood_quick_view_send_status( 'error', 'You are not authorized.' );
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['security_token'] ) ), 'wp_rest' ) ) {
			nuttergood_quick_view_send_status( 'error', 'You are not authorized.' );
		}

		nuttergood_quick_view_prepare_wc();

		$item_id         = isset( $_GET['item_id'] ) ? absint( $_GET['item_id'] ) : 0;
		$page_id         = isset( $_GET['page_id'] ) ? absint( $_GET['page_id'] ) : 0;
		$prev_item_id    = isset( $_GET['prev_item_id'] ) ? absint( $_GET['prev_item_id'] ) : 0;
		$next_item_id    = isset( $_GET['next_item_id'] ) ? absint( $_GET['next_item_id'] ) : 0;
		$quick_view_type = isset( $_GET['quick_view_type'] ) ? sanitize_text_field( wp_unslash( $_GET['quick_view_type'] ) ) : 'pop-up';

		if ( empty( $item_id ) ) {
			nuttergood_quick_view_send_status( 'error', 'Item ID is invalid.' );
		}

		$product = wc_get_product( $item_id );
		if ( ! $product ) {
			nuttergood_quick_view_send_status( 'error', 'Product not found.' );
		}

		$loop_params       = array( 'item_id' => $item_id );
		$navigation_params = array(
			'item_id'         => $item_id,
			'prev_item_id'    => $prev_item_id,
			'next_item_id'    => $next_item_id,
			'quick_view_type' => $quick_view_type,
		);

		ob_start();

		do_action( 'qode_quick_view_for_woocommerce_action_before_quick_view_templates_load', $quick_view_type, $item_id, $page_id );

		if ( class_exists( 'Qode_Quick_View_For_WooCommerce_Module' ) ) {
			Qode_Quick_View_For_WooCommerce_Module::get_instance()->include_quick_view_templates();
		}

		do_action( 'qode_quick_view_for_woocommerce_action_after_quick_view_templates_load', $quick_view_type );

		if ( function_exists( 'qode_quick_view_for_woocommerce_template_part' ) ) {
			qode_quick_view_for_woocommerce_template_part( 'quick-view', 'templates/loop', '', $loop_params );
		}

		do_action( 'qode_quick_view_for_woocommerce_action_include_navigation', $navigation_params );

		$html         = (string) ob_get_clean();
		$product_type = $product->get_type();

		nuttergood_quick_view_send_status(
			'success',
			'Item is added',
			array(
				'html'            => $html,
				'product_type'    => $product_type,
				'quick_view_type' => $quick_view_type,
			)
		);
	}
}

if ( ! function_exists( 'nuttergood_quick_view_send_status' ) ) {
	/**
	 * @param string               $status  success|error.
	 * @param string               $message Message.
	 * @param array<string, mixed> $data    Optional payload.
	 */
	function nuttergood_quick_view_send_status( $status, $message, $data = null ) {
		if ( function_exists( 'qode_quick_view_for_woocommerce_get_ajax_status' ) ) {
			qode_quick_view_for_woocommerce_get_ajax_status( $status, $message, $data );
		}

		wp_send_json(
			array(
				'status'  => $status,
				'message' => $message,
				'data'    => $data,
			)
		);
	}
}
