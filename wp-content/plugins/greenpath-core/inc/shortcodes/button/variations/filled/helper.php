<?php

if ( ! function_exists( 'greenpath_core_add_button_variation_filled' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_button_variation_filled( $variations ) {
		$variations['filled'] = esc_html__( 'Filled', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_button_layouts', 'greenpath_core_add_button_variation_filled' );
}
