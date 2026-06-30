<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_table_items' ) ) {
	/**
	 * Function that return wishlist table items
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_table_items( $params ) {
		$table_items = apply_filters(
			'qode_wishlist_for_woocommerce_filter_wishlist_table_items_args',
			array(
				'remove'       => array(
					'template' => 'remove',
					'label'    => '',
				),
				'thumbnail'    => array(
					'template' => 'thumbnail',
					'label'    => '',
				),
				'name'         => array(
					'template' => array( 'name' ),
					'label'    => esc_html__( 'Product name', 'qode-wishlist-for-woocommerce' ),
				),
				'price'        => array(
					'template' => 'price',
					'label'    => esc_html__( 'Price', 'qode-wishlist-for-woocommerce' ),
				),
				'stock-status' => array(
					'template' => 'stock-status',
					'label'    => esc_html__( 'Stock status', 'qode-wishlist-for-woocommerce' ),
				),
				'add-to-cart'  => array(
					'template' => array( 'add-to-cart' ),
					'label'    => '',
				),
			),
			$params
		);

		if ( 'no' === $params['enable_remove_item'] && isset( $table_items['remove'] ) ) {
			unset( $table_items['remove'] );
		}

		if ( 'no' === $params['enable_add_to_cart'] && isset( $table_items['add-to-cart'] ) ) {
			unset( $table_items['add-to-cart'] );
		}

		return $table_items;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_extend_wishlist_table_items' ) ) {
	/**
	 * Extend main rest api variables with new case
	 *
	 * @param array $table_items
	 * @param array $params
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_extend_wishlist_table_items( $table_items, $params ) {
		$table_items_option  = (array) qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_table_items' );
		$default_table_items = array(
			'name',
			'thumbnail',
		);

		$table_items_option = array_merge( $default_table_items, $table_items_option );

		if ( is_array( $table_items ) && ! empty( $table_items_option ) ) {
			foreach ( $table_items as $table_item_key => $table_item_value ) {

				if ( ! in_array( $table_item_key, $table_items_option, true ) ) {
					unset( $table_items[ $table_item_key ] );
				}
			}

			if ( 'gallery' === $params['layout'] ) {
				$extend_items = array(
					'category'   => array(
						'action'      => 'array_unshift',
						'initial_key' => 'name',
					),
					'variations' => array(
						'action'      => 'array_push',
						'initial_key' => isset( $table_items['price'] ) ? 'price' : 'name',
					),
					'date-added' => array(
						'action'      => 'array_unshift',
						'initial_key' => 'add-to-cart',
					),
				);
			} else {
				$extend_items = array(
					'category'   => array(
						'action'      => 'array_unshift',
						'initial_key' => 'name',
					),
					'variations' => array(
						'action'      => 'array_push',
						'initial_key' => 'name',
					),
					'date-added' => array(
						'action'      => 'array_push',
						'initial_key' => 'add-to-cart',
					),
				);
			}

			foreach ( $extend_items as $extend_item_key => $extend_item_value ) {
				if ( in_array( $extend_item_key, $table_items_option, true ) && isset( $table_items[ $extend_item_value['initial_key'] ] ) ) {
					if ( ! is_array( $table_items[ $extend_item_value['initial_key'] ]['template'] ) ) {
						$table_items[ $extend_item_value['initial_key'] ]['template'] = array( $table_items[ $extend_item_value['initial_key'] ]['template'] );
					}

					switch ( $extend_item_value['action'] ) {
						case 'array_push':
							array_push( $table_items[ $extend_item_value['initial_key'] ]['template'], $extend_item_key );
							break;
						case 'array_unshift':
							array_unshift( $table_items[ $extend_item_value['initial_key'] ]['template'], $extend_item_key );
							break;
					}
				}
			}
		}

		// If its shared wishlist page then unset remove button.
		if ( qode_wishlist_for_woocommerce_get_http_request_args( true ) && isset( $table_items['remove'] ) ) {
			unset( $table_items['remove'] );
		}

		return $table_items;
	}

	add_filter( 'qode_wishlist_for_woocommerce_filter_wishlist_table_items_args', 'qode_wishlist_for_woocommerce_extend_wishlist_table_items', 10, 2 );
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_table_items_per_page' ) ) {
	/**
	 * Function that return wishlist table items per page
	 *
	 * @param array $items - list of wishlist items
	 * @param array $options - shortcode pagination attributes
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_table_items_per_page( $items, $options ) {

		if ( ! empty( $options ) && ! empty( $items ) ) {
			$current_page = (int) $options['current'];

			if ( $current_page < 1 ) {
				$current_page = 1;
			}

			$current_page_offset = ( $current_page - 1 ) * $options['products_per_page'];

			if ( count( $items ) <= $current_page_offset ) {

				if ( $current_page > 1 ) {
					$current_page_offset = ( $current_page - 2 ) * $options['products_per_page'];
				} else {
					$current_page_offset = 0;
				}
			}

			$items = array_slice( $items, $current_page_offset, $options['products_per_page'] );
		}

		return $items;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_table_shortcode_atts' ) ) {
	/**
	 * Function that return wishlist table pagination attributes
	 *
	 * @param array $items - list of wishlist items
	 * @param array $atts - shortcode attributes
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_table_shortcode_atts( $items, $atts ) {
		$shortcode_atts = array();

		if ( ! empty( $atts ) && is_array( $items ) ) {
			$shortcode_atts['layout']             = esc_attr( $atts['layout'] );
			$shortcode_atts['table_name']         = esc_attr( $atts['table_name'] );
			$shortcode_atts['table_title_tag']    = esc_attr( $atts['table_title_tag'] );
			$shortcode_atts['products_per_page']  = (int) $atts['products_per_page'];
			$shortcode_atts['current']            = isset( $atts['current'] ) ? (int) $atts['current'] : 1;
			$shortcode_atts['total']              = (int) $atts['products_per_page'] > 0 ? ceil( count( $items ) / (int) $atts['products_per_page'] ) : 1;
			$shortcode_atts['items_count']        = count( $items );
			$shortcode_atts['enable_remove_item'] = esc_attr( $atts['enable_remove_item'] );
			$shortcode_atts['enable_add_to_cart'] = esc_attr( $atts['enable_add_to_cart'] );
			$shortcode_atts['orderby']            = $atts['orderby'] ?? '';
		}

		return $shortcode_atts;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_set_wishlist_table_styles' ) ) {
	/**
	 * Function that generates module inline styles
	 *
	 * @param string $style
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_set_wishlist_table_styles( $style ) {
		$styles = array();

		// Table area styles.
		$table_heading_color = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_table_items_heading_background_color' );
		$table_content_color = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_table_items_content_background_color' );
		$table_border_color  = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_table_items_border_color' );

		if ( ! empty( $table_heading_color ) ) {
			$styles['--qwfw-wt-heading-bg-color'] = $table_heading_color;
		}

		if ( ! empty( $table_content_color ) ) {
			$styles['--qwfw-wt-content-bg-color'] = $table_content_color;
		}

		if ( ! empty( $table_border_color ) ) {
			$styles['--qwfw-wt-border-color'] = $table_border_color;
		}

		// Table items typography styles.
		$table_font_size = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_table_items_font_size' );

		if ( ! empty( $table_font_size ) ) {
			if ( qode_wishlist_for_woocommerce_string_ends_with_allowed_units( $table_font_size ) ) {
				$styles['--qwfw-wt-items-font-size'] = $table_font_size;
			} else {
				$styles['--qwfw-wt-items-font-size'] = (int) $table_font_size . 'px';
			}
		}

		if ( ! empty( $styles ) ) {
			$style .= qode_wishlist_for_woocommerce_dynamic_style( '.qwfw-wishlist-table', $styles );
		}

		return $style;
	}

	add_filter( 'qode_wishlist_for_woocommerce_filter_add_inline_style', 'qode_wishlist_for_woocommerce_set_wishlist_table_styles' );
}
