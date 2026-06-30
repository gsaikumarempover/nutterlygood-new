<?php

if ( ! function_exists( 'greenpath_core_include_image_sizes' ) ) {
	/**
	 * Function that includes icons
	 */
	function greenpath_core_include_image_sizes() {
		foreach ( glob( GREENPATH_CORE_INC_PATH . '/media/*/include.php' ) as $image_size ) {
			include_once $image_size;
		}
	}

	add_action( 'qode_framework_action_before_images_register', 'greenpath_core_include_image_sizes' );
}
