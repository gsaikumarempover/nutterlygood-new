<?php

if ( ! function_exists( 'greenpath_core_add_team_title_meta_boxes' ) ) {
	/**
	 * Function that add title meta boxes for team module
	 */
	function greenpath_core_add_team_title_meta_boxes( $page, $has_single ) {

		if ( $page && $has_single ) {
			$section = $page->add_section_element(
				array(
					'name'  => 'qodef_team_title_section',
					'title' => esc_html__( 'Title Settings', 'greenpath-core' ),
				)
			);
			$section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_enable_team_title',
					'title'       => esc_html__( 'Enable Title on Team Single', 'greenpath-core' ),
					'description' => esc_html__( 'Use this option to enable/disable team single title', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'yes_no' ),
				)
			);
			$section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_set_team_title_area_in_grid',
					'title'       => esc_html__( 'Team Title in Grid', 'greenpath-core' ),
					'description' => esc_html__( 'Enabling this option will set team title area to be in grid', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'yes_no' ),
				)
			);
		}
	}

	add_action( 'greenpath_core_action_after_team_meta_box_map', 'greenpath_core_add_team_title_meta_boxes', 10, 2 );
}
