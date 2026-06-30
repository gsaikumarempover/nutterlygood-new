<?php

if ( ! function_exists( 'greenpath_core_add_yith_color_and_label_variations_plugin_add_body_classes' ) ) {
	/**
	 * Function that add additional class name into global class list for body tag
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function greenpath_core_add_yith_color_and_label_variations_plugin_add_body_classes( $classes ) {
		if ( qode_framework_is_installed( 'yith-color-and-label-variations' ) ) {
			$option = greenpath_core_get_post_value_through_levels( 'qodef_enable_woo_yith_color_and_label_variations_predefined_style' );

			if ( 'yes' === $option ) {
				$classes[] = 'qodef-yith-wccl--predefined';
			}
		}
		return $classes;
	}

	add_filter( 'body_class', 'greenpath_core_add_yith_color_and_label_variations_plugin_add_body_classes' );
}
