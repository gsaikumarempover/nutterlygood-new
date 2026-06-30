<?php

if ( ! function_exists( 'greenpath_core_add_subscribe_popup_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function greenpath_core_add_subscribe_popup_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => GREENPATH_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'subscribe-popup',
				'icon'        => 'fa fa-envelope',
				'title'       => esc_html__( 'Subscribe Popup', 'greenpath-core' ),
				'description' => esc_html__( 'Global Subscribe Popup Options', 'greenpath-core' ),
			)
		);

		if ( $page ) {
			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_subscribe_popup',
					'title'         => esc_html__( 'Enable Subscribe Popup', 'greenpath-core' ),
					'description'   => esc_html__( 'Use this option to enable/disable subscribe popup', 'greenpath-core' ),
					'default_value' => 'no',
				)
			);

			$subscribe_popup_section = $page->add_section_element(
				array(
					'name'       => 'qodef_subscribe_popup_section',
					'title'      => esc_html__( 'Subscribe Popup', 'greenpath-core' ),
					'dependency' => array(
						'hide' => array(
							'qodef_enable_subscribe_popup' => array(
								'values'        => 'no',
								'default_value' => '',
							),
						),
					),
				)
			);

			$subscribe_popup_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_subscribe_popup_title',
					'title'       => esc_html__( 'Title', 'greenpath-core' ),
					'description' => esc_html__( 'Enter subscribe popup window title ', 'greenpath-core' ),
				)
			);

			$subscribe_popup_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_subscribe_popup_subtitle',
					'title'       => esc_html__( 'Subtitle', 'greenpath-core' ),
					'description' => esc_html__( 'Enter subscribe popup window subtitle', 'greenpath-core' ),
				)
			);

			$subscribe_popup_section->add_field_element(
				array(
					'field_type' => 'image',
					'name'       => 'qodef_subscribe_popup_background_image',
					'title'      => esc_html__( 'Background Image', 'greenpath-core' ),
				)
			);

			$subscribe_popup_section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_subscribe_popup_contact_form',
					'title'       => esc_html__( 'Select Contact Form', 'greenpath-core' ),
					'description' => esc_html__( 'Choose contact form to display in subscribe popup window', 'greenpath-core' ),
					'options'     => qode_framework_get_cpt_items( 'wpcf7_contact_form', array( 'numberposts' => '-1' ) ),
				)
			);

			$subscribe_popup_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_subscribe_popup_prevent',
					'title'         => esc_html__( 'Enable Subscribe Popup Prevent', 'greenpath-core' ),
					'default_value' => 'no',
				)
			);

			$subscribe_popup_section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_subscribe_popup_prevent_behavior',
					'title'       => esc_html__( 'Subscribe Popup Prevent Behavior', 'greenpath-core' ),
					'description' => esc_html__( 'Choose how to manage popup prevent', 'greenpath-core' ),
					'options'     => array(
						'session' => esc_html__( 'by Current Session', 'greenpath-core' ),
						'cookies' => esc_html__( 'by Browser Cookies', 'greenpath-core' ),
					),
					'dependency'  => array(
						'show' => array(
							'qodef_enable_subscribe_popup_prevent' => array(
								'values'        => 'yes',
								'default_value' => '',
							),
						),
					),
				)
			);

			// Hook to include additional options after module options
			do_action( 'greenpath_core_action_after_subscribe_popup_options_map', $subscribe_popup_section );
		}
	}

	add_action( 'greenpath_core_action_default_options_init', 'greenpath_core_add_subscribe_popup_options', greenpath_core_get_admin_options_map_position( 'subscribe-popup' ) );
}
