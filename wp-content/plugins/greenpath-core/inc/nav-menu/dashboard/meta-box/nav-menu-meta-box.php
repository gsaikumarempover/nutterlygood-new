<?php

if ( ! function_exists( 'greenpath_core_nav_menu_meta_options' ) ) {
	/**
	 * Function that add general options for this module
	 *
	 * @param object $page
	 */
	function greenpath_core_nav_menu_meta_options( $page ) {

		if ( $page ) {

			$section = $page->add_section_element(
				array(
					'name'  => 'qodef_nav_menu_section',
					'title' => esc_html__( 'Main Menu', 'greenpath-core' ),
				)
			);

			$section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_dropdown_top_position',
					'title'       => esc_html__( 'Dropdown Position', 'greenpath-core' ),
					'description' => esc_html__( 'Enter value in percentage of entire header height', 'greenpath-core' ),
				)
			);
		}
	}

	add_action( 'greenpath_core_action_after_page_header_meta_map', 'greenpath_core_nav_menu_meta_options' );
}
