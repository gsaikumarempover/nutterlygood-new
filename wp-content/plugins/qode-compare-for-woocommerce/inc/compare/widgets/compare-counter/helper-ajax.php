<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
if ( ! function_exists( 'qode_compare_for_woocommerce_extend_compare_table_response_data_with_counter_widget_new_content' ) ) {
	/**
	 * Function that overwrites compare response data
	 *
	 * @param array $response_data
	 * @param array $comparison_items
	 * @param string $action
	 * @param array $options
	 *
	 * @return array
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	function qode_compare_for_woocommerce_extend_compare_table_response_data_with_counter_widget_new_content( $response_data, $comparison_items, $action, $options ) {

		if ( is_active_widget( false, false, 'qode_compare_for_woocommerce_compare_counter' ) ) {
			$params = array(
				'compare_items' => $comparison_items,
				'opener_url'    => apply_filters( 'qode_compare_for_woocommerce_filter_widget_compare_button_url', '#' ),
			);

			$response_data['widget_counter_trc_new_content']  = qode_compare_for_woocommerce_get_template_part( 'compare', 'widgets/compare-counter/templates/content', 'top-right-count', $params );
			$response_data['widget_counter_wi_new_content']   = qode_compare_for_woocommerce_get_template_part( 'compare', 'widgets/compare-counter/templates/content', 'with-icon', $params );
			$response_data['widget_counter_icon_new_content'] = qode_compare_for_woocommerce_get_template_part( 'compare', 'widgets/compare-counter/templates/content', 'icon', $params );
		}

		return $response_data;
	}

	add_filter( 'qode_compare_for_woocommerce_filter_compare_table_response_data', 'qode_compare_for_woocommerce_extend_compare_table_response_data_with_counter_widget_new_content', 5, 4 );
}
