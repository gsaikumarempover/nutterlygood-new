<?php

if ( ! function_exists( 'greenpath_core_add_page_title_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function greenpath_core_add_page_title_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => GREENPATH_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'title',
				'icon'        => 'fa fa-cog',
				'title'       => esc_html__( 'Title', 'greenpath-core' ),
				'description' => esc_html__( 'Global Title Options', 'greenpath-core' ),
			)
		);

		if ( $page ) {
			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_page_title',
					'title'         => esc_html__( 'Enable Page Title', 'greenpath-core' ),
					'description'   => esc_html__( 'Use this option to enable/disable page title', 'greenpath-core' ),
					'default_value' => 'yes',
				)
			);

			$page_title_section = $page->add_section_element(
				array(
					'name'       => 'qodef_page_title_section',
					'title'      => esc_html__( 'Title Area', 'greenpath-core' ),
					'dependency' => array(
						'hide' => array(
							'qodef_enable_page_title' => array(
								'values'        => 'no',
								'default_value' => '',
							),
						),
					),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_title_layout',
					'title'         => esc_html__( 'Title Layout', 'greenpath-core' ),
					'description'   => esc_html__( 'Choose a title layout', 'greenpath-core' ),
					'options'       => apply_filters( 'greenpath_core_filter_title_layout_options', array() ),
					'default_value' => 'standard',
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_set_page_title_area_in_grid',
					'title'         => esc_html__( 'Page Title In Grid', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will set page title area to be in grid', 'greenpath-core' ),
					'default_value' => 'yes',
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_page_title_height',
					'title'       => esc_html__( 'Height', 'greenpath-core' ),
					'description' => esc_html__( 'Enter title height', 'greenpath-core' ),
					'args'        => array(
						'suffix' => esc_html__( 'px', 'greenpath-core' ),
					),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_page_title_height_on_smaller_screens',
					'title'       => esc_html__( 'Height on Smaller Screens', 'greenpath-core' ),
					'description' => esc_html__( 'Enter title height to be displayed on smaller screens with active mobile header', 'greenpath-core' ),
					'args'        => array(
						'suffix' => esc_html__( 'px', 'greenpath-core' ),
					),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'  => 'color',
					'name'        => 'qodef_page_title_background_color',
					'title'       => esc_html__( 'Background Color', 'greenpath-core' ),
					'description' => esc_html__( 'Enter page title area background color', 'greenpath-core' ),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'  => 'color',
					'name'        => 'qodef_page_title_border_color',
					'title'       => esc_html__( 'Title Border Color', 'greenpath-core' ),
					'description' => esc_html__( 'Enter title border color', 'greenpath-core' ),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_page_title_border_width',
					'title'       => esc_html__( 'Title Border Width', 'greenpath-core' ),
					'description' => esc_html__( 'Enter title border width size', 'greenpath-core' ),
					'args'        => array(
						'suffix' => esc_html__( 'px', 'greenpath-core' ),
					),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_page_title_border_style',
					'title'       => esc_html__( 'Title Border Style', 'greenpath-core' ),
					'description' => esc_html__( 'Choose title border style', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'border_style' ),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_page_title_enable_top_border',
					'title'         => esc_html__( 'Enable Top Border', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes', false ),
					'default_value' => 'no',
					'dependency'    => array(
						'show' => array(
							'qodef_title_layout' => array(
								'values'        => 'breadcrumbs',
								'default_value' => '',
							),
						),
					),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'  => 'image',
					'name'        => 'qodef_page_title_background_image',
					'title'       => esc_html__( 'Background Image', 'greenpath-core' ),
					'description' => esc_html__( 'Enter page title area background image', 'greenpath-core' ),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_page_title_background_image_behavior',
					'title'      => esc_html__( 'Background Image Behavior', 'greenpath-core' ),
					'options'    => array(
						''           => esc_html__( 'Default', 'greenpath-core' ),
						'responsive' => esc_html__( 'Set Responsive Image', 'greenpath-core' ),
						'parallax'   => esc_html__( 'Set Parallax Image', 'greenpath-core' ),
					),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_page_title_color',
					'title'      => esc_html__( 'Title Color', 'greenpath-core' ),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_page_title_tag',
					'title'         => esc_html__( 'Title Tag', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will set title tag', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'title_tag', false ),
					'default_value' => 'h1',
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_page_title_text_alignment',
					'title'         => esc_html__( 'Text Alignment', 'greenpath-core' ),
					'options'       => array(
						'left'   => esc_html__( 'Left', 'greenpath-core' ),
						'center' => esc_html__( 'Center', 'greenpath-core' ),
						'right'  => esc_html__( 'Right', 'greenpath-core' ),
					),
					'default_value' => 'left',
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_page_title_vertical_text_alignment',
					'title'         => esc_html__( 'Vertical Text Alignment', 'greenpath-core' ),
					'options'       => array(
						'header-bottom' => esc_html__( 'From Bottom of Header', 'greenpath-core' ),
						'window-top'    => esc_html__( 'From Window Top', 'greenpath-core' ),
					),
					'default_value' => 'header-bottom',
				)
			);

			// Hook to include additional options after module options
			do_action( 'greenpath_core_action_after_page_title_options_map', $page_title_section );
		}
	}

	add_action( 'greenpath_core_action_default_options_init', 'greenpath_core_add_page_title_options', greenpath_core_get_admin_options_map_position( 'title' ) );
}
