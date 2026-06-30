<?php

if ( ! function_exists( 'greenpath_core_add_lines_spinner_layout_option' ) ) {
	/**
	 * Function that set new value into page spinner layout options map
	 *
	 * @param array $layouts - module layouts
	 *
	 * @return array
	 */
	function greenpath_core_add_lines_spinner_layout_option( $layouts ) {
		$layouts['lines'] = esc_html__( 'Lines', 'greenpath-core' );

		return $layouts;
	}

	add_filter( 'greenpath_core_filter_page_spinner_layout_options', 'greenpath_core_add_lines_spinner_layout_option' );
}
