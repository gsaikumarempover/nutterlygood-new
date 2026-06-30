<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/social-share/helper.php';
include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/social-share/class-qode-wishlist-for-woocommerce-social-share.php';

foreach ( glob( QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/social-share/dashboard/admin/*.php' ) as $option ) {
	include_once $option;
}
