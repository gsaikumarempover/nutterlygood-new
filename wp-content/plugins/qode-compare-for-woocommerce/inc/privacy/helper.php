<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_compare_for_woocommerce_get_privacy_user_compare_items' ) ) {
	/**
	 * Function that return list of user comparisons
	 *
	 * @param array $user_args
	 *
	 * @return array
	 */
	function qode_compare_for_woocommerce_get_privacy_user_compare_items( $user_args ) {
		$user_compare = array();
		$all_tables   = qode_compare_for_woocommerce_get_comparison_token_items();

		if ( ! empty( $all_tables ) && isset( $user_args['user_id'] ) ) {
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

					// Only get current user items.
					if ( $current_table['user'] === $user_args['user_id'] ) {
						// Get compare table items.
						$table_items = qode_compare_for_woocommerce_get_cleaned_comparison_items( $table_name_value );

						if ( ! empty( $table_items ) ) {
							$user_compare[ $table_name_key ] = array(
								'token'        => $current_table['token'],
								'table_key'    => $table_name_key,
								'table_title'  => $table_name_value['table_title'] ?? '',
								'date_created' => $table_name_value['date_created'] ?? '',
								'visibility'   => $table_name_value['visibility'] ?? '',
								'items'        => $table_items,
							);
						}
					}
				}
			}

			if ( ! empty( $user_compare ) ) {
				ksort( $user_compare );

				$user_compare = array_slice( $user_compare, $user_args['offset'] ?? 0, $user_args['limit'] ?? 10 );
			}
		}

		return $user_compare;
	}
}
