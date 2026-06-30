<?php

if ( ! function_exists( 'greenpath_core_set_custom_cursor_icon' ) ) {
	/**
	 * Function that add drag cursor icon into global js object
	 *
	 * @param $array
	 *
	 * @return mixed
	 */
	function greenpath_core_set_custom_cursor_icon( $array ) {
		$array['dragCursor'] = greenpath_core_get_svg_icon( 'drag-cursor' );

		return $array;
	}

	add_filter( 'greenpath_filter_localize_main_js', 'greenpath_core_set_custom_cursor_icon' );
}
