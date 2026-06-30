<?php

if ( ! function_exists( 'greenpath_core_add_social_share_variation_dropdown' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_social_share_variation_dropdown( $variations ) {
		$variations['dropdown'] = esc_html__( 'Dropdown', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_social_share_layouts', 'greenpath_core_add_social_share_variation_dropdown' );
	add_filter( 'greenpath_core_filter_social_share_layout_options', 'greenpath_core_add_social_share_variation_dropdown' );
}
