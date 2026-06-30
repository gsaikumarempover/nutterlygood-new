<?php

if ( ! function_exists( 'greenpath_core_add_page_mobile_header_meta_box' ) ) {
	/**
	 * Function that add general meta box options for this module
	 *
	 * @param object $page
	 */
	function greenpath_core_add_page_mobile_header_meta_box( $page ) {

		if ( $page ) {
			$mobile_header_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-mobile-header',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'Mobile Header Settings', 'greenpath-core' ),
					'description' => esc_html__( 'Mobile header layout settings', 'greenpath-core' ),
				)
			);

			$mobile_header_tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_mobile_header_scroll_appearance',
					'title'         => esc_html__( 'Sticky Mobile Header', 'greenpath-core' ),
					'description'   => esc_html__( 'Set mobile header to be sticky', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'yes_no', true ),
					'default_value' => '',
				)
			);

			$mobile_header_tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_mobile_header_layout',
					'title'       => esc_html__( 'Mobile Header Layout', 'greenpath-core' ),
					'description' => esc_html__( 'Choose a mobile header layout to set for your website', 'greenpath-core' ),
					'args'        => array( 'images' => true ),
					'options'     => greenpath_core_header_radio_to_select_options( apply_filters( 'greenpath_core_filter_mobile_header_layout_option', array() ) ),
				)
			);

			$mobile_header_tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_mobile_header_in_grid',
					'title'         => esc_html__( 'Content in Grid', 'greenpath-core' ),
					'description'   => esc_html__( 'Set content to be in grid', 'greenpath-core' ),
					'default_value' => '',
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes' ),
				)
			);

			$mobile_header_tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_mobile_enable_product_search',
					'title'         => esc_html__( 'Enable Product Search', 'greenpath-core' ),
					'description'   => esc_html__( 'Adds a Product Search widget underneath the Mobile Header', 'greenpath-core' ),
					'default_value' => '',
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes' ),
				)
			);


			$opener_section = $mobile_header_tab->add_section_element(
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
			do_action( 'greenpath_core_action_after_page_mobile_header_meta_map', $mobile_header_tab );
		}
	}

	add_action( 'greenpath_core_action_after_general_meta_box_map', 'greenpath_core_add_page_mobile_header_meta_box' );
}

if ( ! function_exists( 'greenpath_core_add_general_mobile_header_meta_box_callback' ) ) {
	/**
	 * Function that set current meta box callback as general callback functions
	 *
	 * @param array $callbacks
	 *
	 * @return array
	 */
	function greenpath_core_add_general_mobile_header_meta_box_callback( $callbacks ) {
		$callbacks['mobile-header'] = 'greenpath_core_add_page_mobile_header_meta_box';

		return $callbacks;
	}

	add_filter( 'greenpath_core_filter_general_meta_box_callbacks', 'greenpath_core_add_general_mobile_header_meta_box_callback' );
}
