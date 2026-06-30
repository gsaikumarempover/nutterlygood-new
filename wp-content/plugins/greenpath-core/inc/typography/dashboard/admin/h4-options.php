<?php

if ( ! function_exists( 'greenpath_core_add_h4_typography_options' ) ) {
	/**
	 * Function that add general options for this module
	 *
	 * @param object $page
	 */
	function greenpath_core_add_h4_typography_options( $page ) {

		if ( $page ) {
			$h4_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-h4',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'H4 Typography', 'greenpath-core' ),
					'description' => esc_html__( 'Set values for Heading 4 HTML element', 'greenpath-core' ),
				)
			);

			$h4_typography_section = $h4_tab->add_section_element(
				array(
					'name'  => 'qodef_h4_typography_section',
					'title' => esc_html__( 'General Typography', 'greenpath-core' ),
				)
			);

			$h4_typography_row = $h4_typography_section->add_row_element(
				array(
					'name' => 'qodef_h4_typography_row',
				)
			);

			$h4_typography_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_h4_color',
					'title'      => esc_html__( 'Color', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h4_typography_row->add_field_element(
				array(
					'field_type' => 'font',
					'name'       => 'qodef_h4_font_family',
					'title'      => esc_html__( 'Font Family', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h4_typography_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h4_typography_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h4_typography_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_letter_spacing',
					'title'      => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h4_typography_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_h4_font_weight',
					'title'      => esc_html__( 'Font Weight', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'font_weight' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h4_typography_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_h4_text_transform',
					'title'      => esc_html__( 'Text Transform', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_transform' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h4_typography_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_h4_font_style',
					'title'      => esc_html__( 'Font Style', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'font_style' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h4_typography_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_h4_text_decoration',
					'title'      => esc_html__( 'Text Decoration', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_decoration' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h4_typography_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_h4_link_hover_color',
					'title'      => esc_html__( 'Link Hover Color', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h4_typography_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_h4_link_hover_text_decoration',
					'title'      => esc_html__( 'Link Hover Text Decoration', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_decoration' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h4_typography_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_margin_top',
					'title'      => esc_html__( 'Margin Top', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$h4_typography_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_margin_bottom',
					'title'      => esc_html__( 'Margin Bottom', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			/* 1368 styles */
			$h4_1368_typography_section = $h4_tab->add_section_element(
				array(
					'name'  => 'qodef_responsive_1368_typography_h4',
					'title' => esc_html__( 'Responsive 1368 Typography', 'greenpath-core' ),
				)
			);

			$responsive_1368_typography_h4_row = $h4_1368_typography_section->add_row_element(
				array(
					'name' => 'qodef_responsive_1368_h4_typography_row',
				)
			);

			$responsive_1368_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_1368_font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_1368_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_1368_line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_1368_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_1368_letter_spacing',
					'title'      => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			/* 1200 styles */
			$h4_1200_typography_section = $h4_tab->add_section_element(
				array(
					'name'  => 'qodef_responsive_1200_typography_h4',
					'title' => esc_html__( 'Responsive 1200 Typography', 'greenpath-core' ),
				)
			);

			$responsive_1200_typography_h4_row = $h4_1200_typography_section->add_row_element(
				array(
					'name' => 'qodef_responsive_1200_h4_typography_row',
				)
			);

			$responsive_1200_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_1200_font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_1200_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_1200_line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_1200_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_1200_letter_spacing',
					'title'      => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			/* 1024 styles */
			$h4_1024_typography_section = $h4_tab->add_section_element(
				array(
					'name'  => 'qodef_responsive_1024_typography_h4',
					'title' => esc_html__( 'Responsive 1024 Typography', 'greenpath-core' ),
				)
			);

			$responsive_1024_typography_h4_row = $h4_1024_typography_section->add_row_element(
				array(
					'name' => 'qodef_responsive_1024_h4_typography_row',
				)
			);

			$responsive_1024_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_1024_font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_1024_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_1024_line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_1024_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_1024_letter_spacing',
					'title'      => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			/* 880 styles */
			$h4_880_typography_section = $h4_tab->add_section_element(
				array(
					'name'  => 'qodef_responsive_880_typography_h4',
					'title' => esc_html__( 'Responsive 880 Typography', 'greenpath-core' ),
				)
			);

			$responsive_880_typography_h4_row = $h4_880_typography_section->add_row_element(
				array(
					'name' => 'qodef_responsive_880_h4_typography_row',
				)
			);

			$responsive_880_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_880_font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_880_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_880_line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_880_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_880_letter_spacing',
					'title'      => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			/* 680 styles */
			$h4_680_typography_section = $h4_tab->add_section_element(
				array(
					'name'  => 'qodef_responsive_680_typography_h4',
					'title' => esc_html__( 'Responsive 680 Typography', 'greenpath-core' ),
				)
			);

			$responsive_680_typography_h4_row = $h4_680_typography_section->add_row_element(
				array(
					'name' => 'qodef_responsive_680_h4_typography_row',
				)
			);

			$responsive_680_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_680_font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_680_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_680_line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);

			$responsive_680_typography_h4_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_h4_responsive_680_letter_spacing',
					'title'      => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 4,
					),
				)
			);
		}
	}

	add_action( 'greenpath_core_action_after_typography_options_map', 'greenpath_core_add_h4_typography_options' );
}
