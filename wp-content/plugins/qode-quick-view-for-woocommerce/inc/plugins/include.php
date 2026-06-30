<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

foreach ( glob( QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/plugins/*/include.php' ) as $module ) {
	require_once $module;
}
