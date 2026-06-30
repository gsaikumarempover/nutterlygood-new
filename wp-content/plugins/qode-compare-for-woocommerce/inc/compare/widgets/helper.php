<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_compare_for_woocommerce_include_widgets' ) ) {
	/**
	 * Function that includes widgets
	 */
	function qode_compare_for_woocommerce_include_widgets() {

		foreach ( glob( QODE_COMPARE_FOR_WOOCOMMERCE_INC_PATH . '/compare/widgets/*/include.php' ) as $widget ) {
			include_once $widget;
		}
	}

	add_action( 'qode_compare_for_woocommerce_action_framework_before_widgets_register', 'qode_compare_for_woocommerce_include_widgets' );
}

if ( ! function_exists( 'qode_compare_for_woocommerce_register_widgets' ) ) {
	/**
	 * Function that register widgets
	 */
	function qode_compare_for_woocommerce_register_widgets() {
		$qode_framework = qode_compare_for_woocommerce_framework_get_framework_root();
		$widgets        = apply_filters( 'qode_compare_for_woocommerce_filter_register_widgets', array() );

		if ( ! empty( $widgets ) ) {
			foreach ( $widgets as $widget ) {
				$qode_framework->add_widget( new $widget() );
			}
		}
	}

	// Priority 11 set because include of files is called on default action 10.
	add_action( 'qode_compare_for_woocommerce_action_framework_before_widgets_register', 'qode_compare_for_woocommerce_register_widgets', 11 );
}
