<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}

if ( ! function_exists( 'qode_compare_for_woocommerce_check_is_compare_item_added' ) ) {
	/**
	 * Function that checks if an item is forwarded is already in the comparison
	 *
	 * @param int $item_id
	 * @param bool|string $table_name
	 *
	 * @return bool
	 */
	function qode_compare_for_woocommerce_check_is_compare_item_added( $item_id, $table_name = false ) {
		$comparison_items = qode_compare_for_woocommerce_get_comparison_items();

		// Check product ID and items.
		if ( empty( $item_id ) || empty( $comparison_items ) ) {
			return false;
		}

		$flag = false;
		foreach ( $comparison_items as $somparison_item_key => $comparison_item_value ) {
			// Skip if value is not array, because only items are array type.
			if ( ! is_array( $comparison_item_value ) ) {
				continue;
			}

			if ( ! empty( $table_name ) ) {

				if ( $somparison_item_key === $table_name && isset( $comparison_item_value[ qode_compare_for_woocommerce_get_original_product_id( $item_id ) ] ) ) {
					$flag = true;
				}
			} else {

				if ( isset( $comparison_item_value[ qode_compare_for_woocommerce_get_original_product_id( $item_id ) ] ) ) {
					$flag = true;
				}
			}
		}

		return $flag;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_update_comparison_items' ) ) {
	/**
	 * Function that update user comparison items
	 *
	 * @param array $item_ids
	 * @param string $action
	 * @param string|false $new_table_name
	 * @param string|bool $user_token
	 */
	function qode_compare_for_woocommerce_update_comparison_items( $item_ids, $action, $new_table_name = false, $user_token = false ) {
		$items            = qode_compare_for_woocommerce_get_comparison_items();
		$update_flag      = true;
		$table_name_title = esc_attr__( 'My Comparison', 'qode-compare-for-woocommerce' );
		$table_name       = 'default';
		$product_adding   = qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_product_add_position' );

		// Set new table name.
		if ( ! empty( $new_table_name ) && 'default' !== $new_table_name ) {
			$table_name_title = esc_html( esc_attr( $new_table_name ) );
			$table_name       = sanitize_title( $new_table_name );
		}

		// If user comparison table doesn't exist, create it.
		if ( ! isset( $items[ $table_name ] ) ) {
			$items[ $table_name ] = array(
				'table_title' => $table_name_title,
			);
		}

		// If user comparison table title doesn't exist, set it.
		if ( ! isset( $items[ $table_name ]['table_title'] ) ) {
			$items[ $table_name ]['table_title'] = $table_name_title;
		}

		if ( ! empty( $item_ids ) ) {
			foreach ( $item_ids as $item_id ) {
				$item_id = apply_filters( 'qode_compare_for_woocommerce_filter_item_id', qode_compare_for_woocommerce_get_original_product_id( $item_id ), $action, $table_name );

				if ( 'remove' === $action && isset( $items[ $table_name ][ $item_id ] ) ) {
					unset( $items[ $table_name ][ $item_id ] );
				} elseif ( 'add' === $action ) {
					$new_item = array(
						'product_id'    => $item_id,
						'product_title' => get_the_title( $item_id ),
						'date_added'    => date_i18n( get_option( 'date_format' ) ),
					);

					if ( isset( $product_adding ) && 'start' === $product_adding ) {
						$items[ $table_name ] = array( $item_id => $new_item ) + $items[ $table_name ];
					} else {
						$items[ $table_name ][ $item_id ] = $new_item;
					}
				}
			}

			if ( 'sort' === $action ) {
				$items[ $table_name ] = array_replace( array_flip( $item_ids ), $items[ $table_name ] );
			}
		}

		// Set token.
		$token = qode_compare_for_woocommerce_generate_token();
		if ( isset( $items['token'] ) ) {
			$token = $items['token'];
		} elseif ( ! empty( $user_token ) ) {
			$token = esc_attr( $user_token );
		}

		// Set unique comparison items token.
		$items['token'] = $token;

		// Set user.
		$items['user']      = 'guest';
		$items['user_name'] = esc_attr__( 'Guest', 'qode-compare-for-woocommerce' );
		if ( is_user_logged_in() ) {
			$current_user_id = get_current_user_id();
			$current_user    = get_user_by( 'id', $current_user_id );

			$items['user']      = $current_user_id;
			$items['user_name'] = esc_attr( $current_user->display_name );
		}

		// Set comparison created day.
		if ( ! isset( $items[ $table_name ]['date_created'] ) ) {
			$items[ $table_name ]['date_created'] = date_i18n( get_option( 'date_format' ) );
		}

		// Set comparison visibility.
		$table_visibility = $items[ $table_name ]['visibility'] ?? 'public';

		if ( ! isset( $items[ $table_name ]['visibility'] ) || $items[ $table_name ]['visibility'] !== $table_visibility ) {
			$items[ $table_name ]['visibility'] = sanitize_text_field( $table_visibility );
		}

		if ( $update_flag ) {
			qode_compare_for_woocommerce_update_comparison_items_db( $items, $token );
		}
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_update_comparison_items_db' ) ) {
	/**
	 * Function that update current user comparison items into database
	 */
	function qode_compare_for_woocommerce_update_comparison_items_db( $items, $token ) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();

			update_user_meta( $user_id, 'qode_compare_for_woocommerce_user_comparison_items', $items );
		} else {
			qode_compare_for_woocommerce_set_cookie( QODE_COMPARE_FOR_WOOCOMMERCE_GUESTS_ITEMS, $items );
		}

		// Save comparison items into corresponding token tables.
		$token_items = qode_compare_for_woocommerce_get_comparison_token_items();

		$token_items[ $token ] = $items;

		set_transient( QODE_COMPARE_FOR_WOOCOMMERCE_GUESTS_ITEMS, $token_items );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_original_product_id' ) ) {
	/**
	 * Get original product page ID if WPML plugin is installed
	 *
	 * @param int $item_id
	 *
	 * @return int
	 */
	function qode_compare_for_woocommerce_get_original_product_id( $item_id ) {

		if ( qode_compare_for_woocommerce_is_installed( 'wpml' ) ) {
			global $sitepress;

			if ( ! empty( $sitepress ) && ! empty( $sitepress->get_default_language() ) ) {
				$item_id = apply_filters( 'wpml_object_id', $item_id, 'product', true, $sitepress->get_default_language() );
			}
		}

		return (int) $item_id;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_comparison_items' ) ) {
	/**
	 * Function that return user comparison items
	 *
	 * @param bool $check_token_items
	 *
	 * @return array
	 */
	function qode_compare_for_woocommerce_get_comparison_items( $check_token_items = false ) {
		$comparison_items       = array();
		$cache_comparison_items = qode_compare_for_woocommerce_get_cookie( QODE_COMPARE_FOR_WOOCOMMERCE_GUESTS_ITEMS );
		$has_token              = qode_compare_for_woocommerce_get_http_request_args();

		if ( $check_token_items && $has_token ) {
			$token_items = qode_compare_for_woocommerce_get_comparison_items_by_token( $has_token );

			if ( ! empty( $token_items ) ) {
				$comparison_items = $token_items;
			}
		} elseif ( is_user_logged_in() ) {
			$user_id               = get_current_user_id();
			$user_comparison_items = get_user_meta( $user_id, 'qode_compare_for_woocommerce_user_comparison_items', true );

			if ( ! empty( $user_comparison_items ) ) {
				$comparison_items = apply_filters( 'qode_compare_for_woocommerce_filter_user_comparison_items', $user_comparison_items, $user_id );
			}
		} elseif ( ! empty( $cache_comparison_items ) ) {
			$comparison_items = $cache_comparison_items;
		}

		return $comparison_items;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_comparison_items_by_table' ) ) {
	/**
	 * Function that return comparison items by table name
	 *
	 * @param string|false $table_name
	 *
	 * @return array
	 */
	function qode_compare_for_woocommerce_get_comparison_items_by_table( $table_name = false, $token = '' ) {
		$table_items = qode_compare_for_woocommerce_get_comparison_items();
		$has_token   = ! empty( $token ) ? $token : qode_compare_for_woocommerce_get_http_request_args();

		if ( $has_token ) {
			$token_items = qode_compare_for_woocommerce_get_comparison_items_by_token( $has_token );

			if ( ! empty( $token_items ) ) {
				$table_items = $token_items;
			}
		}
		// Return all tables if table name is our predefined.
		if ( 'qcfw-all' !== $table_name ) {
			if ( ! empty( $table_name ) ) {
				$table_items = $table_items[ $table_name ] ?? array();
			} elseif ( isset( $table_items['default'] ) ) {
				$table_items = $table_items['default'];
			} else {
				$table_items = array();
			}
		}

		// Remove unnecessary items from the list in order to return only comparison item values.
		$table_items = qode_compare_for_woocommerce_get_cleaned_comparison_items( $table_items );

		return $table_items;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_cleaned_comparison_items' ) ) {
	/**
	 * Function that return clean comparison items list, remove unnecessary items from the list
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	function qode_compare_for_woocommerce_get_cleaned_comparison_items( $items ) {

		if ( ! empty( $items ) ) {
			foreach ( $items as $item_key => $item_value ) {

				if ( isset( $item_value['table_title'] ) ) {
					unset( $items[ $item_key ]['table_title'] );
				}

				if ( isset( $item_value['date_created'] ) ) {
					unset( $items[ $item_key ]['date_created'] );
				}

				if ( isset( $item_value['visibility'] ) ) {
					unset( $items[ $item_key ]['visibility'] );
				}

				if ( in_array( $item_key, array( 'table_title', 'date_created', 'visibility' ), true ) ) {
					unset( $items[ $item_key ] );
				}
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_cookie' ) ) {
	/**
	 * Function that retrieve the value of a cookie
	 *
	 * @param string $name cookie name
	 *
	 * @return mixed
	 */
	function qode_compare_for_woocommerce_get_cookie( $name ) {
		if ( isset( $_COOKIE[ $name ] ) ) {
			return json_decode( sanitize_text_field( wp_unslash( $_COOKIE[ $name ] ) ), true );
		}

		return array();
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_http_request_args' ) ) {
	/**
	 * Function that return comparison page args
	 *
	 * @param bool $check_user
	 * @param string $type - available values view|table
	 *
	 * @return string|bool
	 */
	function qode_compare_for_woocommerce_get_http_request_args( $check_user = false, $type = 'view' ) {
		$arg = false;

		if ( $check_user ) {
			// phpcs:ignore WordPress.Security.NonceVerification
			$user_items = isset( $_GET['view'] ) ? qode_compare_for_woocommerce_get_comparison_items_by_token( sanitize_text_field( wp_unslash( $_GET['view'] ) ) ) : array();
			$user_id    = is_user_logged_in() ? get_current_user_id() : 'guest';

			if ( ! empty( $user_items ) && isset( $user_items['user'] ) ) {
				$items_user_id = 'guest' === $user_id ? $user_items['user'] : (int) $user_items['user'];

				$arg = $user_id !== $items_user_id;
			}
		} elseif ( isset( $_GET[ $type ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			// phpcs:ignore WordPress.Security.NonceVerification
			$arg = sanitize_text_field( wp_unslash( $_GET[ $type ] ) );
		}

		return $arg;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_comparison_items_by_token' ) ) {
	/**
	 * Function that return specific user comparison items by token
	 *
	 * @param string $token
	 *
	 * @return array
	 */
	function qode_compare_for_woocommerce_get_comparison_items_by_token( $token ) {
		$items       = qode_compare_for_woocommerce_get_comparison_token_items();
		$table_items = array();

		if ( isset( $items[ $token ] ) && $items[ $token ]['token'] === $token ) {
			$table_items = $items[ $token ];
		}

		return $table_items;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_comparison_token_items' ) ) {
	/**
	 * Function that return transient comparison items
	 *
	 * @return array
	 */
	function qode_compare_for_woocommerce_get_comparison_token_items() {
		$items = get_transient( QODE_COMPARE_FOR_WOOCOMMERCE_GUESTS_ITEMS );

		return ! empty( $items ) ? (array) array_reverse( $items, true ) : array();
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_cookie_expiration' ) ) {
	/**
	 * Function that returns default expiration time for comparison cookie
	 *
	 * @return int
	 */
	function qode_compare_for_woocommerce_get_cookie_expiration() {
		// Default value 7d before clear it.
		return intval( apply_filters( 'qode_compare_for_woocommerce_filter_cookie_expiration', 604800 ) );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_set_cookie' ) ) {
	/**
	 * Set a cookie - wrapper for setcookie using WP constants.
	 *
	 * @param  string  $name   Name of the cookie being set.
	 * @param  string  $value  Value of the cookie.
	 * @param  integer $expire Expiry of the cookie.
	 * @param  bool    $secure Whether the cookie should be served only over https.
	 * @param  bool    $httponly Whether the cookie is only accessible over HTTP, not scripting languages like JavaScript. @since 3.6.0.
	 *
	 * @return bool
	 */
	function qode_compare_for_woocommerce_set_cookie( $name, $value = array(), $expire = null, $secure = false, $httponly = false ) {

		if ( ! apply_filters( 'qode_compare_for_woocommerce_filter_set_cookie', true ) || empty( $name ) ) {
			return false;
		}

		$expire = ! empty( $expire ) ? $expire : ( time() + qode_compare_for_woocommerce_get_cookie_expiration() );

		$value = wp_json_encode( stripslashes_deep( $value ) );

		$_COOKIE[ $name ] = $value;
		wc_setcookie( $name, $value, $expire, $secure, $httponly );

		return true;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_destroy_cookie' ) ) {
	/**
	 * Function that destroy a cookie.
	 *
	 * @param string $name cookie name
	 *
	 * @return void
	 */
	function qode_compare_for_woocommerce_destroy_cookie( $name ) {
		qode_compare_for_woocommerce_set_cookie( $name, array(), time() - 3600 );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_generate_token' ) ) {
	/**
	 * Function that generate random token for comparison items
	 *
	 * @return string
	 */
	function qode_compare_for_woocommerce_generate_token() {
		return bin2hex( openssl_random_pseudo_bytes( 8 ) );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_update_user_comparison_items' ) ) {
	/**
	 * Function that update current user comparison items with cached items
	 */
	function qode_compare_for_woocommerce_update_user_comparison_items() {
		$cache_items = qode_compare_for_woocommerce_get_cookie( QODE_COMPARE_FOR_WOOCOMMERCE_GUESTS_ITEMS );

		if ( is_user_logged_in() && ! empty( $cache_items ) ) {
			$user_id     = get_current_user_id();
			$items       = qode_compare_for_woocommerce_get_comparison_items();
			$token_items = qode_compare_for_woocommerce_get_comparison_token_items();
			$cache_token = '';

			foreach ( $cache_items as $cache_item_key => $cache_item_value ) {

				// Set cache token value.
				if ( 'token' === $cache_item_key ) {
					$cache_token = $cache_item_value;
				}

				// Skip if value is not array, because only items are array type.
				if ( ! is_array( $cache_item_value ) ) {
					continue;
				}

				$table_name = $cache_item_key;

				// If user comparison table doesn't exist, create it.
				if ( ! isset( $items[ $table_name ] ) ) {
					$items[ $table_name ] = array();
				}

				foreach ( $cache_item_value as $item_id => $item_value ) {

					// Skip if value is not array, because only items are array type.
					if ( ! is_array( $item_value ) ) {
						continue;
					}

					$items[ $table_name ][ $item_id ] = array(
						'product_id'    => $item_value['product_id'],
						'product_title' => get_the_title( $item_id ),
						'date_added'    => $item_value['date_added'],
					);
				}
			}

			// Update current user cached comparison items.
			update_user_meta( $user_id, 'qode_compare_for_woocommerce_user_comparison_items', $items );

			// Remove current user cached comparison items.
			qode_compare_for_woocommerce_destroy_cookie( QODE_COMPARE_FOR_WOOCOMMERCE_GUESTS_ITEMS );

			// Remove comparison items from corresponding token tables.
			if ( ! empty( $token_items ) && ! empty( $cache_token ) ) {
				foreach ( $token_items as $token_item_key => $token_item_value ) {

					if ( $cache_token === $token_item_key ) {
						unset( $token_items[ $token_item_key ] );
					}
				}

				set_transient( QODE_COMPARE_FOR_WOOCOMMERCE_GUESTS_ITEMS, $token_items );
			}
		}
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_comparison_table_fields' ) ) {
	/**
	 * Function that returns all comparison table fields array
	 *
	 * @return array
	 */
	function qode_compare_for_woocommerce_get_comparison_table_fields() {
		$attributes = apply_filters( 'qode_compare_for_woocommerce_filter_attribute_taxonomies', array() );

		$table_fields = apply_filters(
			'qode_compare_for_woocommerce_filter_comparison_table_fields',
			array_merge(
				array(
					'name'         => esc_html__( 'Title', 'qode-compare-for-woocommerce' ),
					'price'        => esc_html__( 'Price', 'qode-compare-for-woocommerce' ),
					'add-to-cart'  => esc_html__( 'Add to cart', 'qode-compare-for-woocommerce' ),
					'description'  => esc_html__( 'Description', 'qode-compare-for-woocommerce' ),
					'sku'          => esc_html__( 'SKU', 'qode-compare-for-woocommerce' ),
					'stock-status' => esc_html__( 'Availability', 'qode-compare-for-woocommerce' ),
					'weight'       => esc_html__( 'Weight', 'qode-compare-for-woocommerce' ),
					'dimension'    => esc_html__( 'Dimension', 'qode-compare-for-woocommerce' ),
				),
				$attributes
			)
		);

		return $table_fields;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_attribute_taxonomies' ) ) {
	/**
	 * Get attribute taxonomies
	 *
	 * @return array()
	 */
	function qode_compare_for_woocommerce_get_attribute_taxonomies() {
		$attributes           = array();
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $attribute ) {
				$tax = wc_attribute_taxonomy_name( $attribute->attribute_name );
				if ( taxonomy_exists( $tax ) ) {
					$attributes[ $tax ] = ucfirst( $attribute->attribute_name );
				}
			}
		}
		return $attributes;
	}
	add_filter( 'qode_compare_for_woocommerce_filter_attribute_taxonomies', 'qode_compare_for_woocommerce_get_attribute_taxonomies' );
}

if ( ! function_exists( 'qode_compare_for_woocommerce_check_is_comparison_item_added' ) ) {
	/**
	 * Function that checks if an item is forwarded is already in the comparison
	 *
	 * @param int $item_id
	 * @param bool|string $table_name
	 *
	 * @return bool
	 */
	function qode_compare_for_woocommerce_check_is_comparison_item_added( $item_id, $table_name = false ) {
		$comparison_items = qode_compare_for_woocommerce_get_comparison_items();

		// Check product ID and items.
		if ( empty( $item_id ) || empty( $comparison_items ) ) {
			return false;
		}

		$flag = false;
		foreach ( $comparison_items as $comparison_item_key => $comparison_item_value ) {
			// Skip if value is not array, because only items are array type.
			if ( ! is_array( $comparison_item_value ) ) {
				continue;
			}

			if ( ! empty( $table_name ) ) {

				if ( $comparison_item_key === $table_name && isset( $comparison_item_value[ qode_compare_for_woocommerce_get_original_product_id( $item_id ) ] ) ) {
					$flag = true;
				}
			} else {

				if ( isset( $comparison_item_value[ qode_compare_for_woocommerce_get_original_product_id( $item_id ) ] ) ) {
					$flag = true;
				}
			}
		}

		return $flag;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_product_description' ) ) {
	/**
	 * Function that returns product Description
	 *
	 * @param WC_Product $product
	 *
	 * @return string
	 */
	function qode_compare_for_woocommerce_get_product_description( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return '';
		}
		$description = 'yes' === qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_full_description' ) ? $product->get_description() : '';

		if ( empty( $description ) ) {
			$description = apply_filters( 'woocommerce_short_description', $product->get_short_description() );
		}

		return apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_description', $description, $product );
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_product_taxonomies' ) ) {
	/**
	 * Function that returns product Description
	 *
	 * @param int $product_id
	 * @param string $taxonomy_name
	 *
	 * @return string
	 */
	function qode_compare_for_woocommerce_get_product_taxonomies( $product_id, $taxonomy_name ) {
		$taxonomies = array();
		$terms      = get_the_terms( $product_id, $taxonomy_name );
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$term         = sanitize_term( $term, $taxonomy_name );
				$taxonomies[] = $term->name;
			}
		}
		$taxonomies = implode( ', ', $taxonomies );

		$taxonomies = empty( $taxonomies ) ? '/' : $taxonomies;

		return $taxonomies;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_comparison_table_meta' ) ) {
	/**
	 * Function that return user comparison table meta value
	 *
	 * @param string $table_name
	 * @param string $meta_key
	 * @param array $items
	 *
	 * @return string|array
	 */
	function qode_compare_for_woocommerce_get_comparison_table_meta( $table_name, $meta_key, $items = array() ) {
		$comparison_items = ! empty( $items ) ? $items : qode_compare_for_woocommerce_get_comparison_items();
		$return_value     = '';

		if ( ! empty( $comparison_items ) && ! empty( $table_name ) && ! empty( $meta_key ) ) {
			foreach ( $comparison_items as $comparison_item_key => $comparison_item_value ) {

				if ( $table_name === $comparison_item_key && isset( $comparison_item_value[ $meta_key ] ) ) {
					$return_value = $comparison_item_value[ $meta_key ];
					break;
				} elseif ( isset( $comparison_items[ $table_name ] ) && $meta_key === $comparison_item_key ) {
					$return_value = $comparison_item_value;
					break;
				}
			}
		}

		return $return_value;
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_remove_compare_item_db' ) ) {
	/**
	 * Function that remove forward compare item from database
	 *
	 * @param string $token
	 * @param string $table_name
	 */
	function qode_compare_for_woocommerce_remove_compare_item_db( $token, $table_name ) {

		// phpcs:ignore WordPress.WP.Capabilities.Unknown
		if ( is_user_logged_in() && current_user_can( 'manage_woocommerce' ) && ! empty( $token ) && ! empty( $table_name ) ) {
			$user = qode_compare_for_woocommerce_get_comparison_table_meta( $table_name, 'user', $token_items[ $token ] ?? array() );

			// Remove current user compare items.
			if ( is_numeric( $user ) ) {
				$user_items = get_user_meta( (int) $user, 'qode_compare_for_woocommerce_user_comparison_items', true );

				if ( ! empty( $user_items ) && isset( $user_items[ $table_name ] ) ) {
					unset( $user_items[ $table_name ] );

					update_user_meta( (int) $user, 'qode_compare_for_woocommerce_user_comparison_items', $user_items );
				}
			}

			// Remove compare items from corresponding token tables.
			$token_items = qode_compare_for_woocommerce_get_comparison_token_items();

			if ( isset( $token_items[ $token ][ $table_name ] ) ) {
				unset( $token_items[ $token ][ $table_name ] );

				set_transient( QODE_COMPARE_FOR_WOOCOMMERCE_GUESTS_ITEMS, $token_items );
			}
		}
	}
}
