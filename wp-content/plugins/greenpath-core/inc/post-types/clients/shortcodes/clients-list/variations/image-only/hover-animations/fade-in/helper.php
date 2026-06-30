<?php

if ( ! function_exists( 'greenpath_core_filter_clients_list_image_only_fade_in' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_filter_clients_list_image_only_fade_in( $variations ) {
		$variations['fade-in'] = esc_html__( 'Fade In', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_clients_list_image_only_animation_options', 'greenpath_core_filter_clients_list_image_only_fade_in' );
}
