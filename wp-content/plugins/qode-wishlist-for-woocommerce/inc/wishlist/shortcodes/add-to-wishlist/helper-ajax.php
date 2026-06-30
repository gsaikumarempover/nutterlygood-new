<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_add_rest_api_add_to_wishlist_global_variables' ) ) {
	/**
	 * Extend main rest api variables with new case
	 *
	 * @param array $rest_global - list of variables
	 * @param string $rest_namespace - rest namespace url
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_add_rest_api_add_to_wishlist_global_variables( $rest_global, $rest_namespace ) {
		$rest_global['addToWishlistRestRoute'] = $rest_namespace . '/add-to-wishlist';

		return $rest_global;
	}

	add_filter( 'qode_wishlist_for_woocommerce_filter_rest_api_global_variables', 'qode_wishlist_for_woocommerce_add_rest_api_add_to_wishlist_global_variables', 10, 2 );
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_add_rest_api_add_to_wishlist_route' ) ) {
	/**
	 * Extend main rest api routes with new case
	 *
	 * @param array $routes - list of rest routes
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_add_rest_api_add_to_wishlist_route( $routes ) {
		$routes['add-to-wishlist'] = array(
			'route'    => 'add-to-wishlist',
			'methods'  => WP_REST_Server::CREATABLE,
			'callback' => 'qode_wishlist_for_woocommerce_add_to_wishlist_item_callback',
			'args'     => array(
				'item_id'        => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return is_int( $param ) ? $param : (int) $param;
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

	add_filter( 'qode_wishlist_for_woocommerce_filter_rest_api_routes', 'qode_wishlist_for_woocommerce_add_rest_api_add_to_wishlist_route' );
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_add_to_wishlist_item_callback' ) ) {
	/**
	 * Function that add item into wishlist
	 *
	 * @return void
	 */
	function qode_wishlist_for_woocommerce_add_to_wishlist_item_callback() {

		if ( empty( $_POST ) || ! isset( $_POST['security_token'] ) ) {
			qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-wishlist-for-woocommerce' ) );
		} else {

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! wp_verify_nonce( wp_unslash( $_POST['security_token'] ), 'wp_rest' ) ) {
				qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-wishlist-for-woocommerce' ) );
				wp_die( esc_html__( 'You are not authorized.', 'qode-wishlist-for-woocommerce' ) );
			}

			$item_id = isset( $_POST['item_id'] ) ? absint( $_POST['item_id'] ) : 0;
			$action  = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
			$options = isset( $_POST['options'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['options'] ) ), true ) : array();

			if ( empty( $item_id ) || empty( $action ) ) {
				qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'Item ID or action are invalid.', 'qode-wishlist-for-woocommerce' ) );
			} else {
				$behavior      = '';
				$response_data = array();

				if ( ! empty( $options ) ) {

					if ( isset( $options['require_login'] ) && $options['require_login'] ) {
						qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized to add item.', 'qode-wishlist-for-woocommerce' ) );
					}

					if ( ! isset( $options['button_behavior'] ) ) {
						qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'Button behavior option is invalid.', 'qode-wishlist-for-woocommerce' ) );
					}

					$behavior = $options['button_behavior'];
				}

				if ( 'add' === $action && qode_wishlist_for_woocommerce_check_is_wishlist_item_added( $item_id ) ) {
					ob_start();

					qode_wishlist_for_woocommerce_template_part( 'wishlist', 'templates/popup-added' );

					$popup_html = ob_get_clean();

					$response_data['popup_html'] = $popup_html;

					qode_wishlist_for_woocommerce_get_ajax_status( 'success', esc_html__( 'The product is already in your wishlist!', 'qode-wishlist-for-woocommerce' ), $response_data );
				} else {
					// Update wishlist items.
					qode_wishlist_for_woocommerce_update_wishlist_items( array( $item_id ), $action );

					ob_start();

					$button_params = array_merge(
						array(
							'item_id' => $item_id,
						),
						$options
					);

					qode_wishlist_for_woocommerce_template_part( 'wishlist/shortcodes/add-to-wishlist', 'templates/parts/content', '', $button_params );

					$button_html = ob_get_clean();

					$response_data = array(
						'button_html' => $button_html,
					);

					if ( 'add' === $action ) {
						ob_start();

						qode_wishlist_for_woocommerce_template_part( 'wishlist', 'templates/popup', '', array( 'item_id' => $item_id ) );

						$popup_html = ob_get_clean();

						$response_data['popup_html'] = $popup_html;

						if ( 'view' === $behavior ) {
							$response_data['wishlist_page_url'] = qode_wishlist_for_woocommerce_get_wishlist_page_url();
						}

						qode_wishlist_for_woocommerce_get_ajax_status( 'success', esc_html__( 'Item is successfully added into wishlist.', 'qode-wishlist-for-woocommerce' ), $response_data );
					} elseif ( 'remove' === $action ) {
						ob_start();

						qode_wishlist_for_woocommerce_template_part( 'wishlist', 'templates/popup', 'removed' );

						$popup_html = ob_get_clean();

						$response_data['popup_html'] = $popup_html;

						qode_wishlist_for_woocommerce_get_ajax_status( 'success', esc_html__( 'Item is successfully removed from wishlist.', 'qode-wishlist-for-woocommerce' ), $response_data );
					} elseif ( 'update_variation' === $action ) {
						$is_added                       = qode_wishlist_for_woocommerce_check_is_wishlist_item_added( $item_id );
						$response_data['is_item_added'] = $is_added;

						if ( 'view' === $behavior && $is_added ) {
							$response_data['wishlist_page_url'] = qode_wishlist_for_woocommerce_get_wishlist_page_url();
						}

						qode_wishlist_for_woocommerce_get_ajax_status( 'success', esc_html__( 'Item is successfully updated.', 'qode-wishlist-for-woocommerce' ), $response_data );
					} else {
						qode_wishlist_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-wishlist-for-woocommerce' ) );
					}
				}
			}
		}
		die();
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
}
