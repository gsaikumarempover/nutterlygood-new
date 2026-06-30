<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_add_quick_view_variation_pop_up' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function qode_quick_view_for_woocommerce_add_quick_view_variation_pop_up( $variations ) {
		$variations['pop-up'] = esc_html__( 'Pop-up', 'qode-quick-view-for-woocommerce' );

		return $variations;
	}

	add_filter( 'qode_quick_view_for_woocommerce_filter_quick_view_layouts', 'qode_quick_view_for_woocommerce_add_quick_view_variation_pop_up' );
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_get_quick_view_variation_pop_up_classes' ) ) {
	/**
	 * Function that return classes for this module
	 *
	 * @return string
	 */
	function qode_quick_view_for_woocommerce_get_quick_view_variation_pop_up_classes( $classes ) {
		$classes[] = 'qqvfw-type--pop-up';

		return $classes;
	}

	add_filter( 'qode_quick_view_for_woocommerce_filter_set_quick_view_pop_up_classes', 'qode_quick_view_for_woocommerce_get_quick_view_variation_pop_up_classes' );
}
