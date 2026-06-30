<?php

if ( ! function_exists( 'greenpath_core_add_divided_header_options' ) ) {
	/**
	 * Function that add additional header layout options
	 *
	 * @param object $page
	 * @param array  $general_header_tab
	 */
	function greenpath_core_add_divided_header_options( $page, $general_header_tab ) {

		$section = $general_header_tab->add_section_element(
			array(
				'name'       => 'qodef_divided_header_section',
				'title'      => esc_html__( 'Divided Header', 'greenpath-core' ),
				'dependency' => array(
					'show' => array(
						'qodef_header_layout' => array(
							'values'        => 'divided',
							'default_value' => '',
						),
					),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'    => 'yesno',
				'name'          => 'qodef_divided_header_in_grid',
				'title'         => esc_html__( 'Content in Grid', 'greenpath-core' ),
				'description'   => esc_html__( 'Set content to be in grid', 'greenpath-core' ),
				'default_value' => 'no',
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_divided_header_height',
				'title'       => esc_html__( 'Header Height', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header height', 'greenpath-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px', 'greenpath-core' ),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_divided_header_side_padding',
				'title'       => esc_html__( 'Header Side Padding', 'greenpath-core' ),
				'description' => esc_html__( 'Enter side padding for header area', 'greenpath-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px or %', 'greenpath-core' ),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_divided_header_background_color',
				'title'       => esc_html__( 'Header Background Color', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header background color', 'greenpath-core' ),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_divided_header_border_color',
				'title'       => esc_html__( 'Header Border Color', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header border color', 'greenpath-core' ),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_divided_header_border_width',
				'title'       => esc_html__( 'Header Border Width', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header border width size', 'greenpath-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px', 'greenpath-core' ),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_divided_header_border_style',
				'title'       => esc_html__( 'Header Border Style', 'greenpath-core' ),
				'description' => esc_html__( 'Choose header border style', 'greenpath-core' ),
				'options'     => greenpath_core_get_select_type_options_pool( 'border_style' ),
			)
		);
	}

	add_action( 'greenpath_core_action_after_header_options_map', 'greenpath_core_add_divided_header_options', 10, 2 );
}
