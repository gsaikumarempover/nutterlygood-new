<?php

if ( ! function_exists( 'greenpath_core_include_widgets' ) ) {
	/**
	 * Function that includes widgets
	 */
	function greenpath_core_include_widgets() {

		foreach ( glob( GREENPATH_CORE_INC_PATH . '/widgets/*/include.php' ) as $widget ) {
			$is_disabled = greenpath_core_performance_get_option_value( dirname( $widget ), 'greenpath_core_performance_widget_' );

			if ( empty( $is_disabled ) ) {
				include_once $widget;
			}
		}
	}

	add_action( 'qode_framework_action_before_widgets_register', 'greenpath_core_include_widgets' );
}

if ( ! function_exists( 'greenpath_core_register_widgets' ) ) {
	/**
	 * Function that register widgets
	 */
	function greenpath_core_register_widgets() {
		$qode_framework = qode_framework_get_framework_root();
		$widgets        = apply_filters( 'greenpath_core_filter_register_widgets', array() );

		if ( ! empty( $widgets ) ) {
			foreach ( $widgets as $widget ) {
				$qode_framework->add_widget( new $widget() );
			}
		}
	}

	add_action( 'qode_framework_action_before_widgets_register', 'greenpath_core_register_widgets', 11 ); // Priority 11 set because include of files is called on default action 10
}
