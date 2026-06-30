<?php

if ( ! function_exists( 'greenpath_core_add_team_archive_sidebar_options' ) ) {
	/**
	 * Function that add sidebar options for team archive module
	 */
	function greenpath_core_add_team_archive_sidebar_options( $tab ) {

		if ( $tab ) {
			$tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_team_archive_sidebar_layout',
					'title'         => esc_html__( 'Sidebar Layout', 'greenpath-core' ),
					'description'   => esc_html__( 'Choose default sidebar layout for team archives', 'greenpath-core' ),
					'default_value' => 'no-sidebar',
					'options'       => greenpath_core_get_select_type_options_pool( 'sidebar_layouts', false ),
				)
			);

			$custom_sidebars = greenpath_core_get_custom_sidebars();
			if ( ! empty( $custom_sidebars ) && count( $custom_sidebars ) > 1 ) {
				$tab->add_field_element(
					array(
						'field_type'  => 'select',
						'name'        => 'qodef_team_archive_custom_sidebar',
						'title'       => esc_html__( 'Custom Sidebar', 'greenpath-core' ),
						'description' => esc_html__( 'Choose a custom sidebar to display on team archives', 'greenpath-core' ),
						'options'     => $custom_sidebars,
					)
				);
			}

			$tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_team_archive_sidebar_grid_gutter',
					'title'       => esc_html__( 'Set Grid Gutter', 'greenpath-core' ),
					'description' => esc_html__( 'Choose grid gutter size to set space between content and sidebar', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'items_space' ),
				)
			);

			$team_archive_sidebar_grid_gutter_row = $tab->add_row_element(
				array(
					'name'       => 'qodef_team_archive_sidebar_grid_gutter_row',
					'dependency' => array(
						'show' => array(
							'qodef_team_archive_sidebar_grid_gutter' => array(
								'values'        => 'custom',
								'default_value' => '',
							),
						),
					),
				)
			);

			$team_archive_sidebar_grid_gutter_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_team_archive_sidebar_grid_gutter_custom',
					'title'       => esc_html__( 'Custom Grid Gutter', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$team_archive_sidebar_grid_gutter_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_team_archive_sidebar_grid_gutter_custom_1512',
					'title'       => esc_html__( 'Custom Grid Gutter - 1512', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 1512px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$team_archive_sidebar_grid_gutter_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_team_archive_sidebar_grid_gutter_custom_1200',
					'title'       => esc_html__( 'Custom Grid Gutter - 1200', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 1200px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$team_archive_sidebar_grid_gutter_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_team_archive_sidebar_grid_gutter_custom_880',
					'title'       => esc_html__( 'Custom Grid Gutter - 880', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 880px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);
		}
	}

	add_action( 'greenpath_core_action_after_team_options_archive', 'greenpath_core_add_team_archive_sidebar_options' );
}

if ( ! function_exists( 'greenpath_core_add_team_single_sidebar_options' ) ) {
	/**
	 * Function that add sidebar options for team single module
	 */
	function greenpath_core_add_team_single_sidebar_options( $tab ) {

		if ( $tab ) {
			$tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_team_single_sidebar_layout',
					'title'         => esc_html__( 'Sidebar Layout', 'greenpath-core' ),
					'description'   => esc_html__( 'Choose default sidebar layout for team singles', 'greenpath-core' ),
					'default_value' => 'no-sidebar',
					'options'       => greenpath_core_get_select_type_options_pool( 'sidebar_layouts', false ),
				)
			);

			$custom_sidebars = greenpath_core_get_custom_sidebars();
			if ( ! empty( $custom_sidebars ) && count( $custom_sidebars ) > 1 ) {
				$tab->add_field_element(
					array(
						'field_type'  => 'select',
						'name'        => 'qodef_team_single_custom_sidebar',
						'title'       => esc_html__( 'Custom Sidebar', 'greenpath-core' ),
						'description' => esc_html__( 'Choose a custom sidebar to display on team singles', 'greenpath-core' ),
						'options'     => $custom_sidebars,
					)
				);
			}

			$tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_team_single_sidebar_grid_gutter',
					'title'       => esc_html__( 'Set Grid Gutter', 'greenpath-core' ),
					'description' => esc_html__( 'Choose grid gutter size to set space between content and sidebar', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'items_space' ),
				)
			);

			$team_single_sidebar_grid_gutter_row = $tab->add_row_element(
				array(
					'name'       => 'qodef_team_single_sidebar_grid_gutter_row',
					'dependency' => array(
						'show' => array(
							'qodef_team_single_sidebar_grid_gutter' => array(
								'values'        => 'custom',
								'default_value' => '',
							),
						),
					),
				)
			);

			$team_single_sidebar_grid_gutter_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_team_single_sidebar_grid_gutter_custom',
					'title'       => esc_html__( 'Custom Grid Gutter', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$team_single_sidebar_grid_gutter_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_team_single_sidebar_grid_gutter_custom_1512',
					'title'       => esc_html__( 'Custom Grid Gutter - 1512', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 1512px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$team_single_sidebar_grid_gutter_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_team_single_sidebar_grid_gutter_custom_1200',
					'title'       => esc_html__( 'Custom Grid Gutter - 1200', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 1200px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$team_single_sidebar_grid_gutter_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_team_single_sidebar_grid_gutter_custom_880',
					'title'       => esc_html__( 'Custom Grid Gutter - 880', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 880px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);
		}
	}

	add_action( 'greenpath_core_action_after_team_options_single', 'greenpath_core_add_team_single_sidebar_options' );
}
