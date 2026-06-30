<?php

if ( ! function_exists( 'greenpath_core_add_clients_list_variation_image_only' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_clients_list_variation_image_only( $variations ) {
		$variations['image-only'] = esc_html__( 'Image Only', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_clients_list_layouts', 'greenpath_core_add_clients_list_variation_image_only' );
}
