<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

require_once QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/quick-view/variations/pop-up/helper.php';

foreach ( glob( QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/quick-view/variations/pop-up/dashboard/*/*.php' ) as $option ) {
	require_once $option;
}
