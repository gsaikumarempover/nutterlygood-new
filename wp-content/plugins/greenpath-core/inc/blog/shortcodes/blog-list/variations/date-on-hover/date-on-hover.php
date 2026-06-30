<?php

if ( ! function_exists( 'greenpath_core_add_blog_list_variation_date_on_hover' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_blog_list_variation_date_on_hover( $variations ) {
		$variations['date-on-hover'] = esc_html__( 'Date On Hover', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_blog_list_layouts', 'greenpath_core_add_blog_list_variation_date_on_hover' );
}
