<?php

if ( ! function_exists( 'greenpath_core_filter_clients_list_image_only_fade' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_filter_clients_list_image_only_fade( $variations ) {
		$variations['fade'] = esc_html__( 'Fade', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_clients_list_image_only_animation_options', 'greenpath_core_filter_clients_list_image_only_fade' );
}

if ( ! function_exists( 'greenpath_core_set_fade_as_clients_list_image_only_default_animation_option' ) ) {
	/**
	 * Function that add default hover option layout for this layout
	 */
	function greenpath_core_set_fade_as_clients_list_image_only_default_animation_option() {
		return 'fade';
	}

	add_filter( 'greenpath_core_filter_clients_list_image_only_default_animation_option', 'greenpath_core_set_fade_as_clients_list_image_only_default_animation_option' );
}
