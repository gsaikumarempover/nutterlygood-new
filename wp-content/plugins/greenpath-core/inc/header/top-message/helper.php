<?php

if ( ! function_exists( 'greenpath_core_is_top_message_enabled' ) ) {
	/**
	 * Function that check is module enabled
	 *
	 * @return bool
	 */
	function greenpath_core_is_top_message_enabled() {
		return 'no' !== greenpath_core_get_post_value_through_levels( 'qodef_top_message_header' );
	}
}

if ( ! function_exists( 'greenpath_core_add_top_message_to_body_classes' ) ) {
	/**
	 * Function that add additional class name into global class list for body tag
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function greenpath_core_add_top_message_to_body_classes( $classes ) {
		$classes[] = greenpath_core_is_top_message_enabled() ? 'qodef-top-message--enabled' : '';

		return $classes;
	}

	add_filter( 'body_class', 'greenpath_core_add_top_message_to_body_classes' );
}

if ( ! function_exists( 'greenpath_core_load_top_message' ) ) {
	/**
	 * Loads Back To Top HTML
	 */
	function greenpath_core_load_top_message() {

		if ( greenpath_core_is_top_message_enabled() ) {
			greenpath_core_template_part( 'header', 'top-message/templates/top-message' );
		}
	}

	add_action( 'greenpath_action_after_body_tag_open', 'greenpath_core_load_top_message' );
}
