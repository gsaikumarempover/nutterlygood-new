<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/wishlist/helper.php';
include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/wishlist/class-qode-wishlist-for-woocommerce-wishlist-template.php';
include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/wishlist/class-qode-wishlist-for-woocommerce-wishlist-module.php';

foreach ( glob( QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/wishlist/blocks/*/include.php' ) as $block ) {
	include_once $block;
}

foreach ( glob( QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/wishlist/dashboard/*/*.php' ) as $option ) {
	include_once $option;
}
