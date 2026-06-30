<?php

if ( ! function_exists( 'greenpath_core_dependency_for_top_area_options' ) ) {
	/**
	 * Function which return dependency values for global module options
	 *
	 * @return array
	 */
	function greenpath_core_dependency_for_top_area_options() {
		return apply_filters( 'greenpath_core_filter_top_area_hide_option', array() );
	}
}

if ( ! function_exists( 'greenpath_core_register_top_area_header_areas' ) ) {
	/**
	 * Function which register widget areas for current module
	 */
	function greenpath_core_register_top_area_header_areas() {
		register_sidebar(
			array(
				'id'            => 'qodef-top-area-left',
				'name'          => esc_html__( 'Header Top Area - Left', 'greenpath-core' ),
				'description'   => esc_html__( 'Widgets added here will appear on the left side in top header area', 'greenpath-core' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s qodef-top-bar-widget">',
				'after_widget'  => '</div>',
			)
		);

		register_sidebar(
			array(
				'id'            => 'qodef-top-area-center',
				'name'          => esc_html__( 'Header Top Area - Center', 'greenpath-core' ),
				'description'   => esc_html__( 'Widgets added here will appear in the center of the top header area', 'greenpath-core' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s qodef-top-bar-widget">',
				'after_widget'  => '</div>',
			)
		);

		register_sidebar(
			array(
				'id'            => 'qodef-top-area-right',
				'name'          => esc_html__( 'Header Top Area - Right', 'greenpath-core' ),
				'description'   => esc_html__( 'Widgets added here will appear on the right side in top header area', 'greenpath-core' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s qodef-top-bar-widget">',
				'after_widget'  => '</div>',
			)
		);
	}

	add_action( 'greenpath_core_action_additional_header_widgets_area', 'greenpath_core_register_top_area_header_areas' );
}

if ( ! function_exists( 'greenpath_core_set_top_area_header_inner_classes' ) ) {
	/**
	 * Function that return classes for top header area
	 * @param array $classes
	 *
	 * @return array
	 */
	function greenpath_core_set_top_area_header_inner_classes( $classes ) {
		$alignment = greenpath_core_get_post_value_through_levels( 'qodef_set_top_area_header_content_alignment' );

		if ( ! empty( $alignment ) ) {
			$classes[] = 'qodef-alignment--' . esc_attr( $alignment );
		}

		return $classes;
	}

	add_filter( 'greenpath_core_filter_top_area_inner_class', 'greenpath_core_set_top_area_header_inner_classes' );
}
