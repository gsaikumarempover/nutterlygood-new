<?php

if ( ! function_exists( 'greenpath_core_add_page_social_share_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function greenpath_core_add_page_social_share_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => GREENPATH_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'social-share',
				'icon'        => 'fa fa-book',
				'title'       => esc_html__( 'Social Share', 'greenpath-core' ),
				'description' => esc_html__( 'Global Social Share Options', 'greenpath-core' ),
				'layout'      => 'tabbed',
			)
		);

		if ( $page ) {
			$social_networks_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-social-networks',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'Social Networks Settings', 'greenpath-core' ),
					'description' => esc_html__( 'Social networks settings', 'greenpath-core' ),
				)
			);

			$social_networks = greenpath_core_social_networks_list();

			foreach ( $social_networks as $network => $params ) {
				$social_networks_tab->add_field_element(
					array(
						'field_type'    => 'yesno',
						'name'          => 'qodef_enable_share_' . $network,
						'title'         => sprintf( esc_html__( 'Enable %s Share', 'greenpath-core' ), $params['label'] ),
						'default_value' => 'yes',
					)
				);

				if ( 'twitter' === $network ) {
					$social_networks_tab->add_field_element(
						array(
							'field_type'    => 'text',
							'name'          => 'qodef_twitter_via',
							'title'         => esc_html__( 'Twitter Via Text', 'greenpath-core' ),
							'default_value' => esc_html__( '@QodeInteractive', 'greenpath-core' ),
							'dependency'    => array(
								'show' => array(
									'qodef_enable_share_twitter' => array(
										'values'        => 'yes',
										'default_value' => 'yes',
									),
								),
							),
						)
					);
				}
			}

			$layout_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-social-layout',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'Layout Settings', 'greenpath-core' ),
					'description' => esc_html__( 'Social share layout settings', 'greenpath-core' ),
				)
			);

			$layout_tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_social_share_layout',
					'title'         => esc_html__( 'Social Share Layout', 'greenpath-core' ),
					'description'   => esc_html__( 'Choose default layout for social share', 'greenpath-core' ),
					'default_value' => apply_filters( 'greenpath_core_filter_social_share_layout_default_value', '' ),
					'options'       => apply_filters( 'greenpath_core_filter_social_share_layout_options', array() ),
				)
			);

			// Hook to include additional options after module options
			do_action( 'greenpath_core_action_after_social_share_layout_options_map', $layout_tab );

			$cpt_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-social-cpt',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'CPT Settings', 'greenpath-core' ),
					'description' => esc_html__( 'Social share CPT settings', 'greenpath-core' ),
				)
			);

			// Hook to include additional options after module options
			do_action( 'greenpath_core_action_after_social_share_cpt_options_map', $cpt_tab );

			// Hook to include additional options after module options
			do_action( 'greenpath_core_action_after_social_share_options_map', $page );
		}
	}

	add_action( 'greenpath_core_action_default_options_init', 'greenpath_core_add_page_social_share_options', greenpath_core_get_admin_options_map_position( 'social-share' ) );
}
