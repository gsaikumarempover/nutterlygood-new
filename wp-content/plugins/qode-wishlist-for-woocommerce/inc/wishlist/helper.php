<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_original_product_id' ) ) {
	/**
	 * Get original product page ID if WPML plugin is installed
	 *
	 * @param int $item_id
	 *
	 * @return int
	 */
	function qode_wishlist_for_woocommerce_get_original_product_id( $item_id ) {

		if ( qode_wishlist_for_woocommerce_is_installed( 'wpml' ) ) {
			global $sitepress;

			if ( ! empty( $sitepress ) && ! empty( $sitepress->get_default_language() ) ) {
				$item_id = apply_filters( 'wpml_object_id', $item_id, 'product', true, $sitepress->get_default_language() );
			}
		}

		return (int) $item_id;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_page_id' ) ) {
	/**
	 * Get main Wishlist page ID
	 *
	 * @return int
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_page_id() {
		$wishlist_page_id = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_page_template' );

		return ! empty( $wishlist_page_id ) ? (int) apply_filters( 'wpml_object_id', $wishlist_page_id, 'page', true ) : 0;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_page_url' ) ) {
	/**
	 * Get main Wishlist page URL
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_page_url() {
		$wishlist_page_id  = qode_wishlist_for_woocommerce_get_wishlist_page_id();
		$wishlist_page_url = '';

		if ( ! empty( $wishlist_page_id ) ) {
			$wishlist_page_url = get_the_permalink( $wishlist_page_id );
		}

		return $wishlist_page_url;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_page_url_with_args' ) ) {
	/**
	 * Get main Wishlist page URL with args
	 *
	 * @param array $args
	 * @param bool $check_is_user_logged_in
	 * @param bool $prevent_default_table_check
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_page_url_with_args( $args = array(), $check_is_user_logged_in = false, $prevent_default_table_check = false ) {
		$wishlist_page_url = qode_wishlist_for_woocommerce_get_wishlist_page_url();
		$is_user_logged_in = is_user_logged_in() && $check_is_user_logged_in;

		if ( ! empty( $wishlist_page_url ) && ! empty( $args ) ) {
			$query_args = array();

			if ( isset( $args['view'] ) && ! empty( $args['view'] ) && ! $is_user_logged_in ) {
				$query_args['view'] = esc_attr( $args['view'] );
			}

			if ( isset( $args['table'] ) && ( $prevent_default_table_check || 'default' !== $args['table'] ) ) {
				$query_args['table'] = esc_attr( $args['table'] );
			}

			if ( isset( $args['user-action'] ) ) {
				$query_args['user-action'] = esc_attr( $args['user-action'] );
			}

			if ( ! empty( $query_args ) ) {
				$wishlist_page_url = add_query_arg( $query_args, qode_wishlist_for_woocommerce_get_wishlist_page_url() );
			}
		}

		return $wishlist_page_url;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_cookie_expiration' ) ) {
	/**
	 * Function that returns default expiration time for wishlist cookie
	 *
	 * @return int
	 */
	function qode_wishlist_for_woocommerce_get_cookie_expiration() {
		// Default value 7d before clear it.
		return intval( apply_filters( 'qode_wishlist_for_woocommerce_filter_cookie_expiration', 604800 ) );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_set_cookie' ) ) {
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
	function qode_wishlist_for_woocommerce_set_cookie( $name, $value = array(), $expire = null, $secure = false, $httponly = false ) {

		if ( ! apply_filters( 'qode_wishlist_for_woocommerce_filter_set_cookie', true ) || empty( $name ) ) {
			return false;
		}

		$expire = ! empty( $expire ) ? $expire : ( time() + qode_wishlist_for_woocommerce_get_cookie_expiration() );

		$value = wp_json_encode( stripslashes_deep( $value ) );

		$_COOKIE[ $name ] = $value;
		wc_setcookie( $name, $value, $expire, $secure, $httponly );

		return true;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_cookie' ) ) {
	/**
	 * Function that retrieve the value of a cookie
	 *
	 * @param string $name cookie name
	 *
	 * @return mixed
	 */
	function qode_wishlist_for_woocommerce_get_cookie( $name ) {
		if ( isset( $_COOKIE[ $name ] ) ) {
			return json_decode( sanitize_text_field( wp_unslash( $_COOKIE[ $name ] ) ), true );
		}

		return array();
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_destroy_cookie' ) ) {
	/**
	 * Function that destroy a cookie.
	 *
	 * @param string $name cookie name
	 *
	 * @return void
	 */
	function qode_wishlist_for_woocommerce_destroy_cookie( $name ) {
		qode_wishlist_for_woocommerce_set_cookie( $name, array(), time() - 3600 );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_generate_token' ) ) {
	/**
	 * Function that generate random token for wishlist items
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_generate_token() {
		return bin2hex( openssl_random_pseudo_bytes( 8 ) );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_http_request_args' ) ) {
	/**
	 * Function that return wishlist page args
	 *
	 * @param bool $check_user
	 * @param string $type - available values view|table
	 *
	 * @return string|bool
	 */
	function qode_wishlist_for_woocommerce_get_http_request_args( $check_user = false, $type = 'view' ) {
		$arg = false;

		if ( $check_user ) {
			// phpcs:ignore WordPress.Security.NonceVerification
			$user_items = isset( $_GET['view'] ) ? qode_wishlist_for_woocommerce_get_wishlist_items_by_token( sanitize_text_field( wp_unslash( $_GET['view'] ) ) ) : array();
			$user_id    = is_user_logged_in() ? get_current_user_id() : 'guest';

			if ( ! empty( $user_items ) && isset( $user_items['user'] ) ) {
				$items_user_id        = 'guest' === $user_id ? $user_items['user'] : (int) $user_items['user'];
				$cache_wishlist_items = qode_wishlist_for_woocommerce_get_cookie( QODE_WISHLIST_FOR_WOOCOMMERCE_GUESTS_ITEMS );

				if ( 'guest' === $user_id ) {
					$arg = $user_items !== $cache_wishlist_items;
				} else {
					$arg = $user_id !== $items_user_id;
				}
			}
		} elseif ( isset( $_GET[ $type ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			// phpcs:ignore WordPress.Security.NonceVerification
			$arg = sanitize_text_field( wp_unslash( $_GET[ $type ] ) );
		}

		return $arg;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_items' ) ) {
	/**
	 * Function that return user wishlist items
	 *
	 * @param bool $check_token_items
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_items( $check_token_items = false ) {
		$wishlist_items       = array();
		$cache_wishlist_items = qode_wishlist_for_woocommerce_get_cookie( QODE_WISHLIST_FOR_WOOCOMMERCE_GUESTS_ITEMS );
		$has_token            = qode_wishlist_for_woocommerce_get_http_request_args();

		if ( $check_token_items && $has_token ) {
			$token_items = qode_wishlist_for_woocommerce_get_wishlist_items_by_token( $has_token );

			if ( ! empty( $token_items ) ) {
				$wishlist_items = $token_items;
			}
		} elseif ( is_user_logged_in() ) {
			$user_id             = get_current_user_id();
			$user_wishlist_items = get_user_meta( $user_id, 'qode_wishlist_for_woocommerce_user_wishlist_items', true );

			if ( ! empty( $user_wishlist_items ) ) {
				$wishlist_items = apply_filters( 'qode_wishlist_for_woocommerce_filter_user_wishlist_items', $user_wishlist_items, $user_id );
			}
		} elseif ( ! empty( $cache_wishlist_items ) ) {
			$wishlist_items = $cache_wishlist_items;
		}

		return $wishlist_items;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_items_by_table' ) ) {
	/**
	 * Function that return wishlist items by table name
	 *
	 * @param string|false $table_name
	 * @param array $wishlist_items
	 * @param bool $optimized_list
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_items_by_table( $table_name = false, $wishlist_items = array(), $optimized_list = true ) {
		$table_items = ! empty( $wishlist_items ) ? (array) $wishlist_items : qode_wishlist_for_woocommerce_get_wishlist_items();
		$has_token   = qode_wishlist_for_woocommerce_get_http_request_args();

		if ( $has_token ) {
			$token_items = qode_wishlist_for_woocommerce_get_wishlist_items_by_token( $has_token );

			if ( ! empty( $token_items ) ) {
				$table_items = $token_items;
			}
		}

		// Return all tables if table name is our predefined.
		if ( 'qwfw-all' !== $table_name ) {

			if ( ! empty( $table_name ) ) {
				$table_items = $table_items[ $table_name ] ?? array();
			} elseif ( isset( $table_items['default'] ) ) {
				$table_items = $table_items['default'];
			} else {
				$table_items = array();
			}
		}

		// Remove unnecessary items from the list in order to return only wishlist item values.
		if ( $optimized_list ) {
			$table_items = qode_wishlist_for_woocommerce_get_cleaned_wishlist_items( $table_items );
		}

		return array_reverse( $table_items, true );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_table_names' ) ) {
	/**
	 * Function that return user wishlist table names
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_table_names() {
		$wishlist_items = qode_wishlist_for_woocommerce_get_wishlist_items();
		$table_names    = array(
			'default' => esc_attr__( 'My Wishlist', 'qode-wishlist-for-woocommerce' ),
		);

		if ( ! empty( $wishlist_items ) ) {
			foreach ( $wishlist_items as $wishlist_item_key => $wishlist_item_value ) {
				// Skip if value is not array, because only items are array type.
				if ( ! is_array( $wishlist_item_value ) || ! isset( $wishlist_item_value['table_title'] ) ) {
					continue;
				}

				$table_names[ $wishlist_item_key ] = $wishlist_item_value['table_title'];
			}
		}

		return array_unique( $table_names );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_table_meta' ) ) {
	/**
	 * Function that return user wishlist table meta value
	 *
	 * @param string $table_name
	 * @param string $meta_key
	 * @param array $items
	 *
	 * @return string|array
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_table_meta( $table_name, $meta_key, $items = array() ) {
		$wishlist_items = ! empty( $items ) ? $items : qode_wishlist_for_woocommerce_get_wishlist_items();
		$return_value   = '';

		if ( ! empty( $wishlist_items ) && ! empty( $table_name ) && ! empty( $meta_key ) ) {
			foreach ( $wishlist_items as $wishlist_item_key => $wishlist_item_value ) {

				if ( $table_name === $wishlist_item_key && isset( $wishlist_item_value[ $meta_key ] ) ) {
					$return_value = $wishlist_item_value[ $meta_key ];
					break;
				} elseif ( isset( $wishlist_items[ $table_name ] ) && $meta_key === $wishlist_item_key ) {
					$return_value = $wishlist_item_value;
					break;
				}
			}
		}

		return $return_value;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_tables_by_item_id' ) ) {
	/**
	 * Function that return user wishlist tables that contains Item ID
	 *
	 * @param int $item_id
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_tables_by_item_id( $item_id ) {
		$wishlist_items = qode_wishlist_for_woocommerce_get_wishlist_items();
		$table_names    = array();

		if ( ! empty( $wishlist_items ) && ! empty( $item_id ) ) {
			foreach ( $wishlist_items as $wishlist_item_key => $wishlist_item_value ) {
				// Skip if value is not array, because only items are array type.
				if ( ! is_array( $wishlist_item_value ) ) {
					continue;
				}

				if ( in_array( $item_id, array_keys( $wishlist_item_value ), true ) ) {
					$table_names[ $wishlist_item_key ] = $wishlist_item_value;
				}
			}
		}

		return $table_names;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_token_items' ) ) {
	/**
	 * Function that return transient wishlist items
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_token_items() {
		$items = get_transient( QODE_WISHLIST_FOR_WOOCOMMERCE_GUESTS_ITEMS );

		return ! empty( $items ) ? (array) array_reverse( $items, true ) : array();
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_items_by_token' ) ) {
	/**
	 * Function that return specific user wishlist items by token
	 *
	 * @param string $token
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_items_by_token( $token ) {
		$items       = qode_wishlist_for_woocommerce_get_wishlist_token_items();
		$table_items = array();

		if ( isset( $items[ $token ] ) && $items[ $token ]['token'] === $token ) {
			$table_items = $items[ $token ];
		}

		return $table_items;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_item_wishlists_count' ) ) {
	/**
	 * Function that return item wishlists count
	 *
	 * @param int $item_id
	 *
	 * @return int
	 */
	function qode_wishlist_for_woocommerce_get_item_wishlists_count( $item_id ) {
		$items = qode_wishlist_for_woocommerce_get_wishlist_token_items();

		// Check product ID.
		if ( empty( $item_id ) || empty( $items ) ) {
			return 0;
		}

		$count = 0;
		foreach ( $items as $tables ) {
			if ( ! empty( $tables ) ) {
				foreach ( $tables as $table_items ) {

					if ( ! empty( $table_items ) && is_array( $table_items ) ) {

						if ( in_array( $item_id, array_keys( $table_items ), true ) ) {
							$count++;
						}
					}
				}
			}
		}

		return $count;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlists_count' ) ) {
	/**
	 * Function that return number of all wishlists count
	 *
	 * @param array $items
	 *
	 * @return int
	 */
	function qode_wishlist_for_woocommerce_get_wishlists_count( $items ) {
		$count = 0;

		if ( ! empty( $items ) ) {
			$items = qode_wishlist_for_woocommerce_get_cleaned_wishlist_items( $items );

			foreach ( $items as $table_items ) {

				if ( ! empty( $table_items ) && is_array( $table_items ) ) {
					$count = $count + count( array_keys( $table_items ) );
				}
			}
		}

		return $count;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_item_users' ) ) {
	/**
	 * Function that return users list who have added this product to their wishlist
	 *
	 * @param int $item_id
	 * @param string $return_user_meta
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_wishlist_item_users( $item_id, $return_user_meta = '' ) {
		// Array will contain user data in following order:
		// 1. ID.
		// 2. Email.
		// 3. First Name.
		// 4. Last Name.
		// 5. Display Name.
		$users      = array();
		$all_tables = qode_wishlist_for_woocommerce_get_wishlist_token_items();

		if ( ! empty( $all_tables ) && ! empty( $item_id ) ) {

			foreach ( $all_tables as $token => $table_values ) {
				foreach ( $table_values as $table_name_key => $table_name_value ) {
					$current_table = $all_tables[ $token ];

					// Skip if value is not array, because only items are array type.
					if ( ! is_array( $table_name_value ) ) {
						continue;
					}

					// Skip if current user is guest.
					if ( ! isset( $current_table['user'] ) || ! is_numeric( $current_table['user'] ) ) {
						continue;
					}

					// Get wishlist table items.
					$table_items = qode_wishlist_for_woocommerce_get_cleaned_wishlist_items( $table_name_value );
					if ( ! empty( $table_items ) ) {
						$user_id = (int) $current_table['user'];
						$user    = get_user_by( 'id', $user_id );

						if ( ! empty( $user ) ) {
							foreach ( $table_items as $table_item ) {

								if ( (int) $item_id === (int) $table_item['product_id'] ) {
									$user_meta = array(
										$user_id,
										$user->user_email,
										! empty( $user_obj->billing_first_name ) ? $user->billing_first_name : $user->first_name,
										! empty( $user_obj->billing_last_name ) ? $user->billing_last_name : $user->last_name,
										$user->display_name,
									);

									if ( ! empty( $return_user_meta ) ) {
										switch ( $return_user_meta ) {
											case 'ID':
												$user_meta = array( $user_meta[0] );
												break;
											case 'email':
												$user_meta = array( $user_meta[1] );
												break;
										}
									}

									$users[ $user_id ] = $user_meta;
								}
							}
						}
					}
				}
			}
		}

		return $users;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_cleaned_wishlist_tables' ) ) {
	/**
	 * Function that return clean wishlist table list, remove unnecessary items from the list
	 *
	 * @param array $items
	 * @param string $search_query
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_cleaned_wishlist_tables( $items, $search_query = '' ) {
		$cleaned_items = array();

		if ( ! empty( $items ) ) {
			foreach ( $items as $item_key => $item_value ) {

				if ( in_array( $item_key, array( 'token', 'user', 'user_name', 'date_created' ), true ) ) {
					continue;
				}

				// Check the search query string and set items that contain that query string.
				$search_skip_flag = false;
				if ( ! empty( $search_query ) ) {
					$search_query       = strtolower( $search_query );
					$search_table_title = strtolower( $item_value['table_title'] );

					// Check Wishlist table name.
					if ( strpos( $search_table_title, $search_query ) === false ) {
						$search_skip_flag = true;
					}

					// Check Wishlist products title.
					if ( ! empty( $item_value ) ) {
						$search_item_flag = false;

						foreach ( $item_value as $table_item ) {
							if ( isset( $table_item['product_title'] ) && strpos( strtolower( $table_item['product_title'] ), $search_query ) !== false ) {
								$search_item_flag = true;
							}
						}

						if ( $search_item_flag ) {
							$search_skip_flag = false;
						}
					}
				}

				if ( $search_skip_flag ) {
					continue;
				}

				$cleaned_items[ $item_key ] = $item_value;
			}
		}

		return $cleaned_items;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_cleaned_wishlist_items' ) ) {
	/**
	 * Function that return clean wishlist items list, remove unnecessary items from the list
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_cleaned_wishlist_items( $items ) {

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

				// Check Wishlist item is valid Product, otherwise remove it.
				if ( isset( $item_value['product_id'] ) || 'product_id' === $item_key ) {
					$product_id = absint( $item_value['product_id'] ?? $item_key );

					if ( empty( wc_get_product( $product_id ) ) ) {
						unset( $items[ $item_key ] );
					}
				}
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_sorted_wishlist_items' ) ) {
	/**
	 * Function that return sorted wishlist items list
	 *
	 * @param array $items
	 * @param string $order_by
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_sorted_wishlist_items( $items, $order_by = '' ) {
		$sorted_items = $items;

		if ( ! empty( $items ) && ! empty( $order_by ) ) {
			$sort_map = array();

			switch ( $order_by ) {
				case 'price':
					foreach ( $items as $item_id => $item_values ) {

						// Skip if value is not array, because only items are array type.
						if ( ! is_array( $item_values ) ) {
							continue;
						}

						$sort_map[ $item_id ] = wc_get_price_to_display( wc_get_product( $item_id ) );
					}

					asort( $sort_map );

					$sorted_items = array_replace( $sort_map, $items );
					break;
				case 'price-desc':
					foreach ( $items as $item_id => $item_values ) {

						// Skip if value is not array, because only items are array type.
						if ( ! is_array( $item_values ) ) {
							continue;
						}

						$sort_map[ $item_id ] = wc_get_price_to_display( wc_get_product( $item_id ) );
					}

					arsort( $sort_map );

					$sorted_items = array_replace( $sort_map, $items );
					break;
				case 'name':
					foreach ( $items as $item_id => $item_values ) {

						// Skip if value is not array, because only items are array type.
						if ( ! is_array( $item_values ) ) {
							continue;
						}

						$sort_map[ $item_id ] = $item_values['product_title'] ?? '';
					}

					asort( $sort_map );

					$sorted_items = array_replace( $sort_map, $items );
					break;
				case 'name-desc':
					foreach ( $items as $item_id => $item_values ) {

						// Skip if value is not array, because only items are array type.
						if ( ! is_array( $item_values ) ) {
							continue;
						}

						$sort_map[ $item_id ] = $item_values['product_title'] ?? '';
					}

					arsort( $sort_map );

					$sorted_items = array_replace( $sort_map, $items );
					break;
				case 'stock-level':
					foreach ( $items as $item_id => $item_values ) {

						// Skip if value is not array, because only items are array type.
						if ( ! is_array( $item_values ) ) {
							continue;
						}

						$product = wc_get_product( $item_id );

						$sort_map[ $item_id ] = $product->get_stock_quantity() ?? 99999;
					}

					arsort( $sort_map );

					$sorted_items = array_replace( $sort_map, $items );
					break;
				case 'discount':
					$sale_price_map    = array();
					$regular_price_map = array();
					foreach ( $items as $item_id => $item_values ) {

						// Skip if value is not array, because only items are array type.
						if ( ! is_array( $item_values ) ) {
							continue;
						}

						$product = wc_get_product( $item_id );

						if ( $product->is_on_sale() ) {
							$sale_price_map[ $item_id ] = wc_get_price_to_display( $product );
						} else {
							$regular_price_map[ $item_id ] = wc_get_price_to_display( $product );
						}
					}

					if ( ! empty( $sale_price_map ) ) {
						asort( $sale_price_map );
					}

					if ( ! empty( $regular_price_map ) ) {
						asort( $regular_price_map );
					}

					$sort_map = array_replace( $sale_price_map, $regular_price_map );

					$sorted_items = array_replace( $sort_map, $items );
					break;
			}
		}

		return $sorted_items;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_check_is_wishlist_item_added' ) ) {
	/**
	 * Function that checks if an item is forwarded is already in the wishlist
	 *
	 * @param int $item_id
	 * @param bool|string $table_name
	 *
	 * @return bool
	 */
	function qode_wishlist_for_woocommerce_check_is_wishlist_item_added( $item_id, $table_name = false ) {
		$wishlist_items = qode_wishlist_for_woocommerce_get_wishlist_items();

		// Check product ID and items.
		if ( empty( $item_id ) || empty( $wishlist_items ) ) {
			return false;
		}

		$flag = false;
		foreach ( $wishlist_items as $wishlist_item_key => $wishlist_item_value ) {
			// Skip if value is not array, because only items are array type.
			if ( ! is_array( $wishlist_item_value ) ) {
				continue;
			}

			if ( ! empty( $table_name ) ) {

				if ( $wishlist_item_key === $table_name && isset( $wishlist_item_value[ qode_wishlist_for_woocommerce_get_original_product_id( $item_id ) ] ) ) {
					$flag = true;
				}
			} else {

				if ( isset( $wishlist_item_value[ qode_wishlist_for_woocommerce_get_original_product_id( $item_id ) ] ) ) {
					$flag = true;
				}
			}
		}

		return $flag;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_update_wishlist_items' ) ) {
	/**
	 * Function that update user wishlist items
	 *
	 * @param array $item_ids
	 * @param string $action
	 * @param string|false $new_table_name
	 * @param string|bool $user_token
	 * @param string $new_table_visibility
	 */
	function qode_wishlist_for_woocommerce_update_wishlist_items( $item_ids, $action, $new_table_name = false, $user_token = false, $new_table_visibility = '' ) {
		$items            = qode_wishlist_for_woocommerce_get_wishlist_items();
		$update_flag      = true;
		$table_name_title = esc_attr__( 'My Wishlist', 'qode-wishlist-for-woocommerce' );
		$table_name       = 'default';

		// Set new table name.
		if ( ! empty( $new_table_name ) && 'default' !== $new_table_name ) {
			$table_name_title = esc_html( esc_attr( $new_table_name ) );
			$table_name       = sanitize_title( $new_table_name );
		}

		// If user wishlist table doesn't exist, create it.
		if ( ! isset( $items[ $table_name ] ) ) {
			$items[ $table_name ] = array(
				'table_title' => $table_name_title,
			);
		}

		// If user wishlist table title doesn't exist, set it.
		if ( ! isset( $items[ $table_name ]['table_title'] ) ) {
			$items[ $table_name ]['table_title'] = $table_name_title;
		}

		// If user default wishlist table title is translated, set the new one.
		if ( 'default' === $table_name && $items[ $table_name ]['table_title'] !== $table_name_title ) {
			$items[ $table_name ]['table_title'] = $table_name_title;
		}

		if ( ! empty( $item_ids ) ) {
			foreach ( $item_ids as $item_id ) {
				$item_id = apply_filters( 'qode_wishlist_for_woocommerce_filter_item_id', qode_wishlist_for_woocommerce_get_original_product_id( $item_id ), $action, $table_name );

				if ( 'remove' === $action && isset( $items[ $table_name ][ $item_id ] ) ) {
					unset( $items[ $table_name ][ $item_id ] );
				} elseif ( 'add' === $action ) {
					$items[ $table_name ][ $item_id ] = array(
						'product_id'    => $item_id,
						'product_title' => get_the_title( $item_id ),
						'quantity'      => 1,
						'date_added'    => date_i18n( get_option( 'date_format' ) ),
					);
				} elseif ( 'update_variation' === $action ) {
					$update_flag = false;
				}
			}
		}

		// Set token.
		$token = qode_wishlist_for_woocommerce_generate_token();
		if ( isset( $items['token'] ) ) {
			$token = $items['token'];
		} elseif ( ! empty( $user_token ) ) {
			$token = esc_attr( $user_token );
		}

		// Set unique wishlist items token.
		$items['token'] = $token;

		// Set user.
		$items['user']      = 'guest';
		$items['user_name'] = esc_attr__( 'Guest', 'qode-wishlist-for-woocommerce' );
		if ( is_user_logged_in() ) {
			$current_user_id = get_current_user_id();
			$current_user    = get_user_by( 'id', $current_user_id );

			$items['user']      = $current_user_id;
			$items['user_name'] = esc_attr( $current_user->display_name );
		}

		// Set wishlist created day.
		if ( ! isset( $items[ $table_name ]['date_created'] ) ) {
			$items[ $table_name ]['date_created'] = date_i18n( get_option( 'date_format' ) );
		}

		// Set wishlist visibility.
		$table_visibility        = $items[ $table_name ]['visibility'] ?? 'public';
		$table_visibility_option = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_set_default_wishlist_page_privacy' );

		if ( 'default' === $table_name && ! empty( $table_visibility_option ) ) {
			$table_visibility = $table_visibility_option;
		} elseif ( 'default' !== $table_name && ! empty( $new_table_visibility ) ) {
			$table_visibility = $new_table_visibility;
		}

		if ( ! isset( $items[ $table_name ]['visibility'] ) || $items[ $table_name ]['visibility'] !== $table_visibility ) {
			$items[ $table_name ]['visibility'] = sanitize_text_field( $table_visibility );
		}

		if ( $update_flag ) {
			qode_wishlist_for_woocommerce_update_wishlist_items_db( $items, $token );
		}
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_update_wishlist_items_db' ) ) {
	/**
	 * Function that update current user wishlist items into database
	 */
	function qode_wishlist_for_woocommerce_update_wishlist_items_db( $items, $token ) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();

			update_user_meta( $user_id, 'qode_wishlist_for_woocommerce_user_wishlist_items', $items );
		} else {
			qode_wishlist_for_woocommerce_set_cookie( QODE_WISHLIST_FOR_WOOCOMMERCE_GUESTS_ITEMS, $items );
		}

		// Save wishlist items into corresponding token tables.
		$token_items = qode_wishlist_for_woocommerce_get_wishlist_token_items();

		$token_items[ $token ] = $items;

		set_transient( QODE_WISHLIST_FOR_WOOCOMMERCE_GUESTS_ITEMS, $token_items );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_remove_wishlist_item_db' ) ) {
	/**
	 * Function that remove forward wishlist item from database
	 *
	 * @param string $token
	 * @param string $table_name
	 */
	function qode_wishlist_for_woocommerce_remove_wishlist_item_db( $token, $table_name ) {

		// phpcs:ignore WordPress.WP.Capabilities.Unknown
		if ( is_user_logged_in() && current_user_can( 'manage_woocommerce' ) && ! empty( $token ) && ! empty( $table_name ) ) {
			$user = qode_wishlist_for_woocommerce_get_wishlist_table_meta( $table_name, 'user', $token_items[ $token ] ?? array() );

			// Remove current user wishlist items.
			if ( is_numeric( $user ) ) {
				$user_items = get_user_meta( (int) $user, 'qode_wishlist_for_woocommerce_user_wishlist_items', true );

				if ( ! empty( $user_items ) && isset( $user_items[ $table_name ] ) ) {
					unset( $user_items[ $table_name ] );

					update_user_meta( (int) $user, 'qode_wishlist_for_woocommerce_user_wishlist_items', $user_items );
				}
			}

			// Remove wishlist items from corresponding token tables.
			$token_items = qode_wishlist_for_woocommerce_get_wishlist_token_items();

			if ( isset( $token_items[ $token ][ $table_name ] ) ) {
				unset( $token_items[ $token ][ $table_name ] );

				set_transient( QODE_WISHLIST_FOR_WOOCOMMERCE_GUESTS_ITEMS, $token_items );
			}
		}
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_update_user_wishlist_items' ) ) {
	/**
	 * Function that update current user wishlist items with cached items
	 */
	function qode_wishlist_for_woocommerce_update_user_wishlist_items() {
		$cache_items = qode_wishlist_for_woocommerce_get_cookie( QODE_WISHLIST_FOR_WOOCOMMERCE_GUESTS_ITEMS );

		if ( is_user_logged_in() && ! empty( $cache_items ) ) {
			$user_id          = get_current_user_id();
			$items            = qode_wishlist_for_woocommerce_get_wishlist_items();
			$token_items      = qode_wishlist_for_woocommerce_get_wishlist_token_items();
			$cache_token      = '';
			$table_name_title = esc_attr__( 'My Wishlist', 'qode-wishlist-for-woocommerce' );

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

				// If user wishlist table doesn't exist, create it.
				if ( ! isset( $items[ $table_name ] ) ) {
					$items[ $table_name ] = array(
						'table_title' => $table_name_title,
					);
				}

				// If user wishlist table title doesn't exist, set it.
				if ( ! isset( $items[ $table_name ]['table_title'] ) ) {
					$items[ $table_name ]['table_title'] = $table_name_title;
				}

				foreach ( $cache_item_value as $item_id => $item_value ) {

					// Skip if value is not array, because only items are array type.
					if ( ! is_array( $item_value ) ) {
						continue;
					}

					$items[ $table_name ][ $item_id ] = array(
						'product_id'    => $item_value['product_id'],
						'product_title' => get_the_title( $item_id ),
						'quantity'      => $item_value['quantity'] ?? 1,
						'date_added'    => $item_value['date_added'],
					);
				}

				// Set wishlist created day.
				if ( ! isset( $items[ $table_name ]['date_created'] ) ) {
					$items[ $table_name ]['date_created'] = date_i18n( get_option( 'date_format' ) );
				}

				// Set wishlist visibility.
				$table_visibility        = $items[ $table_name ]['visibility'] ?? 'public';
				$table_visibility_option = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_set_default_wishlist_page_privacy' );

				if ( 'default' === $table_name && ! empty( $table_visibility_option ) ) {
					$table_visibility = $table_visibility_option;
				}

				if ( ! isset( $items[ $table_name ]['visibility'] ) ) {
					$items[ $table_name ]['visibility'] = sanitize_text_field( $table_visibility );
				}
			}

			// Update current user cached wishlist items.
			update_user_meta( $user_id, 'qode_wishlist_for_woocommerce_user_wishlist_items', $items );

			// Remove current user cached wishlist items.
			qode_wishlist_for_woocommerce_destroy_cookie( QODE_WISHLIST_FOR_WOOCOMMERCE_GUESTS_ITEMS );

			// Remove wishlist items from corresponding token tables.
			if ( ! empty( $token_items ) && ! empty( $cache_token ) ) {
				foreach ( $token_items as $token_item_key => $token_item_value ) {

					if ( $cache_token === $token_item_key ) {
						unset( $token_items[ $token_item_key ] );
					}
				}

				set_transient( QODE_WISHLIST_FOR_WOOCOMMERCE_GUESTS_ITEMS, $token_items );
			}
		}
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_check_is_guests_wishlist_expired' ) ) {
	/**
	 * Function that remove forward wishlist item from database
	 *
	 * @param string $token
	 * @param string $table_name
	 */
	function qode_wishlist_for_woocommerce_check_is_guests_wishlist_expired() {
		$require_login = (bool) apply_filters( 'qode_wishlist_for_woocommerce_filter_wishlist_feature_require_login', false );

		if ( ! $require_login ) {
			$all_items         = qode_wishlist_for_woocommerce_get_wishlist_token_items();
			$expiration_time   = qode_wishlist_for_woocommerce_get_cookie_expiration();
			$update_items_flag = false;

			if ( ! empty( $all_items ) ) {
				foreach ( $all_items as $token => $table_values ) {
					foreach ( $table_values as $table_name_key => $table_name_value ) {
						$current_table = $all_items[ $token ] ?? '';

						// Check is guest Wishlist and Wishlist date of creation exists.
						if ( ! empty( $current_table ) && 'guest' === ( $current_table['user'] ?? '' ) && isset( $current_table[ $table_name_key ]['date_created'] ) ) {

							// If Wishlist date of creation is older than guest visibility expiration, remove the Wishlist.
							if ( strtotime( (string) $current_table[ $table_name_key ]['date_created'] ) < ( time() - $expiration_time ) ) {
								unset( $all_items[ $token ][ $table_name_key ] );

								// If the guest has no more Wishlists, remove the whole table token map.
								if ( empty( qode_wishlist_for_woocommerce_get_cleaned_wishlist_tables( $all_items[ $token ] ) ) ) {
									unset( $all_items[ $token ] );
								}

								$update_items_flag = true;
							}
						}
					}
				}
			}

			if ( $update_items_flag ) {
				set_transient( QODE_WISHLIST_FOR_WOOCOMMERCE_GUESTS_ITEMS, $all_items );
			}
		}
	}

	add_action( 'qode_wishlist_for_woocommerce_trigger_guests_wishlist_check', 'qode_wishlist_for_woocommerce_check_is_guests_wishlist_expired' );
}
