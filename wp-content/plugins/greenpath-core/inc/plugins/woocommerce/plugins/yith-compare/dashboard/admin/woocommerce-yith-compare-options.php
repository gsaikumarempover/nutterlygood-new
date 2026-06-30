<?php

if ( ! function_exists( 'greenpath_core_add_yith_compare_options' ) ) {
	/**
	 * Function that add general options for this module
	 *
	 * @param object $page
	 */
	function greenpath_core_add_yith_compare_options( $page ) {

		if ( $page ) {

			$yith_compare_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-yith-compare',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'YITH Compare', 'greenpath-core' ),
					'description' => esc_html__( 'Settings related to YITH Compare', 'greenpath-core' ),
				)
			);

			$yith_compare_tab->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_woo_yith_compare_predefined_style',
					'title'         => esc_html__( 'Enable Predefined Style', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will set predefined style for YITH Compare plugin', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes', false ),
					'default_value' => 'yes',
				)
			);
		}
	}

	add_action( 'greenpath_core_action_after_woo_options_map', 'greenpath_core_add_yith_compare_options' );
}
