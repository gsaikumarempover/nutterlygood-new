<?php

if ( ! function_exists( 'greenpath_core_add_page_spinner_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function greenpath_core_add_page_spinner_options( $page ) {

		if ( $page ) {
			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_page_spinner',
					'title'         => esc_html__( 'Enable Page Spinner', 'greenpath-core' ),
					'description'   => esc_html__( 'Enable Page Spinner Effect', 'greenpath-core' ),
					'default_value' => 'no',
				)
			);

			$spinner_section = $page->add_section_element(
				array(
					'name'       => 'qodef_page_spinner_section',
					'title'      => esc_html__( 'Page Spinner Section', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_enable_page_spinner' => array(
								'values'        => 'yes',
								'default_value' => 'no',
							),
						),
					),
				)
			);

			$spinner_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_page_spinner_type',
					'title'         => esc_html__( 'Select Page Spinner Type', 'greenpath-core' ),
					'description'   => esc_html__( 'Choose a page spinner animation style', 'greenpath-core' ),
					'options'       => apply_filters( 'greenpath_core_filter_page_spinner_layout_options', array() ),
					'default_value' => apply_filters( 'greenpath_core_filter_page_spinner_default_layout_option', '' ),
				)
			);

			$spinner_section->add_field_element(
				array(
					'field_type'  => 'color',
					'name'        => 'qodef_page_spinner_background_color',
					'title'       => esc_html__( 'Spinner Background Color', 'greenpath-core' ),
					'description' => esc_html__( 'Choose the spinner background color', 'greenpath-core' ),
				)
			);

			$spinner_section->add_field_element(
				array(
					'field_type' => 'image',
					'name'       => 'qodef_spinner_background_image',
					'title'      => esc_html__( 'Upload Spinner Background Image', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_page_spinner_type' => array(
								'values'        => array(
									'predefined',
								),
								'default_value' => '',
							),
						),
					),
				)
			);

			$spinner_section->add_field_element(
				array(
					'field_type' => 'textarea',
					'name'       => 'qodef_spinner_svg',
					'title'      => esc_html__( 'Upload Spinner Svg', 'mien-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_page_spinner_type' => array(
								'values'        => array(
									'predefined',
								),
								'default_value' => '',
							),
						),
					),
				)
			);

			$spinner_section->add_field_element(
				array(
					'field_type'  => 'color',
					'name'        => 'qodef_page_spinner_color',
					'title'       => esc_html__( 'Spinner Color', 'greenpath-core' ),
					'description' => esc_html__( 'Choose the spinner color', 'greenpath-core' ),
				)
			);

			$spinner_section->add_field_element(
				array(
					'field_type'    => 'text',
					'name'          => 'qodef_page_spinner_text',
					'title'         => esc_html__( 'Spinner Text', 'greenpath-core' ),
					'description'   => esc_html__( 'Enter the spinner text', 'greenpath-core' ),
					'default_value' => 'greenpath',
					'dependency'    => array(
						'show' => array(
							'qodef_page_spinner_type' => array(
								'values'        => 'textual',
								'default_value' => '',
							),
						),
					),
				)
			);

			$spinner_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_page_spinner_fade_out_animation',
					'title'         => esc_html__( 'Enable Fade Out Animation', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will turn on fade out animation when leaving page', 'greenpath-core' ),
					'default_value' => 'no',
				)
			);
		}
	}

	add_action( 'greenpath_core_action_after_general_options_map', 'greenpath_core_add_page_spinner_options' );
}
