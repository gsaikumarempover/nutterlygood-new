<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

foreach ( glob( QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/plugins/*/include.php' ) as $module ) {
	include_once $module;
}
