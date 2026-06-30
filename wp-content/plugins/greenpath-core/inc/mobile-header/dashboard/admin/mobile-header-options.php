<?php

if ( ! function_exists( 'greenpath_core_add_mobile_header_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function greenpath_core_add_mobile_header_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => GREENPATH_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'layout'      => 'tabbed',
				'slug'        => 'mobile-header',
				'icon'        => 'fa fa-cog',
				'title'       => esc_html__( 'Mobile Header', 'greenpath-core' ),
				'description' => esc_html__( 'Global Mobile Header Options', 'greenpath-core' ),
			)
		);

		if ( $page ) {
			$general_tab = $page->add_tab_element(
				array(
					'name'  => 'tab-mobile-header-general',
					'icon'  => 'fa fa-cog',
					'title' => esc_html__( 'General Settings', 'greenpath-core' ),
				)
			);

			$general_tab->add_field_element(
				array(
					'field_type'    => 'yesno',
					'default_value' => 'no',
					'name'          => 'qodef_mobile_header_scroll_appearance',
					'title'         => esc_html__( 'Sticky Mobile Header', 'greenpath-core' ),
					'description'   => esc_html__( 'Set mobile header to be sticky', 'greenpath-core' ),
				)
			);

			$general_tab->add_field_element(
				array(
					'field_type'    => 'radio',
					'name'          => 'qodef_mobile_header_layout',
					'title'         => esc_html__( 'Mobile Header Layout', 'greenpath-core' ),
					'description'   => esc_html__( 'Choose a mobile header layout to set for your website', 'greenpath-core' ),
					'args'          => array( 'images' => true ),
					'default_value' => apply_filters( 'greenpath_core_filter_mobile_header_layout_default_option', '' ),
					'options'       => apply_filters( 'greenpath_core_filter_mobile_header_layout_option', array() ),
				)
			);

			$general_tab->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_mobile_header_in_grid',
					'title'         => esc_html__( 'Content in Grid', 'greenpath-core' ),
					'description'   => esc_html__( 'Set content to be in grid', 'greenpath-core' ),
					'default_value' => 'no',
				)
			);

			$general_tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_mobile_menu_icon_source',
					'title'         => esc_html__( 'Icon Source', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'icon_source', false ),
					'default_value' => 'predefined',
				)
			);

			$general_tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_mobile_menu_icon_pack',
					'title'         => esc_html__( 'Icon Pack', 'greenpath-core' ),
					'options'       => qode_framework_icons()->get_icon_packs( array( 'linea-icons', 'dripicons', 'simple-line-icons' ) ),
					'default_value' => 'font-awesome',
					'dependency'    => array(
						'show' => array(
							'qodef_mobile_menu_icon_source' => array(
								'values'        => 'icon_pack',
								'default_value' => 'predefined',
							),
						),
					),
				)
			);

			$section_svg_path = $general_tab->add_section_element(
				array(
					'title'      => esc_html__( 'SVG Path', 'greenpath-core' ),
					'name'       => 'qodef_mobile_menu_svg_path_section',
					'dependency' => array(
						'show' => array(
							'qodef_mobile_menu_icon_source' => array(
								'values'        => 'svg_path',
								'default_value' => 'predefined',
							),
						),
					),
				)
			);

			$section_svg_path->add_field_element(
				array(
					'field_type'  => 'textarea',
					'name'        => 'qodef_mobile_menu_icon_svg_path',
					'title'       => esc_html__( 'Mobile Menu Open Icon SVG Path', 'greenpath-core' ),
					'description' => esc_html__( 'Enter your mobile menu open icon SVG path here. Please remove version and id attributes from your SVG path because of HTML validation', 'greenpath-core' ),
				)
			);

			$section_svg_path->add_field_element(
				array(
					'field_type'  => 'textarea',
					'name'        => 'qodef_mobile_menu_close_icon_svg_path',
					'title'       => esc_html__( 'Mobile Menu Close Icon SVG Path', 'greenpath-core' ),
					'description' => esc_html__( 'Enter your mobile menu close icon SVG path here. Please remove version and id attributes from your SVG path because of HTML validation', 'greenpath-core' ),
				)
			);

			$opener_section = $general_tab->add_section_element(
				array(
					'name'  => 'qodef_mobile_header_opener_section',
					'title' => esc_html__( 'Mobile Header Opener Styles', 'greenpath-core' ),
				)
			);

			$opener_section_row = $opener_section->add_row_element(
				array(
					'name' => 'qodef_mobile_header_opener_row',
				)
			);

			$opener_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_mobile_header_opener_color',
					'title'      => esc_html__( 'Color', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$opener_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_mobile_header_opener_hover_color',
					'title'      => esc_html__( 'Hover/Active Color', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$opener_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_mobile_header_opener_size',
					'title'      => esc_html__( 'Icon Size', 'greenpath-core' ),
					'args'       => array(
						'col_width' => 3,
						'suffix'    => 'px',
					),
				)
			);

			// Hook to include additional options after module options
			do_action( 'greenpath_core_action_after_mobile_header_options_map', $page, $general_tab );
		}
	}

	add_action( 'greenpath_core_action_default_options_init', 'greenpath_core_add_mobile_header_options', greenpath_core_get_admin_options_map_position( 'mobile-header' ) );
}
