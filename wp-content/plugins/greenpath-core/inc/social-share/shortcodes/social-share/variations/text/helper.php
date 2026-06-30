<?php

if ( ! function_exists( 'greenpath_core_add_social_share_variation_text' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_social_share_variation_text( $variations ) {
		$variations['text'] = esc_html__( 'Text', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_social_share_layouts', 'greenpath_core_add_social_share_variation_text' );
	add_filter( 'greenpath_core_filter_social_share_layout_options', 'greenpath_core_add_social_share_variation_text' );
}
