<?php

if ( ! function_exists( 'greenpath_core_add_standard_header_meta' ) ) {
	/**
	 * Function that add additional header layout meta box options
	 *
	 * @param object $page
	 */
	function greenpath_core_add_standard_header_meta( $page ) {
		$section = $page->add_section_element(
			array(
				'name'       => 'qodef_standard_header_section',
				'title'      => esc_html__( 'Standard Header', 'greenpath-core' ),
				'dependency' => array(
					'show' => array(
						'qodef_header_layout' => array(
							'values'        => array( '', 'standard' ),
							'default_value' => '',
						),
					),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'    => 'select',
				'name'          => 'qodef_standard_header_in_grid',
				'title'         => esc_html__( 'Content in Grid', 'greenpath-core' ),
				'description'   => esc_html__( 'Set content to be in grid', 'greenpath-core' ),
				'default_value' => '',
				'options'       => greenpath_core_get_select_type_options_pool( 'no_yes' ),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_standard_header_height',
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
				'name'        => 'qodef_standard_header_side_padding',
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
				'name'        => 'qodef_standard_header_background_color',
				'title'       => esc_html__( 'Header Background Color', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header background color', 'greenpath-core' ),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_standard_header_border_color',
				'title'       => esc_html__( 'Header Border Color', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header border color', 'greenpath-core' ),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_standard_header_border_width',
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
				'name'        => 'qodef_standard_header_border_style',
				'title'       => esc_html__( 'Header Border Style', 'greenpath-core' ),
				'description' => esc_html__( 'Choose header border style', 'greenpath-core' ),
				'options'     => greenpath_core_get_select_type_options_pool( 'border_style' ),
			)
		);

		$section->add_field_element(
			array(
				'field_type'    => 'select',
				'name'          => 'qodef_standard_header_menu_position',
				'title'         => esc_html__( 'Menu position', 'greenpath-core' ),
				'default_value' => '',
				'options'       => array(
					''       => esc_html__( 'Default', 'greenpath-core' ),
					'left'   => esc_html__( 'Left', 'greenpath-core' ),
					'center' => esc_html__( 'Center', 'greenpath-core' ),
					'right'  => esc_html__( 'Right', 'greenpath-core' ),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_standard_header_widget_spacing',
				'title'       => esc_html__( 'Header Widget Spacing', 'greenpath-core' ),
				'description' => esc_html__( 'Enter header widget spacing', 'greenpath-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px', 'greenpath-core' ),
				),
			)
		);
	}

	add_action( 'greenpath_core_action_after_page_header_meta_map', 'greenpath_core_add_standard_header_meta' );
}
