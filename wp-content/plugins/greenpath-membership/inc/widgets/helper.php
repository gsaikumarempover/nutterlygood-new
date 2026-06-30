<?php

if ( ! function_exists( 'greenpath_membership_include_widgets' ) ) {
	/**
	 * Function that includes widgets
	 */
	function greenpath_membership_include_widgets() {
		foreach ( glob( GREENPATH_MEMBERSHIP_INC_PATH . '/widgets/*/include.php' ) as $widget ) {
			include_once $widget;
		}
	}

	add_action( 'qode_framework_action_before_widgets_register', 'greenpath_membership_include_widgets' );
}

if ( ! function_exists( 'greenpath_membership_register_widgets' ) ) {
	/**
	 * Function that register widgets
	 */
	function greenpath_membership_register_widgets() {
		$qode_framework = qode_framework_get_framework_root();
		$widgets        = apply_filters( 'greenpath_membership_filter_register_widgets', $widgets = array() );

		if ( ! empty( $widgets ) ) {
			foreach ( $widgets as $widget ) {
				$qode_framework->add_widget( new $widget() );
			}
		}
	}

	add_action( 'qode_framework_action_before_widgets_register', 'greenpath_membership_register_widgets', 11 ); // Priority 11 set because include of files is called on default action 10
}

if ( ! function_exists( 'greenpath_membership_get_button_element' ) ) {
	/**
	 * Function that returns button with provided params
	 *
	 * @param array $params - array of parameters
	 *
	 * @return string - string representing button html
	 */
	function greenpath_membership_get_button_element( $params ) {
		if ( qode_framework_is_installed( 'core' ) && class_exists( 'GreenPathCore_Button_Shortcode' ) ) {
			return GreenPathCore_Button_Shortcode::call_shortcode( $params );
		} else {
			$link   = isset( $params['link'] ) ? $params['link'] : '#';
			$target = isset( $params['target'] ) ? $params['target'] : '_self';
			$text   = isset( $params['text'] ) ? $params['text'] : '';

			return '<a itemprop="url" class="qodef-theme-button" href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '">' . esc_html( $text ) . '</a>';
		}
	}
}
