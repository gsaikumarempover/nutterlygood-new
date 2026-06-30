<?php

if ( ! function_exists( 'greenpath_core_add_button_variation_outlined' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_button_variation_outlined( $variations ) {
		$variations['outlined'] = esc_html__( 'Outlined', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_button_layouts', 'greenpath_core_add_button_variation_outlined' );
}
