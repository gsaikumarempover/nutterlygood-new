<?php

if ( ! function_exists( 'greenpath_core_add_team_single_meta_box' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function greenpath_core_add_team_single_meta_box() {
		$qode_framework = qode_framework_get_framework_root();
		$has_single     = greenpath_core_team_has_single();

		$page = $qode_framework->add_options_page(
			array(
				'scope' => array( 'team' ),
				'type'  => 'meta',
				'slug'  => 'team',
				'title' => esc_html__( 'Team Single', 'greenpath-core' ),
			)
		);

		if ( $page ) {
			$section = $page->add_section_element(
				array(
					'name'        => 'qodef_team_general_section',
					'title'       => esc_html__( 'General Settings', 'greenpath-core' ),
					'description' => esc_html__( 'General information about team member.', 'greenpath-core' ),
				)
			);

			if ( $has_single ) {
				$section->add_field_element(
					array(
						'field_type'  => 'select',
						'name'        => 'qodef_team_single_layout',
						'title'       => esc_html__( 'Single Layout', 'greenpath-core' ),
						'description' => esc_html__( 'Choose default layout for team single', 'greenpath-core' ),
						'options'     => array(
							'' => esc_html__( 'Default', 'greenpath-core' ),
						),
					)
				);
			}

			$section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_team_member_role',
					'title'       => esc_html__( 'Role', 'greenpath-core' ),
					'description' => esc_html__( 'Enter team member role', 'greenpath-core' ),
				)
			);

			$social_icons_repeater = $section->add_repeater_element(
				array(
					'name'        => 'qodef_team_member_social_icons',
					'title'       => esc_html__( 'Social Networks', 'greenpath-core' ),
					'description' => esc_html__( 'Populate team member social networks info', 'greenpath-core' ),
					'button_text' => esc_html__( 'Add New Network', 'greenpath-core' ),
				)
			);

			$social_icons_repeater->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_team_member_icon_source',
					'title'         => esc_html__( 'Team Member Icon Source', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'icon_source', false, array( 'predefined' ) ),
					'default_value' => 'svg_path',
				)
			);

			$social_icons_repeater->add_field_element(
				array(
					'field_type' => 'iconpack',
					'name'       => 'qodef_team_member_icon',
					'title'      => esc_html__( 'Icon', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_team_member_icon_source' => array(
								'values'        => 'icon_pack',
								'default_value' => 'svg_path',
							),
						),
					),
				)
			);

			$social_icons_repeater->add_field_element(
				array(
					'field_type'  => 'textarea',
					'name'        => 'qodef_team_member_svg_path',
					'title'       => esc_html__( 'Team Member Icon SVG Path', 'greenpath-core' ),
					'description' => esc_html__( 'Enter your search open icon SVG path here. Please remove version and id attributes from your SVG path because of HTML validation', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_team_member_icon_source' => array(
								'values'        => 'svg_path',
								'default_value' => 'svg_path',
							),
						),
					),
				)
			);

			$social_icons_repeater->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_team_member_icon_link',
					'title'      => esc_html__( 'Icon Link', 'greenpath-core' ),
				)
			);

			$social_icons_repeater->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_team_member_icon_target',
					'title'      => esc_html__( 'Icon Target', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'link_target' ),
				)
			);

			if ( $has_single ) {
				$section->add_field_element(
					array(
						'field_type'  => 'date',
						'name'        => 'qodef_team_member_birth_date',
						'title'       => esc_html__( 'Birth Date', 'greenpath-core' ),
						'description' => esc_html__( 'Enter team member birth date', 'greenpath-core' ),
					)
				);

				$section->add_field_element(
					array(
						'field_type'  => 'text',
						'name'        => 'qodef_team_member_email',
						'title'       => esc_html__( 'E-mail', 'greenpath-core' ),
						'description' => esc_html__( 'Enter team member e-mail address', 'greenpath-core' ),
					)
				);

				$section->add_field_element(
					array(
						'field_type'  => 'text',
						'name'        => 'qodef_team_member_address',
						'title'       => esc_html__( 'Address', 'greenpath-core' ),
						'description' => esc_html__( 'Enter team member address', 'greenpath-core' ),
					)
				);

				$section->add_field_element(
					array(
						'field_type'  => 'text',
						'name'        => 'qodef_team_member_education',
						'title'       => esc_html__( 'Education', 'greenpath-core' ),
						'description' => esc_html__( 'Enter team member education', 'greenpath-core' ),
					)
				);

				$section->add_field_element(
					array(
						'field_type'  => 'file',
						'name'        => 'qodef_team_member_resume',
						'title'       => esc_html__( 'Resume', 'greenpath-core' ),
						'description' => esc_html__( 'Upload team member resume', 'greenpath-core' ),
						'args'        => array(
							'allowed_type' => '[application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document]',
						),
					)
				);
			}

			// Hook to include additional options after module options
			do_action( 'greenpath_core_action_after_team_meta_box_map', $page, $has_single );
		}
	}

	add_action( 'greenpath_core_action_default_meta_boxes_init', 'greenpath_core_add_team_single_meta_box' );
}
