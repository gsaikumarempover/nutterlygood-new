<?php

if ( ! function_exists( 'greenpath_core_register_standard_title_layout' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $layouts
	 *
	 * @return array
	 */
	function greenpath_core_register_standard_title_layout( $layouts ) {
		$layouts['standard'] = 'GreenPathCore_Standard_Title';

		return $layouts;
	}

	add_filter( 'greenpath_core_filter_register_title_layouts', 'greenpath_core_register_standard_title_layout' );
}

if ( ! function_exists( 'greenpath_core_add_standard_title_layout_option' ) ) {
	/**
	 * Function that set new value into title layout options map
	 *
	 * @param array $layouts - module layouts
	 *
	 * @return array
	 */
	function greenpath_core_add_standard_title_layout_option( $layouts ) {
		$layouts['standard'] = esc_html__( 'Standard', 'greenpath-core' );

		return $layouts;
	}

	add_filter( 'greenpath_core_filter_title_layout_options', 'greenpath_core_add_standard_title_layout_option' );
}

if ( ! function_exists( 'greenpath_core_get_standard_title_layout_subtitle_text' ) ) {
	/**
	 * Function that render current page subtitle text
	 */
	function greenpath_core_get_standard_title_layout_subtitle_text() {
		$subtitle_meta = greenpath_core_get_post_value_through_levels( 'qodef_page_title_subtitle' );
		$subtitle      = array( 'subtitle' => ! empty( $subtitle_meta ) ? $subtitle_meta : '' );

		return apply_filters( 'greenpath_core_filter_standard_title_layout_subtitle_text', $subtitle );
	}
}
