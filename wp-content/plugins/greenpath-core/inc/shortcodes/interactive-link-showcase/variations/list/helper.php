<?php

if ( ! function_exists( 'greenpath_core_add_interactive_link_showcase_variation_list' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_interactive_link_showcase_variation_list( $variations ) {
		$variations['list'] = esc_html__( 'List', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_interactive_link_showcase_layouts', 'greenpath_core_add_interactive_link_showcase_variation_list' );
}
