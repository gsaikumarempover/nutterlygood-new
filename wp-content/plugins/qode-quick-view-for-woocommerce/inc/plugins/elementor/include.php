<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( qode_quick_view_for_woocommerce_is_installed( 'elementor' ) ) {
	require_once QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/plugins/elementor/helper.php';
	require_once QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/plugins/elementor/class-qode-quick-view-for-woocommerce-elementor-handler.php';
}
