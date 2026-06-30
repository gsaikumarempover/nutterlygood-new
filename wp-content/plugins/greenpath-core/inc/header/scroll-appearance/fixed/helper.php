<?php

if ( ! function_exists( 'greenpath_core_add_fixed_header_option' ) ) {
	/**
	 * This function set header scrolling appearance value for global header option map
	 */
	function greenpath_core_add_fixed_header_option( $options ) {
		$options['fixed'] = esc_html__( 'Fixed', 'greenpath-core' );

		return $options;
	}

	add_filter( 'greenpath_core_filter_header_scroll_appearance_option', 'greenpath_core_add_fixed_header_option' );
}
