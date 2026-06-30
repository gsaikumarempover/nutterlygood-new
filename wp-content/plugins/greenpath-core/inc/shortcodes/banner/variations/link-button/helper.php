<?php

if ( ! function_exists( 'greenpath_core_add_banner_variation_link_button' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_banner_variation_link_button( $variations ) {
		$variations['link-button'] = esc_html__( 'Link Button', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_banner_layouts', 'greenpath_core_add_banner_variation_link_button' );
}
