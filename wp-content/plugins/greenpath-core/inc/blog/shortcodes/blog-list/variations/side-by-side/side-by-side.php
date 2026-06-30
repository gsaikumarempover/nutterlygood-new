<?php

if ( ! function_exists( 'greenpath_core_add_blog_list_variation_side_by_side' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_blog_list_variation_side_by_side( $variations ) {
		$variations['side-by-side'] = esc_html__( 'Side By Side', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_blog_list_layouts', 'greenpath_core_add_blog_list_variation_side_by_side' );
	add_filter( 'greenpath_core_filter_simple_blog_list_widget_layouts', 'greenpath_core_add_blog_list_variation_side_by_side' );
}
