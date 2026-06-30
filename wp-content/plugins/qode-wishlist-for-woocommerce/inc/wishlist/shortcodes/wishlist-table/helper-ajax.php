<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_add_rest_api_wishlist_table_global_variables' ) ) {
	/**
	 * Extend main rest api variables with new case
	 *
	 * @param array $rest_global - list of variables
	 * @param string $rest_namespace - rest namespace url
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_add_rest_api_wishlist_table_global_variables( $rest_global, $rest_namespace ) {
		$rest_global['wishlistTableRestRoute'] = $rest_namespace . '/wishlist-table';

		return $rest_global;
	}

	add_filter( 'qode_wishlist_for_woocommerce_filter_rest_api_global_variables', 'qode_wishlist_for_woocommerce_add_rest_api_wishlist_table_global_variables', 10, 2 );
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_add_rest_api_wishlist_table_route' ) ) {
	/**
	 * Extend main rest api routes with new case
	 *
	 * @param array $routes - list of rest routes
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_add_rest_api_wishlist_table_route( $routes ) {
		$routes['wishlist-table'] = array(
			'route'    => 'wishlist-table',
			'methods'  => WP_REST_Server::CREATABLE,
			'callback' => 'qode_wishlist_for_woocommerce_wishlist_table_item_callback',
			'args'     => array(
				'item_ids'       => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return is_array( $param ) ? $param : (array) $param;
					},
					'description'       => esc_html__( 'ID is int value', 'qode-wishlist-for-woocommerce' ),
				),
				'action'         => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return esc_attr( $param );
					},
					'description'       => esc_html__( 'Type of action', 'qode-wishlist-for-woocommerce' ),
				),
				'table'          => array(
					'required'          => false,
					'validate_callback' => function ( $param ) {
						return esc_attr( $param );
					},
				),
				'token'          => array(
					'required'          => false,
					'validate_callback' => function ( $param ) {
						return esc_attr( $param );
					},
				),
				'options'        => array(
					'required'          => false,
					'validate_callback' => function ( $param ) {
						// Simple solution for validation can be 'is_array' value instead of callback function.
						return is_array( $param ) ? $param : (array) $param;
					},
					'description'       => esc_html__( 'Options data is array with all selected shortcode parameters value', 'qode-wishlist-for-woocommerce' ),
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

	add_filter( 'qode_wishlist_for_woocommerce_filter_rest_api_routes', 'qode_wishlist_for_woocommerce_add_rest_api_wishlist_table_route' );
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_wishlist_table_item_callback' ) ) {
	/**
	 * Function that update user wishlist table items
	 *
	 * @return void
	 */
	function qode_wishlist_for_woocommerce_wishlist_table_item_callback() {

		if ( empty( $_POST ) || ! isset( $_POST['security_token'] ) ) {
			qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-wishlist-for-woocommerce' ) );
		} else {

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! wp_verify_nonce( wp_unslash( $_POST['security_token'] ), 'wp_rest' ) ) {
				qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-wishlist-for-woocommerce' ) );
				wp_die( esc_html__( 'You are not authorized.', 'qode-wishlist-for-woocommerce' ) );
			}

			$item_ids = isset( $_POST['item_ids'] ) && is_array( $_POST['item_ids'] ) ? array_map( 'absint', $_POST['item_ids'] ) : array();
			$action   = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
			$table    = isset( $_POST['table'] ) && ! empty( $_POST['table'] ) ? sanitize_text_field( wp_unslash( $_POST['table'] ) ) : false;
			$token    = isset( $_POST['token'] ) && ! empty( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : false;
			$options  = isset( $_POST['options'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['options'] ) ), true ) : array();

			if ( empty( $item_ids ) || empty( $action ) ) {
				qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'Item IDs or action are invalid.', 'qode-wishlist-for-woocommerce' ) );
			} else {

				if ( ! empty( $token ) ) {
					$token_wishlist_items = qode_wishlist_for_woocommerce_get_wishlist_items_by_token( $token );

					if ( ! isset( $token_wishlist_items['user'] ) || ! in_array( $token_wishlist_items['user'], array( 'guest', get_current_user_id() ), true ) ) {
						qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-wishlist-for-woocommerce' ) );
						wp_die( esc_html__( 'You are not authorized.', 'qode-wishlist-for-woocommerce' ) );
					}
				}

				// Update wishlist items.
				qode_wishlist_for_woocommerce_update_wishlist_items( $item_ids, $action, $table, $token );

				if ( 'add' === $action && ! empty( $options ) ) {
					$options['current'] = 1;
				}

				$wishlist_items     = qode_wishlist_for_woocommerce_get_wishlist_items_by_table( $table );
				$new_shortcode_atts = qode_wishlist_for_woocommerce_get_wishlist_table_shortcode_atts( $wishlist_items, $options );

				// Set additional hook before module for 3rd party elements.
				do_action( 'qode_wishlist_for_woocommerce_action_before_updating_wishlist_table' );

				$new_content              = '';
				$new_pagination_content   = '';
				$new_total_amount_content = '';
				$not_found_content        = '';
				if ( ! empty( $wishlist_items ) ) {
					// Sort wishlist items by forward criteria.
					if ( isset( $new_shortcode_atts['orderby'] ) ) {
						$wishlist_items = qode_wishlist_for_woocommerce_get_sorted_wishlist_items( $wishlist_items, $new_shortcode_atts['orderby'] );
					}

					ob_start();

					$all_wishlist_items = $wishlist_items;
					$wishlist_items     = qode_wishlist_for_woocommerce_get_wishlist_table_items_per_page( $wishlist_items, $new_shortcode_atts );

					$items_template_atts = array_merge(
						array( 'items' => $wishlist_items ),
						$new_shortcode_atts
					);

					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo apply_filters(
						'qode_wishlist_for_woocommerce_filter_wishlist_table_items',
						qode_wishlist_for_woocommerce_get_template_part( 'wishlist/shortcodes/wishlist-table', 'templates/items', '', $items_template_atts ),
						$items_template_atts
					);

					$new_content = ob_get_clean();

					ob_start();

					// Hook to render total amount template part from premium plugin.
					do_action( 'qode_wishlist_for_woocommerce_action_updating_wishlist_table_total_amount', $all_wishlist_items );

					$new_total_amount_content = ob_get_clean();

					ob_start();

					// Hook to render pagination template part from premium plugin.
					do_action( 'qode_wishlist_for_woocommerce_action_updating_wishlist_table_pagination', $new_shortcode_atts );

					$new_pagination_content = ob_get_clean();
				} else {
					ob_start();

					qode_wishlist_for_woocommerce_template_part( 'wishlist/shortcodes/wishlist-table', 'templates/not-found', '', $new_shortcode_atts );

					$not_found_content = ob_get_clean();
				}

				$response_data = array(
					'new_content'              => $new_content,
					'new_shortcode_atts'       => $new_shortcode_atts,
					'new_pagination_content'   => $new_pagination_content,
					'new_total_amount_content' => $new_total_amount_content,
					'not_found_content'        => $not_found_content,
				);

				$response_message = esc_html__( 'Item is successfully removed from the wishlist table', 'qode-wishlist-for-woocommerce' );
				if ( 'add' === $action ) {
					$response_message = esc_html__( 'Item is successfully added into wishlist table', 'qode-wishlist-for-woocommerce' );
				}

				// Set additional hook before module for 3rd party elements.
				do_action( 'qode_wishlist_for_woocommerce_action_after_updating_wishlist_table' );

				qode_wishlist_for_woocommerce_get_ajax_status( 'success', $response_message, $response_data );
			}
		}
		die();
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
}
