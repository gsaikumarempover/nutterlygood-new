<?php

if ( ! function_exists( 'greenpath_core_add_icon_with_text_variation_before_title' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_icon_with_text_variation_before_title( $variations ) {
		$variations['before-title'] = esc_html__( 'Before Title', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_icon_with_text_layouts', 'greenpath_core_add_icon_with_text_variation_before_title' );
}
