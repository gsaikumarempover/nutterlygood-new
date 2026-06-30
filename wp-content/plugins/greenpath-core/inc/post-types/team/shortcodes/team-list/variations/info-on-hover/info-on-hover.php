<?php

if ( ! function_exists( 'greenpath_core_add_team_list_variation_info_on_hover' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_team_list_variation_info_on_hover( $variations ) {
		$variations['info-on-hover'] = esc_html__( 'Info on Hover', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_team_list_layouts', 'greenpath_core_add_team_list_variation_info_on_hover' );
}
