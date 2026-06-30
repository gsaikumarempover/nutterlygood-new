<?php

if ( ! function_exists( 'greenpath_core_add_product_category_list_variation_info_on_image' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_product_category_list_variation_info_on_image( $variations ) {
		$variations['info-on-image'] = esc_html__( 'Info On Image', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_product_category_list_layouts', 'greenpath_core_add_product_category_list_variation_info_on_image' );
}
