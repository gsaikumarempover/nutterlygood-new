<?php

if ( ! function_exists( 'greenpath_core_add_pricing_table_variation_standard' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_pricing_table_variation_standard( $variations ) {

		$variations['standard'] = esc_html__( 'Standard', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_pricing_table_layouts', 'greenpath_core_add_pricing_table_variation_standard' );
}
