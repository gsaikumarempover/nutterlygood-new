<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

require_once QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/quick-view/shortcodes/quick-view-button/helper.php';
require_once QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/quick-view/shortcodes/quick-view-button/class-qode-quick-view-for-woocommerce-quick-view-button-shortcode.php';

foreach ( glob( QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/quick-view/shortcodes/quick-view-button/dashboard/*/*.php' ) as $option ) {
	require_once $option;
}

foreach ( glob( QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/quick-view/shortcodes/quick-view-button/layouts/*/include.php' ) as $layout ) {
	require_once $layout;
}
