<?php

if ( ! function_exists( 'greenpath_core_add_product_category_list_variation_info_below' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_product_category_list_variation_info_below( $variations ) {
		$variations['info-below'] = esc_html__( 'Info Below', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_product_category_list_layouts', 'greenpath_core_add_product_category_list_variation_info_below' );
}
