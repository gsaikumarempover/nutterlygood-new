<?php

if ( ! function_exists( 'greenpath_core_add_top_message_options' ) ) {
	/**
	 * Function that add general options for this module
	 *
	 * @param object $page
	 * @param array $general_header_tab
	 */
	function greenpath_core_add_top_message_options( $page, $general_header_tab ) {

		$top_message_section = $general_header_tab->add_section_element(
			array(
				'name'        => 'qodef_top_message_section',
				'title'       => esc_html__( 'Top Message', 'greenpath-core' ),
				'description' => esc_html__( 'Options related to top message', 'greenpath-core' ),
			)
		);

		$top_message_section->add_field_element(
			array(
				'field_type'    => 'yesno',
				'default_value' => 'no',
				'name'          => 'qodef_top_message_header',
				'title'         => esc_html__( 'Top Message', 'greenpath-core' ),
				'description'   => esc_html__( 'Enable top message', 'greenpath-core' ),
			)
		);

		$top_message_section->add_field_element(
			array(
				'field_type' => 'text',
				'name'       => 'qodef_top_message',
				'title'      => esc_html__( 'Top Message Text', 'greenpath-core' ),
				'dependency' => array(
					'show' => array(
						'qodef_top_message_header' => array(
							'values'        => 'yes',
							'default_value' => 'no',
						),
					),
				),
			)
		);

		$top_message_section->add_field_element(
			array(
				'field_type' => 'text',
				'name'       => 'qodef_top_message_link',
				'title'      => esc_html__( 'Top Message Link', 'greenpath-core' ),
				'dependency' => array(
					'show' => array(
						'qodef_top_message_header' => array(
							'values'        => 'yes',
							'default_value' => 'no',
						),
					),
				),
			)
		);
	}

	add_action( 'greenpath_core_action_after_header_options_map', 'greenpath_core_add_top_message_options', 30, 2 );
}
