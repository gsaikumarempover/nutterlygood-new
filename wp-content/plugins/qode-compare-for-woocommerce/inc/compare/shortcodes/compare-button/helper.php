<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_compare_for_woocommerce_is_single_product_page' ) ) {
	/**
	 * Function that checks if current page is single product page
	 *
	 * @return bool
	 */
	function qode_compare_for_woocommerce_is_single_product_page() {
		return is_product() && function_exists( 'wc_get_loop_prop' ) && ! in_array( wc_get_loop_prop( 'name' ), array( 'related', 'up-sells' ), true );
	}
}
