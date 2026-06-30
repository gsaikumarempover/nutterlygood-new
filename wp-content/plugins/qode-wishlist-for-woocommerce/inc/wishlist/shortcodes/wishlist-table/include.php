<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/wishlist/shortcodes/wishlist-table/helper.php';
include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/wishlist/shortcodes/wishlist-table/helper-ajax.php';
include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/wishlist/shortcodes/wishlist-table/class-qode-wishlist-for-woocommerce-wishlist-table-shortcode.php';

foreach ( glob( QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/wishlist/shortcodes/wishlist-table/dashboard/*/*.php' ) as $option ) {
	include_once $option;
}
