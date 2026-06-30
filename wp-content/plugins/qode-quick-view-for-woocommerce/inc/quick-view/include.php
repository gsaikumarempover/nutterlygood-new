<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

require_once QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/quick-view/dashboard/admin/global-options.php';
require_once QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/quick-view/dashboard/admin/content-options.php';
require_once QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/quick-view/class-qode-quick-view-for-woocommerce-module.php';

foreach ( glob( QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/quick-view/blocks/*/include.php' ) as $block ) {
	require_once $block;
}

foreach ( glob( QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/quick-view/variations/*/include.php' ) as $variation ) {
	require_once $variation;
}
