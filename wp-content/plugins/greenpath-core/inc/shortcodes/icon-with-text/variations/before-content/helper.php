<?php

if ( ! function_exists( 'greenpath_core_add_icon_with_text_variation_before_content' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_icon_with_text_variation_before_content( $variations ) {
		$variations['before-content'] = esc_html__( 'Before Content', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_icon_with_text_layouts', 'greenpath_core_add_icon_with_text_variation_before_content' );
}
