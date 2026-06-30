<?php

if ( ! function_exists( 'greenpath_core_add_social_share_variation_list' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_social_share_variation_list( $variations ) {
		$variations['list'] = esc_html__( 'List', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_social_share_layouts', 'greenpath_core_add_social_share_variation_list' );
	add_filter( 'greenpath_core_filter_social_share_layout_options', 'greenpath_core_add_social_share_variation_list' );
}

if ( ! function_exists( 'greenpath_core_set_default_social_share_variation_list' ) ) {
	/**
	 * Function that set default variation layout for this module
	 *
	 * @return string
	 */
	function greenpath_core_set_default_social_share_variation_list() {
		return 'list';
	}

	add_filter( 'greenpath_core_filter_social_share_layout_default_value', 'greenpath_core_set_default_social_share_variation_list' );
}
