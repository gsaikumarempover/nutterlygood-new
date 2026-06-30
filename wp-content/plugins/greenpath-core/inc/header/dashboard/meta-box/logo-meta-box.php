<?php

if ( ! function_exists( 'greenpath_core_add_page_logo_meta_box' ) ) {
	/**
	 * Function that add general meta box options for this module
	 *
	 * @param object $page
	 */
	function greenpath_core_add_page_logo_meta_box( $page ) {

		if ( $page ) {

			$logo_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-logo',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'Logo Settings', 'greenpath-core' ),
					'description' => esc_html__( 'Logo settings', 'greenpath-core' ),
				)
			);

			$header_logo_section = $logo_tab->add_section_element(
				array(
					'name'  => 'qodef_header_logo_section',
					'title' => esc_html__( 'Header Logo Options', 'greenpath-core' ),
				)
			);

			$header_logo_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_logo_height',
					'title'       => esc_html__( 'Logo Height', 'greenpath-core' ),
					'description' => esc_html__( 'Enter logo height', 'greenpath-core' ),
					'args'        => array(
						'suffix' => esc_html__( 'px', 'greenpath-core' ),
					),
				)
			);

			$header_logo_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_logo_padding',
					'title'       => esc_html__( 'Logo Padding', 'greenpath-core' ),
					'description' => esc_html__( 'Enter logo padding value (top right bottom left)', 'greenpath-core' ),
				)
			);

			$header_logo_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_logo_overflow',
					'title'         => esc_html__( 'Enable Logo Overflow', 'greenpath-core' ),
					'description'   => esc_html__( 'This option allows the Logo image to overflow outside of the Header area', 'greenpath-core' ),
					'default_value' => 'no',
					'dependency'    => array(
						'hide' => array(
							'qodef_header_layout' => array(
								'values'        => 'standard-extended',
								'default_value' => '',
							),
						),
					),
				)
			);

			$header_logo_section->add_field_element(
				array(
					'field_type'    => 'text',
					'name'          => 'qodef_logo_top_margin',
					'title'         => esc_html__( 'Logo Top Margin', 'greenpath-core' ),
					'description'   => esc_html__( 'Sets a top margin on the Logo image (in px)', 'greenpath-core' ),
					'default_value' => '',
					'dependency'    => array(
						'show' => array(
							'qodef_enable_logo_overflow' => array(
								'values'        => 'yes',
								'default_value' => '',
							),
						),
					),
				)
			);

			$header_logo_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_logo_source',
					'title'         => esc_html__( 'Logo Source', 'greenpath-core' ),
					'options'       => array(
						''         => esc_html__( 'Default', 'greenpath-core' ),
						'image'    => esc_html__( 'Image', 'greenpath-core' ),
						'svg-path' => esc_html__( 'SVG Path', 'greenpath-core' ),
						'textual'  => esc_html__( 'Textual', 'greenpath-core' ),
					),
					'default_value' => '',
				)
			);

			$logo_image_section = $header_logo_section->add_section_element(
				array(
					'title'      => esc_html__( 'Image settings', 'greenpath-core' ),
					'name'       => 'qodef_logo_image_section',
					'dependency' => array(
						'show' => array(
							'qodef_logo_source' => array(
								'values'        => 'image',
								'default_value' => '',
							),
						),
					),
				)
			);

			$logo_image_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'        => 'qodef_logo_predefined_icon',
					'title'       => esc_html__( 'Logo - Predefined animated icon before logo', 'greenpath-core' ),
				)
			);

			$logo_image_section->add_field_element(
				array(
					'field_type'  => 'image',
					'name'        => 'qodef_logo_main',
					'title'       => esc_html__( 'Logo - Main', 'greenpath-core' ),
					'description' => esc_html__( 'Choose main logo image', 'greenpath-core' ),
					'multiple'    => 'no',
				)
			);

			$logo_image_section->add_field_element(
				array(
					'field_type'  => 'image',
					'name'        => 'qodef_logo_dark',
					'title'       => esc_html__( 'Logo - Dark', 'greenpath-core' ),
					'description' => esc_html__( 'Choose dark logo image', 'greenpath-core' ),
					'multiple'    => 'no',
				)
			);

			$logo_image_section->add_field_element(
				array(
					'field_type'  => 'image',
					'name'        => 'qodef_logo_light',
					'title'       => esc_html__( 'Logo - Light', 'greenpath-core' ),
					'description' => esc_html__( 'Choose light logo image', 'greenpath-core' ),
					'multiple'    => 'no',
				)
			);

			// Hook to include additional options after section part
			do_action( 'greenpath_core_action_after_header_logo_image_section_meta_map', $logo_tab, $header_logo_section, $logo_image_section );

			$logo_svg_path_section = $header_logo_section->add_section_element(
				array(
					'title'      => esc_html__( 'SVG settings', 'greenpath-core' ),
					'name'       => 'qodef_logo_svg_path_section',
					'dependency' => array(
						'show' => array(
							'qodef_logo_source' => array(
								'values'        => 'svg-path',
								'default_value' => '',
							),
						),
					),
				)
			);

			$logo_svg_path_section->add_field_element(
				array(
					'field_type'  => 'textarea',
					'name'        => 'qodef_logo_svg_path',
					'title'       => esc_html__( 'Logo SVG Path', 'greenpath-core' ),
					'description' => esc_html__( 'Enter your logo icon SVG path here. Please remove version and id attributes from your SVG path because of HTML validation', 'greenpath-core' ),
				)
			);

			// Hook to include additional options before section part
			do_action( 'greenpath_core_action_before_header_logo_svg_path_section_meta_map', $logo_tab, $header_logo_section, $logo_svg_path_section );

			$logo_svg_path_section_row = $logo_svg_path_section->add_row_element(
				array(
					'name'  => 'qodef_logo_svg_path_section_row',
					'title' => esc_html__( 'SVG Styles', 'greenpath-core' ),
				)
			);

			$logo_svg_path_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_logo_svg_path_color',
					'title'      => esc_html__( 'Color', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$logo_svg_path_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_logo_svg_path_hover_color',
					'title'      => esc_html__( 'Hover Color', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$logo_svg_path_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_logo_svg_path_size',
					'title'      => esc_html__( 'SVG Icon Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			// Hook to include additional options after section part
			do_action( 'greenpath_core_action_after_header_logo_svg_path_section_meta_map', $logo_tab, $header_logo_section, $logo_svg_path_section );

			$logo_textual_section = $header_logo_section->add_section_element(
				array(
					'title'      => esc_html__( 'Textual settings', 'greenpath-core' ),
					'name'       => 'qodef_logo_textual_section',
					'dependency' => array(
						'show' => array(
							'qodef_logo_source' => array(
								'values'        => 'textual',
								'default_value' => '',
							),
						),
					),
				)
			);

			$logo_textual_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_logo_text',
					'title'       => esc_html__( 'Logo Text', 'greenpath-core' ),
					'description' => esc_html__( 'Fill your text to be as Logo image', 'greenpath-core' ),
				)
			);

			// Hook to include additional options before section part
			do_action( 'greenpath_core_action_before_header_logo_textual_section_meta_map', $logo_tab, $header_logo_section, $logo_textual_section );

			$logo_textual_section_row = $logo_textual_section->add_row_element(
				array(
					'name'  => 'qodef_logo_textual_section_row',
					'title' => esc_html__( 'Typography Styles', 'greenpath-core' ),
				)
			);

			$logo_textual_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_logo_text_color',
					'title'      => esc_html__( 'Color', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$logo_textual_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_logo_text_hover_color',
					'title'      => esc_html__( 'Hover Color', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$logo_textual_section_row->add_field_element(
				array(
					'field_type' => 'font',
					'name'       => 'qodef_logo_text_font_family',
					'title'      => esc_html__( 'Font Family', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$logo_textual_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_logo_text_font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$logo_textual_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_logo_text_line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$logo_textual_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_logo_text_letter_spacing',
					'title'      => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$logo_textual_section_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_logo_text_font_weight',
					'title'      => esc_html__( 'Font Weight', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'font_weight' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$logo_textual_section_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_logo_text_text_transform',
					'title'      => esc_html__( 'Text Transform', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_transform' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$logo_textual_section_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_logo_text_font_style',
					'title'      => esc_html__( 'Font Style', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'font_style' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$logo_textual_section_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_logo_text_text_decoration',
					'title'      => esc_html__( 'Text Decoration', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_decoration' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$logo_textual_section_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_logo_text_hover_text_decoration',
					'title'      => esc_html__( 'Hover Text Decoration', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_decoration' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			// Hook to include additional options after section part
			do_action( 'greenpath_core_action_after_header_logo_textual_section_meta_map', $logo_tab, $header_logo_section, $logo_textual_section );

			// Hook to include additional options after module options
			do_action( 'greenpath_core_action_after_page_logo_meta_map', $logo_tab, $header_logo_section );
		}
	}

	add_action( 'greenpath_core_action_after_general_meta_box_map', 'greenpath_core_add_page_logo_meta_box' );
}

if ( ! function_exists( 'greenpath_core_add_general_logo_meta_box_callback' ) ) {
	/**
	 * Function that set current meta box callback as general callback functions
	 *
	 * @param array $callbacks
	 *
	 * @return array
	 */
	function greenpath_core_add_general_logo_meta_box_callback( $callbacks ) {
		$callbacks['logo'] = 'greenpath_core_add_page_logo_meta_box';

		return $callbacks;
	}

	add_filter( 'greenpath_core_filter_general_meta_box_callbacks', 'greenpath_core_add_general_logo_meta_box_callback' );
}
