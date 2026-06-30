<?php
/**
 * Product card add-to-cart — AJAX, no "View cart" overlap, open side cart drawer.
 */

if ( ! function_exists( 'nuttergood_farmley_repair_woocommerce_pages' ) ) {
	/**
	 * Point cart/checkout options at published pages when stored IDs are missing.
	 */
	function nuttergood_farmley_repair_woocommerce_pages() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$map = array(
			'woocommerce_cart_page_id'     => array( 'cart-2', 'cart' ),
			'woocommerce_checkout_page_id' => array( 'checkout-2', 'checkout' ),
		);

		foreach ( $map as $option => $slugs ) {
			$page_id = (int) get_option( $option, 0 );
			$valid   = $page_id > 0 && 'publish' === get_post_status( $page_id );

			if ( $valid ) {
				continue;
			}

			foreach ( $slugs as $slug ) {
				$page = get_page_by_path( $slug );
				if ( $page && 'publish' === $page->post_status ) {
					update_option( $option, (int) $page->ID );
					break;
				}
			}
		}
	}

	add_action( 'init', 'nuttergood_farmley_repair_woocommerce_pages', 4 );
}

if ( ! function_exists( 'nuttergood_farmley_get_checkout_url' ) ) {
	function nuttergood_farmley_get_checkout_url() {
		if ( function_exists( 'nuttergood_farmley_repair_woocommerce_pages' ) ) {
			nuttergood_farmley_repair_woocommerce_pages();
		}

		if ( function_exists( 'wc_get_checkout_url' ) ) {
			$checkout_id = wc_get_page_id( 'checkout' );
			if ( $checkout_id > 0 && 'publish' === get_post_status( $checkout_id ) ) {
				return wc_get_checkout_url();
			}
		}

		foreach ( array( 'checkout-2', 'checkout' ) as $slug ) {
			$page = get_page_by_path( $slug );
			if ( $page && 'publish' === $page->post_status ) {
				return get_permalink( $page );
			}
		}

		return home_url( '/checkout/' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_cart_drawer_should_load' ) ) {
	function nuttergood_farmley_cart_drawer_should_load() {
		if ( is_admin() || ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		if ( function_exists( 'nuttergood_farmley_should_apply_product_cards' ) ) {
			return nuttergood_farmley_should_apply_product_cards();
		}

		return function_exists( 'is_woocommerce' ) && is_woocommerce();
	}
}

if ( ! function_exists( 'nuttergood_farmley_cart_drawer_assets' ) ) {
	function nuttergood_farmley_cart_drawer_assets() {
		if ( ! nuttergood_farmley_cart_drawer_should_load() ) {
			return;
		}

		wp_enqueue_script( 'wc-cart-fragments' );

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$js  = $dir . '/assets/js/farmley-cart-drawer.js';

		if ( ! file_exists( $js ) ) {
			return;
		}

		wp_enqueue_script(
			'nuttergood-farmley-cart-drawer',
			$uri . '/assets/js/farmley-cart-drawer.js',
			array( 'jquery', 'wc-add-to-cart', 'wc-cart-fragments', 'greenpath-core-script' ),
			filemtime( $js ),
			true
		);

		wp_localize_script(
			'nuttergood-farmley-cart-drawer',
			'ngFarmleyCartDrawer',
			array(
				'enabled'     => true,
				'checkoutUrl' => function_exists( 'nuttergood_farmley_get_checkout_url' )
					? nuttergood_farmley_get_checkout_url()
					: ( function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' ) ),
				'i18nBuyNowLoading' => __( 'Processing…', 'nuttergood' ),
			)
		);
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_cart_drawer_assets', 40 );
}
