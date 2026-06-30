<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( qode_wishlist_for_woocommerce_is_installed( 'elementor' ) ) {
	include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/plugins/elementor/helper.php';
	include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/plugins/elementor/class-qode-wishlist-for-woocommerce-elementor-handler.php';
}
