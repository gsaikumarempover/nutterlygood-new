<?php

if ( ! function_exists( 'greenpath_core_add_general_page_meta_box' ) ) {
	/**
	 * Function that add general meta box options for this module
	 *
	 * @param object $page
	 */
	function greenpath_core_add_general_page_meta_box( $page ) {

		$general_tab = $page->add_tab_element(
			array(
				'name'        => 'tab-page',
				'icon'        => 'fa fa-cog',
				'title'       => esc_html__( 'Page Settings', 'greenpath-core' ),
				'description' => esc_html__( 'General page layout settings', 'greenpath-core' ),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_page_background_color',
				'title'       => esc_html__( 'Page Background Color', 'greenpath-core' ),
				'description' => esc_html__( 'Set background color', 'greenpath-core' ),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'image',
				'name'        => 'qodef_page_background_image',
				'title'       => esc_html__( 'Page Background Image', 'greenpath-core' ),
				'description' => esc_html__( 'Set background image', 'greenpath-core' ),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_page_background_repeat',
				'title'       => esc_html__( 'Page Background Image Repeat', 'greenpath-core' ),
				'description' => esc_html__( 'Set background image repeat', 'greenpath-core' ),
				'options'     => array(
					''          => esc_html__( 'Default', 'greenpath-core' ),
					'no-repeat' => esc_html__( 'No Repeat', 'greenpath-core' ),
					'repeat'    => esc_html__( 'Repeat', 'greenpath-core' ),
					'repeat-x'  => esc_html__( 'Repeat-x', 'greenpath-core' ),
					'repeat-y'  => esc_html__( 'Repeat-y', 'greenpath-core' ),
				),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_page_background_size',
				'title'       => esc_html__( 'Page Background Image Size', 'greenpath-core' ),
				'description' => esc_html__( 'Set background image size', 'greenpath-core' ),
				'options'     => array(
					''        => esc_html__( 'Default', 'greenpath-core' ),
					'contain' => esc_html__( 'Contain', 'greenpath-core' ),
					'cover'   => esc_html__( 'Cover', 'greenpath-core' ),
				),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_page_background_attachment',
				'title'       => esc_html__( 'Page Background Image Attachment', 'greenpath-core' ),
				'description' => esc_html__( 'Set background image attachment', 'greenpath-core' ),
				'options'     => array(
					''       => esc_html__( 'Default', 'greenpath-core' ),
					'fixed'  => esc_html__( 'Fixed', 'greenpath-core' ),
					'scroll' => esc_html__( 'Scroll', 'greenpath-core' ),
				),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_page_content_padding',
				'title'       => esc_html__( 'Page Content Padding', 'greenpath-core' ),
				'description' => esc_html__( 'Set padding that will be applied for page content in format: top right bottom left (e.g. 10px 5px 10px 5px)', 'greenpath-core' ),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_page_content_padding_mobile',
				'title'       => esc_html__( 'Page Content Padding Mobile', 'greenpath-core' ),
				'description' => esc_html__( 'Set padding that will be applied for page content on mobile screens (1200px and below) in format: top right bottom left (e.g. 10px 5px 10px 5px)', 'greenpath-core' ),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'    => 'select',
				'name'          => 'qodef_boxed',
				'title'         => esc_html__( 'Boxed Layout', 'greenpath-core' ),
				'description'   => esc_html__( 'Set boxed layout', 'greenpath-core' ),
				'default_value' => '',
				'options'       => greenpath_core_get_select_type_options_pool( 'yes_no' ),
			)
		);

		$boxed_section = $general_tab->add_section_element(
			array(
				'name'       => 'qodef_boxed_section',
				'title'      => esc_html__( 'Boxed Layout Section', 'greenpath-core' ),
				'dependency' => array(
					'hide' => array(
						'qodef_boxed' => array(
							'values'        => 'no',
							'default_value' => '',
						),
					),
				),
			)
		);

		$boxed_section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_boxed_background_color',
				'title'       => esc_html__( 'Boxed Background Color', 'greenpath-core' ),
				'description' => esc_html__( 'Set boxed background color', 'greenpath-core' ),
			)
		);

		$boxed_section->add_field_element(
			array(
				'field_type'  => 'image',
				'name'        => 'qodef_boxed_background_pattern',
				'title'       => esc_html__( 'Boxed Background Pattern', 'greenpath-core' ),
				'description' => esc_html__( 'Set boxed background pattern', 'greenpath-core' ),
			)
		);

		$boxed_section->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_boxed_background_pattern_behavior',
				'title'       => esc_html__( 'Boxed Background Pattern Behavior', 'greenpath-core' ),
				'description' => esc_html__( 'Set boxed background pattern behavior', 'greenpath-core' ),
				'options'     => array(
					''       => esc_html__( 'Default', 'greenpath-core' ),
					'fixed'  => esc_html__( 'Fixed', 'greenpath-core' ),
					'scroll' => esc_html__( 'Scroll', 'greenpath-core' ),
				),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'    => 'select',
				'name'          => 'qodef_passepartout',
				'title'         => esc_html__( 'Passepartout', 'greenpath-core' ),
				'description'   => esc_html__( 'Enabling this option will display a passepartout around website content', 'greenpath-core' ),
				'default_value' => '',
				'options'       => greenpath_core_get_select_type_options_pool( 'yes_no' ),
			)
		);

		$passepartout_section = $general_tab->add_section_element(
			array(
				'name'       => 'qodef_passepartout_section',
				'dependency' => array(
					'hide' => array(
						'qodef_passepartout' => array(
							'values'        => 'no',
							'default_value' => '',
						),
					),
				),
			)
		);

		$passepartout_section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_passepartout_color',
				'title'       => esc_html__( 'Passepartout Color', 'greenpath-core' ),
				'description' => esc_html__( 'Choose background color for passepartout', 'greenpath-core' ),
			)
		);

		$passepartout_section->add_field_element(
			array(
				'field_type'  => 'image',
				'name'        => 'qodef_passepartout_image',
				'title'       => esc_html__( 'Passepartout Background Image', 'greenpath-core' ),
				'description' => esc_html__( 'Set background image for passepartout', 'greenpath-core' ),
			)
		);

		$passepartout_section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_passepartout_size',
				'title'       => esc_html__( 'Passepartout Size', 'greenpath-core' ),
				'description' => esc_html__( 'Enter size amount for passepartout', 'greenpath-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px or %', 'greenpath-core' ),
				),
			)
		);

		$passepartout_section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_passepartout_size_responsive',
				'title'       => esc_html__( 'Passepartout Responsive Size', 'greenpath-core' ),
				'description' => esc_html__( 'Enter size amount for passepartout for smaller screens (1200px and below)', 'greenpath-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px or %', 'greenpath-core' ),
				),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_content_width',
				'title'       => esc_html__( 'Initial Width of Content', 'greenpath-core' ),
				'description' => esc_html__( 'Choose the initial width of content which is in grid (applies to pages set to "Default Template" and rows set to "In Grid")', 'greenpath-core' ),
				'options'     => greenpath_core_get_select_type_options_pool( 'content_width' ),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'    => 'yesno',
				'default_value' => 'no',
				'name'          => 'qodef_content_behind_header',
				'title'         => esc_html__( 'Always put content behind header', 'greenpath-core' ),
				'description'   => esc_html__( 'Enabling this option will put page content behind page header', 'greenpath-core' ),
			)
		);

		// Hook to include additional options after module options
		do_action( 'greenpath_core_action_after_general_page_meta_box_map', $general_tab );
	}

	add_action( 'greenpath_core_action_after_general_meta_box_map', 'greenpath_core_add_general_page_meta_box', 9 );
}

if ( ! function_exists( 'greenpath_core_add_general_page_meta_box_callback' ) ) {
	/**
	 * Function that set current meta box callback as general callback functions
	 *
	 * @param array $callbacks
	 *
	 * @return array
	 */
	function greenpath_core_add_general_page_meta_box_callback( $callbacks ) {
		$callbacks['page'] = 'greenpath_core_add_general_page_meta_box';

		return $callbacks;
	}

	add_filter( 'greenpath_core_filter_general_meta_box_callbacks', 'greenpath_core_add_general_page_meta_box_callback' );
}
