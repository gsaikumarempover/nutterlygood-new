<?php

if ( ! function_exists( 'greenpath_core_add_image_marquee_variation_default' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_image_marquee_variation_default( $variations ) {
		$variations['default'] = esc_html__( 'Default', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_image_marquee_layouts', 'greenpath_core_add_image_marquee_variation_default' );
}
