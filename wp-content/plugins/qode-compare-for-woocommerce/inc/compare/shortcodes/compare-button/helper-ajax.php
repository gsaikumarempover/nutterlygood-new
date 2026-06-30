<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_compare_for_woocommerce_add_rest_api_compare_global_variables' ) ) {
	/**
	 * Extend main rest api variables with new case
	 *
	 * @param array $global - list of variables
	 * @param string $namespace - rest namespace url
	 *
	 * @return array
	 */
	function qode_compare_for_woocommerce_add_rest_api_compare_global_variables( $global, $namespace ) {
		$global['compareRestRoute']      = $namespace . '/compare';
		$global['compareTableRestRoute'] = $namespace . '/compare-table';

		return $global;
	}

	add_filter( 'qode_compare_for_woocommerce_filter_rest_api_global_variables', 'qode_compare_for_woocommerce_add_rest_api_compare_global_variables', 10, 2 );
}

if ( ! function_exists( 'qode_compare_for_woocommerce_add_rest_api_compare_route' ) ) {
	/**
	 * Extend main rest api routes with new case
	 *
	 * @param array $routes - list of rest routes
	 *
	 * @return array
	 */
	function qode_compare_for_woocommerce_add_rest_api_compare_route( $routes ) {
		$routes['compare'] = array(
			'route'    => 'compare',
			'methods'  => WP_REST_Server::CREATABLE,
			'callback' => 'qode_compare_for_woocommerce_compare_callback',
			'args'     => array(
				'item_id'        => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return is_int( $param ) ? $param : (int) $param;
					},
					'description'       => esc_html__( 'ID is int value', 'qode-compare-for-woocommerce' ),
				),
				'action'         => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return esc_attr( $param );
					},
					'description'       => esc_html__( 'Type of action', 'qode-compare-for-woocommerce' ),
				),
				'security_token' => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return esc_attr( $param );
					},
				),
			),
		);

		$routes['compare-table'] = array(
			'route'    => 'compare-table',
			'methods'  => WP_REST_Server::CREATABLE,
			'callback' => 'qode_compare_for_woocommerce_compare_table_callback',
			'args'     => array(
				'item_ids'       => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return is_array( $param ) ? $param : (array) $param;
					},
					'description'       => esc_html__( 'Array of product IDs', 'qode-compare-for-woocommerce' ),
				),
				'action'         => array(
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return esc_attr( $param );
					},
					'description'       => esc_html__( 'Type of action', 'qode-compare-for-woocommerce' ),
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
					'description'       => esc_html__( 'Options data is array with all selected shortcode parameters value', 'qode-compare-for-woocommerce' ),
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

	add_filter( 'qode_compare_for_woocommerce_filter_rest_api_routes', 'qode_compare_for_woocommerce_add_rest_api_compare_route' );
}

