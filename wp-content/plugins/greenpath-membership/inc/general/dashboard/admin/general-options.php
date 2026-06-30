<?php

if ( ! function_exists( 'greenpath_membership_add_general_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function greenpath_membership_add_general_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => GREENPATH_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'membership',
				'icon'        => 'fa fa-envelope',
				'title'       => esc_html__( 'Membership', 'greenpath-membership' ),
				'description' => esc_html__( 'Membership Settings', 'greenpath-membership' ),
			)
		);

		if ( $page ) {

			$page->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_membership_privacy_policy_text',
					'title'       => esc_html__( 'Privacy Policy Text', 'greenpath-membership' ),
					'description' => esc_html__( 'Enter privacy policy text for registration modal form', 'greenpath-membership' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_membership_privacy_policy_link',
					'title'       => esc_html__( 'Privacy Policy Link', 'greenpath-membership' ),
					'description' => esc_html__( 'Choose "Privacy Policy Link" page to link from registration modal form', 'greenpath-membership' ),
					'options'     => qode_framework_get_pages( true ),
				)
			);

			$page->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_membership_privacy_policy_link_text',
					'title'       => esc_html__( 'Privacy Policy Link Text', 'greenpath-membership' ),
					'description' => esc_html__( 'Enter privacy policy link text for registration modal form. Default value is "privacy policy"', 'greenpath-membership' ),
				)
			);

			// Hook to include additional options after module options
			do_action( 'greenpath_membership_action_after_membership_options_map', $page );
		}
	}

	add_action( 'greenpath_core_action_default_options_init', 'greenpath_membership_add_general_options', 70 );
}
