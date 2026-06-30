<?php

if ( ! function_exists( 'greenpath_core_add_countdown_variation_simple' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_countdown_variation_simple( $variations ) {
		$variations['simple'] = esc_html__( 'Simple', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_countdown_layouts', 'greenpath_core_add_countdown_variation_simple' );
}
