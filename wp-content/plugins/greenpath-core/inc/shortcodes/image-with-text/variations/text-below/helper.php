<?php

if ( ! function_exists( 'greenpath_core_add_image_with_text_variation_text_below' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_image_with_text_variation_text_below( $variations ) {
		$variations['text-below'] = esc_html__( 'Text Below', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_image_with_text_layouts', 'greenpath_core_add_image_with_text_variation_text_below' );
}
