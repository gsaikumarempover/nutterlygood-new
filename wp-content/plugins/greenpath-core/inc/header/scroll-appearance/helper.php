<?php

if ( ! function_exists( 'greenpath_core_dependency_for_scroll_appearance_options' ) ) {
	/**
	 * Function which return dependency values for global module options
	 *
	 * @return array
	 */
	function greenpath_core_dependency_for_scroll_appearance_options() {
		return apply_filters( 'greenpath_core_filter_header_scroll_appearance_hide_option', array() );
	}
}
