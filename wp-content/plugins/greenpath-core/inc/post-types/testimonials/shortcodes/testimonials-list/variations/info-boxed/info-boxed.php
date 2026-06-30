<?php

if ( ! function_exists( 'greenpath_core_add_testimonials_list_variation_info_boxed' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_testimonials_list_variation_info_boxed( $variations ) {
		$variations['info-boxed'] = esc_html__( 'Info Boxed', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_testimonials_list_layouts', 'greenpath_core_add_testimonials_list_variation_info_boxed' );
}
