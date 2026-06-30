<?php

if ( ! function_exists( 'greenpath_core_add_standard_extended_header_options' ) ) {
	/**
	 * Function that add additional header layout options
	 *
	 * @param object $page
	 * @param array $general_header_tab
	 */
	function greenpath_core_add_standard_extended_header_options( $page, $general_header_tab ) {

		$section = $general_header_tab->add_section_element(
			array(
				'name'        => 'qodef_standard_extended_header_section',
				'title'       => esc_html__( 'Standard Extended Header', 'greenpath-core' ),
				'description' => esc_html__( 'Standard extended header settings', 'greenpath-core' ),
				'dependency'  => array(
					'show' => array(
						'qodef_header_layout' => array(
							'values'        => 'standard-extended',
							'default_value' => '',
						),
					),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_standard_extended_header_height',
				'title'       => esc_html__( 'Header Height', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header height', 'greenpath-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px', 'greenpath-core' ),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'    => 'yesno',
				'name'          => 'qodef_standard_extended_header_in_grid',
				'title'         => esc_html__( 'Content in Grid', 'greenpath-core' ),
				'description'   => esc_html__( 'Set content to be in grid', 'greenpath-core' ),
				'default_value' => 'no',
			)
		);

		$header_dropdown_section = $section->add_section_element(
			array(
				'name'        => 'qodef_standard_extended_header_dropdown_section',
				'title'       => esc_html__( 'Standard Extended Dropdown', 'greenpath-core' ),
				'description' => esc_html__( 'Standard extended header dropdown settings', 'greenpath-core' ),
				'dependency'  => array(
					'show' => array(
						'qodef_header_layout' => array(
							'values'        => 'standard-extended',
							'default_value' => '',
						),
					),
				),
			)
		);

		$header_dropdown_section->add_field_element(
			array(
				'field_type'    => 'select',
				'name'          => 'qodef_standard_extended_show_extended_dropdown',
				'title'         => esc_html__( 'Show Extended Dropdown', 'greenpath-core' ),
				'options'       => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
				'default_value' => 'yes',
			)
		);

		$header_dropdown_section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_standard_extended_extended_dropdown_opener_label',
				'title'       => esc_html__( 'Extended Dropdown Opener Label', 'greenpath-core' ),
				'description' => esc_html__( 'Set Extended Dropdown Opener Label, or leave empty for default value.', 'greenpath-core' ),
				'dependency'  => array(
					'show' => array(
						'qodef_standard_extended_show_extended_dropdown' => array(
							'values'        => 'yes',
							'default_value' => '',
						),
					),
				),
			)
		);

		$header_dropdown_section->add_field_element(
			array(
				'field_type'    => 'select',
				'name'          => 'qodef_standard_extended_hide_label',
				'title'         => esc_html__( 'Hide Extended Dropdown Label', 'greenpath-core' ),
				'description'   => esc_html__( 'Display only opener icon for Extended Dropdown', 'greenpath-core' ),
				'options'       => greenpath_core_get_select_type_options_pool( 'no_yes', false ),
				'default_value' => 'no',
			)
		);

		$header_dropdown_section->add_field_element(
			array(
				'field_type'    => 'select',
				'name'          => 'qodef_standard_extended_dropdown_always_opened',
				'default_value' => 'no',
				'title'         => esc_html__( 'Extended Dropdown Always Open', 'greenpath-core' ),
				'description'   => esc_html__( 'Set Extended Dropdown to always be opened', 'greenpath-core' ),
				'options'       => greenpath_core_get_select_type_options_pool( 'no_yes', false ),
			)
		);

		$header_top_section = $section->add_section_element(
			array(
				'name'        => 'qodef_standard_extended_header_top_section',
				'title'       => esc_html__( 'Standard Extended Header Top', 'greenpath-core' ),
				'description' => esc_html__( 'Standard extended header top section settings', 'greenpath-core' ),
				'dependency'  => array(
					'show' => array(
						'qodef_header_layout' => array(
							'values'        => 'standard-extended',
							'default_value' => '',
						),
					),
				),
			)
		);

		$header_top_section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_standard_extended_header_top_height',
				'title'       => esc_html__( 'Header Top Height', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header top area height', 'greenpath-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px', 'greenpath-core' ),
				),
			)
		);

		$header_top_section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_standard_extended_header_top_side_padding',
				'title'       => esc_html__( 'Header Top Side Padding', 'greenpath-core' ),
				'description' => esc_html__( 'Enter side padding for top header area', 'greenpath-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px or %', 'greenpath-core' ),
				),
			)
		);

		$header_top_section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_standard_extended_header_top_background_color',
				'title'       => esc_html__( 'Header Top Background Color', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header top background color', 'greenpath-core' ),
			)
		);

		$header_top_section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_standard_extended_header_top_border_color',
				'title'       => esc_html__( 'Header Top Border Color', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header top border color', 'greenpath-core' ),
			)
		);

		$header_top_section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_standard_extended_header_top_border_width',
				'title'       => esc_html__( 'Header Top Border Width', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header top border width size', 'greenpath-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px', 'greenpath-core' ),
				),
			)
		);

		$header_top_section->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_standard_extended_header_top_border_style',
				'title'       => esc_html__( 'Header Top Border Style', 'greenpath-core' ),
				'description' => esc_html__( 'Choose header top border style', 'greenpath-core' ),
				'options'     => greenpath_core_get_select_type_options_pool( 'border_style' ),
			)
		);

		$header_bottom_section = $section->add_section_element(
			array(
				'name'        => 'qodef_standard_extended_header_bottom_section',
				'title'       => esc_html__( 'Standard Extended Header Bottom', 'greenpath-core' ),
				'description' => esc_html__( 'Standard extended header bottom section settings', 'greenpath-core' ),
				'dependency'  => array(
					'show' => array(
						'qodef_header_layout' => array(
							'values'        => 'standard-extended',
							'default_value' => '',
						),
					),
				),
			)
		);

		$header_bottom_section->add_field_element(
			array(
				'field_type' => 'select',
				'name'       => 'qodef_standard_extended_bottom_skin',
				'title'      => esc_html__( 'Extended Header Bottom Skin', 'greenpath-core' ),
				'options'    => greenpath_core_get_select_type_options_pool( 'header_skin', false ),
				'dependency'    => array(
					'show' => array(
						'qodef_header_layout' => array(
							'values'        => array( 'standard-extended' ),
							'default_value' => ''
						)
					)
				)
			)
		);

		$header_bottom_section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_standard_extended_header_bottom_side_padding',
				'title'       => esc_html__( 'Header Bottom Side Padding', 'greenpath-core' ),
				'description' => esc_html__( 'Enter side padding for bottom header area', 'greenpath-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px or %', 'greenpath-core' ),
				),
			)
		);

		$header_bottom_section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_standard_extended_header_bottom_background_color',
				'title'       => esc_html__( 'Header Bottom Background Color', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header bottom background color', 'greenpath-core' ),
			)
		);

		$header_bottom_section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_standard_extended_header_bottom_border_color',
				'title'       => esc_html__( 'Header Bottom Border Color', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header bottom border color', 'greenpath-core' ),
			)
		);

		$header_bottom_section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_standard_extended_header_bottom_border_width',
				'title'       => esc_html__( 'Header Bottom Border Width', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header bottom border width size', 'greenpath-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px', 'greenpath-core' ),
				),
			)
		);

		$header_bottom_section->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_standard_extended_header_bottom_border_style',
				'title'       => esc_html__( 'Header Bottom Border Style', 'greenpath-core' ),
				'description' => esc_html__( 'Choose header bottom border style', 'greenpath-core' ),
				'options'     => greenpath_core_get_select_type_options_pool( 'border_style' ),
			)
		);
	}

	add_action( 'greenpath_core_action_after_header_options_map', 'greenpath_core_add_standard_extended_header_options', 10, 2 );
}
