<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/wishlist/shortcodes/add-to-wishlist/helper.php';
include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/wishlist/shortcodes/add-to-wishlist/helper-ajax.php';
include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/wishlist/shortcodes/add-to-wishlist/class-qode-wishlist-for-woocommerce-add-to-wishlist-shortcode.php';

foreach ( glob( QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/wishlist/shortcodes/add-to-wishlist/dashboard/*/*.php' ) as $option ) {
	include_once $option;
}
