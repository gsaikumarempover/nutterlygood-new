<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

include_once QODE_COMPARE_FOR_WOOCOMMERCE_INC_PATH . '/compare/shortcodes/compare-button/helper.php';
include_once QODE_COMPARE_FOR_WOOCOMMERCE_INC_PATH . '/compare/shortcodes/compare-button/helper-ajax.php';
include_once QODE_COMPARE_FOR_WOOCOMMERCE_INC_PATH . '/compare/shortcodes/compare-button/class-qode-compare-for-woocommerce-compare-button-shortcode.php';

foreach ( glob( QODE_COMPARE_FOR_WOOCOMMERCE_INC_PATH . '/compare/shortcodes/compare-button/dashboard/*/*.php' ) as $option ) {
	include_once $option;
}
