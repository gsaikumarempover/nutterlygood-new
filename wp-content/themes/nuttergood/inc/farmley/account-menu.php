<?php
/**
 * My Account navigation — hide unused endpoints.
 */

if ( ! function_exists( 'nuttergood_farmley_account_menu_items' ) ) {
	function nuttergood_farmley_account_menu_items( $items ) {
		unset( $items['downloads'] );

		if ( function_exists( 'greenpath_membership_set_woo_profile_key' ) ) {
			$key = greenpath_membership_set_woo_profile_key();
			if ( $key && isset( $items[ $key ] ) ) {
				unset( $items[ $key ] );
			}
		}

		return $items;
	}
	add_filter( 'woocommerce_account_menu_items', 'nuttergood_farmley_account_menu_items', 99 );
}

if ( ! function_exists( 'nuttergood_farmley_redirect_dashboard_endpoint' ) ) {
	function nuttergood_farmley_redirect_dashboard_endpoint() {
		if ( ! function_exists( 'is_account_page' ) || ! is_account_page() || ! is_user_logged_in() ) {
			return;
		}

		if ( ! function_exists( 'greenpath_membership_set_woo_profile_key' ) ) {
			return;
		}

		$key = greenpath_membership_set_woo_profile_key();
		if ( ! $key || ! is_wc_endpoint_url( $key ) ) {
			return;
		}

		wp_safe_redirect( wc_get_account_endpoint_url( 'orders' ) );
		exit;
	}
	add_action( 'template_redirect', 'nuttergood_farmley_redirect_dashboard_endpoint', 6 );
}