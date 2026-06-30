<?php

if ( ! function_exists( 'greenpath_core_add_h3_typography_options' ) ) {
	/**
	 * Function that add general options for this module
	 *
	 * @param object $page
	 */
	function greenpath_core_add_h3_typography_options( $page ) {

		if ( $page ) {
			$h3_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-h3',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'H3 Typography', 'greenpath-core' ),
					'description' => esc_html__( 'Set values for Heading 3 HTML element', 'greenpath-core' ),
				)
			);

			$h3_typography_section = $h3_tab->add_section_element(
				array(
					'name'  => 'qodef_h3_typography_section',
					'title' => esc_html__( 'General Typography', 'greenpath-core' ),
				)
			);

			$h3_typography_row = $h3_typography_section->add_row_element(
				array(
					'name' => 'qodef_h3_typography_row',
				)
			);

			$h3_typography_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_h3_color',
					'title'      => esc_html__( 'Color', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h3_typography_row->add_field_element(
				array(
					'field_type' => 'font',
					'name'       => 'qodef_h3_font_family',
					'title'      => esc_html__( 'Font Family', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h3_typography_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h3_typography_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h3_typography_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_letter_spacing',
					'title'      => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h3_typography_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_h3_font_weight',
					'title'      => esc_html__( 'Font Weight', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'font_weight' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h3_typography_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_h3_text_transform',
					'title'      => esc_html__( 'Text Transform', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_transform' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h3_typography_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_h3_font_style',
					'title'      => esc_html__( 'Font Style', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'font_style' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h3_typography_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_h3_text_decoration',
					'title'      => esc_html__( 'Text Decoration', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_decoration' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h3_typography_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_h3_link_hover_color',
					'title'      => esc_html__( 'Link Hover Color', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h3_typography_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_h3_link_hover_text_decoration',
					'title'      => esc_html__( 'Link Hover Text Decoration', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_decoration' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h3_typography_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_margin_top',
					'title'      => esc_html__( 'Margin Top', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h3_typography_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_margin_bottom',
					'title'      => esc_html__( 'Margin Bottom', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			/* 1368 styles */
			$h3_1368_typography_section = $h3_tab->add_section_element(
				array(
					'name'  => 'qodef_responsive_1368_typography_h3',
					'title' => esc_html__( 'Responsive 1368 Typography', 'greenpath-core' ),
				)
			);

			$responsive_1368_typography_h3_row = $h3_1368_typography_section->add_row_element(
				array(
					'name' => 'qodef_responsive_1368_h3_typography_row',
				)
			);

			$responsive_1368_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_1368_font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_1368_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_1368_line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_1368_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_1368_letter_spacing',
					'title'      => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			/* 1200 styles */
			$h3_1200_typography_section = $h3_tab->add_section_element(
				array(
					'name'  => 'qodef_responsive_1200_typography_h3',
					'title' => esc_html__( 'Responsive 1200 Typography', 'greenpath-core' ),
				)
			);

			$responsive_1200_typography_h3_row = $h3_1200_typography_section->add_row_element(
				array(
					'name' => 'qodef_responsive_1200_h3_typography_row',
				)
			);

			$responsive_1200_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_1200_font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_1200_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_1200_line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_1200_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_1200_letter_spacing',
					'title'      => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			/* 1024 styles */
			$h3_1024_typography_section = $h3_tab->add_section_element(
				array(
					'name'  => 'qodef_responsive_1024_typography_h3',
					'title' => esc_html__( 'Responsive 1024 Typography', 'greenpath-core' ),
				)
			);

			$responsive_1024_typography_h3_row = $h3_1024_typography_section->add_row_element(
				array(
					'name' => 'qodef_responsive_1024_h3_typography_row',
				)
			);

			$responsive_1024_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_1024_font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_1024_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_1024_line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_1024_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_1024_letter_spacing',
					'title'      => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			/* 880 styles */
			$h3_880_typography_section = $h3_tab->add_section_element(
				array(
					'name'  => 'qodef_responsive_880_typography_h3',
					'title' => esc_html__( 'Responsive 880 Typography', 'greenpath-core' ),
				)
			);

			$responsive_880_typography_h3_row = $h3_880_typography_section->add_row_element(
				array(
					'name' => 'qodef_responsive_880_h3_typography_row',
				)
			);

			$responsive_880_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_880_font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_880_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_880_line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_880_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_880_letter_spacing',
					'title'      => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			/* 680 styles */
			$h3_680_typography_section = $h3_tab->add_section_element(
				array(
					'name'  => 'qodef_responsive_680_typography_h3',
					'title' => esc_html__( 'Responsive 680 Typography', 'greenpath-core' ),
				)
			);

			$responsive_680_typography_h3_row = $h3_680_typography_section->add_row_element(
				array(
					'name' => 'qodef_responsive_680_h3_typography_row',
				)
			);

			$responsive_680_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_680_font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_680_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_680_line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_680_typography_h3_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h3_responsive_680_letter_spacing',
					'title'      => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);
		}
	}

	add_action( 'greenpath_core_action_after_typography_options_map', 'greenpath_core_add_h3_typography_options' );
}