if ( ! function_exists( 'qode_compare_for_woocommerce_compare_table_callback' ) ) {
	/**
	 * Function that update user comparison table items
	 *
	 * @return void
	 */
	function qode_compare_for_woocommerce_compare_table_callback() {

		if ( empty( $_POST ) || ! isset( $_POST['security_token'] ) ) {
			qode_compare_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-compare-for-woocommerce' ) );
		} else {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security_token'] ) ), 'wp_rest' ) ) {
				qode_compare_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-compare-for-woocommerce' ) );
			}
			$item_ids = array();
			if ( isset( $_POST['item_ids'] ) && is_array( $_POST['item_ids'] ) ) {
				$item_ids = isset( $_POST['item_ids'][0] ) && 'all' === $_POST['item_ids'][0] ? array_keys( qode_compare_for_woocommerce_get_comparison_items_by_table() ) : array_map( 'absint', $_POST['item_ids'] );
			}
			$action  = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
			$table   = isset( $_POST['table'] ) && ! empty( $_POST['table'] ) ? sanitize_text_field( wp_unslash( $_POST['table'] ) ) : false;
			$token   = isset( $_POST['token'] ) && ! empty( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : false;
			$options = isset( $_POST['options'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['options'] ) ), true ) : array();

			if ( empty( $action ) || ( empty( $item_ids ) && ! in_array( $action, array( 'add', 'update_fields' ), true ) ) ) {
				qode_compare_for_woocommerce_get_ajax_status( 'error', esc_html__( 'Item IDs or action are invalid.', 'qode-compare-for-woocommerce' ) );
			} else {
				// Update comparison items.
				if ( 'update_fields' !== $action ) {
					qode_compare_for_woocommerce_update_comparison_items( $item_ids, $action, $table, $token );
				}

				$comparison_items = qode_compare_for_woocommerce_get_comparison_items_by_table( $table );

				// Set additional hook before module for 3rd party elements.
				do_action( 'qode_compare_for_woocommerce_action_before_updating_compare_table' );

				$new_content       = '';
				$not_found_content = '';

				if ( apply_filters( 'qode_compare_for_woocommerce_filter_compare_table_response_data_content_condition', true ) ) {
					if ( ! empty( $comparison_items ) ) {
						ob_start();
						$items_template_atts = array(
							'items' => $comparison_items,
						);

						qode_compare_for_woocommerce_template_part( 'compare', '/templates/table-holder', '', $items_template_atts );
						$new_content = ob_get_clean();
					} else {
						ob_start();
						qode_compare_for_woocommerce_template_part( 'compare', 'templates/parts/not-found' );
						$not_found_content = ob_get_clean();
					}
				}

				$response_data = apply_filters(
					'qode_compare_for_woocommerce_filter_compare_table_response_data',
					array(
						'new_content'       => $new_content,
						'not_found_content' => $not_found_content,
						'items_count'       => is_array( $comparison_items ) ? count( $comparison_items ) : 0,
					),
					$comparison_items,
					$action,
					$options
				);

				$response_message = esc_html__( 'Item is successfully added into comparison table', 'qode-compare-for-woocommerce' );

				if ( 'remove' === $action ) {
					$response_message = esc_html( _n( 'Item is successfully removed from the comparison table', 'Items are successfully removed from the comparison table', count( $item_ids ), 'qode-compare-for-woocommerce' ) );
				}

				// Set additional hook before module for 3rd party elements.
				do_action( 'qode_compare_for_woocommerce_action_after_updating_compare_table' );

				qode_compare_for_woocommerce_get_ajax_status( 'success', apply_filters( 'qode_compare_for_woocommerce_filter_compare_table_response_message', $response_message, $action ), $response_data );
			}
		}
		die();
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_compare_callback' ) ) {
	/**
	 * Function that item to comparison
	 *
	 * @return void
	 */
	function qode_compare_for_woocommerce_compare_callback() {

		if ( empty( $_POST ) || ! isset( $_POST['security_token'] ) ) {
			qode_compare_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-compare-for-woocommerce' ) );
		} else {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security_token'] ) ), 'wp_rest' ) ) {
				qode_compare_for_woocommerce_get_ajax_status( 'error', esc_html__( 'You are not authorized.', 'qode-compare-for-woocommerce' ) );
			}
			$item_id = isset( $_POST['item_id'] ) ? absint( $_POST['item_id'] ) : 0;
			$action  = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
			$options = isset( $_POST['options'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['options'] ) ), true ) : array();
			$message = esc_html__( 'Item ID or action are invalid.', 'qode-compare-for-woocommerce' );
			$message = apply_filters( 'qode_compare_for_woocommerce_filter_compare_message', $message, $item_id );

			if ( empty( $item_id ) || empty( $action ) ) {
				qode_compare_for_woocommerce_get_ajax_status( 'error', $message );
			} else {
				$product_name = '<span class="qcfw--product-name">' . ucfirst( get_the_title( $item_id ) ) . '</span>';
				if ( 'view' === $action && qode_compare_for_woocommerce_check_is_compare_item_added( $item_id ) ) {
					// translators: %s - product name.
					$message = sprintf( __( '%s is already in comparison.', 'qode-compare-for-woocommerce' ), $product_name );
				} elseif ( apply_filters( 'qode_compare_for_woocommerce_filter_compare_add_condition', 'add' === $action, $action ) ) {
					// Update comparison items.
					qode_compare_for_woocommerce_update_comparison_items( array( $item_id ), $action );
					// translators: %s - product name.
					$message = sprintf( __( '%s is successfully added into comparison.', 'qode-compare-for-woocommerce' ), $product_name );
				}
				qode_compare_for_woocommerce_get_ajax_status(
					'success',
					$message,
					apply_filters( 'qode_compare_for_woocommerce_filter_compare_response_data', array(), $item_id, $message, $options )
				);
			}
		}
		die();
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
}
