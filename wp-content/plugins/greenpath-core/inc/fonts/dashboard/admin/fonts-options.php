<?php

if ( ! function_exists( 'greenpath_core_add_fonts_options' ) ) {
	/**
	 * Function that add options for this module
	 */
	function greenpath_core_add_fonts_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => GREENPATH_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'fonts',
				'title'       => esc_html__( 'Fonts', 'greenpath-core' ),
				'description' => esc_html__( 'Global Fonts Options', 'greenpath-core' ),
				'icon'        => 'fa fa-cog',
			)
		);

		if ( $page ) {
			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_google_fonts',
					'title'         => esc_html__( 'Enable Google Fonts', 'greenpath-core' ),
					'default_value' => 'yes',
					'args'          => array(
						'custom_class' => 'qodef-enable-google-fonts',
					),
				)
			);

			$google_fonts_section = $page->add_section_element(
				array(
					'name'       => 'qodef_google_fonts_section',
					'title'      => esc_html__( 'Google Fonts Options', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_enable_google_fonts' => array(
								'values'        => 'yes',
								'default_value' => '',
							),
						),
					),
				)
			);

			$page_repeater = $google_fonts_section->add_repeater_element(
				array(
					'name'        => 'qodef_choose_google_fonts',
					'title'       => esc_html__( 'Google Fonts to Include', 'greenpath-core' ),
					'description' => esc_html__( 'Choose Google Fonts which you want to use on your website', 'greenpath-core' ),
					'button_text' => esc_html__( 'Add New Google Font', 'greenpath-core' ),
				)
			);

			$page_repeater->add_field_element(
				array(
					'field_type'  => 'googlefont',
					'name'        => 'qodef_choose_google_font',
					'title'       => esc_html__( 'Google Font', 'greenpath-core' ),
					'description' => esc_html__( 'Choose Google Font', 'greenpath-core' ),
					'args'        => array(
						'include' => 'google-fonts',
					),
				)
			);

			$google_fonts_section->add_field_element(
				array(
					'field_type'  => 'checkbox',
					'name'        => 'qodef_google_fonts_weight',
					'title'       => esc_html__( 'Google Fonts Weight', 'greenpath-core' ),
					'description' => esc_html__( 'Choose a default Google Fonts weights for your website. Impact on page load time', 'greenpath-core' ),
					'options'     => array(
						'100'  => esc_html__( '100 Thin', 'greenpath-core' ),
						'100i' => esc_html__( '100 Thin Italic', 'greenpath-core' ),
						'200'  => esc_html__( '200 Extra-Light', 'greenpath-core' ),
						'200i' => esc_html__( '200 Extra-Light Italic', 'greenpath-core' ),
						'300'  => esc_html__( '300 Light', 'greenpath-core' ),
						'300i' => esc_html__( '300 Light Italic', 'greenpath-core' ),
						'400'  => esc_html__( '400 Regular', 'greenpath-core' ),
						'400i' => esc_html__( '400 Regular Italic', 'greenpath-core' ),
						'500'  => esc_html__( '500 Medium', 'greenpath-core' ),
						'500i' => esc_html__( '500 Medium Italic', 'greenpath-core' ),
						'600'  => esc_html__( '600 Semi-Bold', 'greenpath-core' ),
						'600i' => esc_html__( '600 Semi-Bold Italic', 'greenpath-core' ),
						'700'  => esc_html__( '700 Bold', 'greenpath-core' ),
						'700i' => esc_html__( '700 Bold Italic', 'greenpath-core' ),
						'800'  => esc_html__( '800 Extra-Bold', 'greenpath-core' ),
						'800i' => esc_html__( '800 Extra-Bold Italic', 'greenpath-core' ),
						'900'  => esc_html__( '900 Ultra-Bold', 'greenpath-core' ),
						'900i' => esc_html__( '900 Ultra-Bold Italic', 'greenpath-core' ),
					),
				)
			);

			$google_fonts_section->add_field_element(
				array(
					'field_type'  => 'checkbox',
					'name'        => 'qodef_google_fonts_subset',
					'title'       => esc_html__( 'Google Fonts Style', 'greenpath-core' ),
					'description' => esc_html__( 'Choose a default Google Fonts style for your website. Impact on page load time', 'greenpath-core' ),
					'options'     => array(
						'latin'        => esc_html__( 'Latin', 'greenpath-core' ),
						'latin-ext'    => esc_html__( 'Latin Extended', 'greenpath-core' ),
						'cyrillic'     => esc_html__( 'Cyrillic', 'greenpath-core' ),
						'cyrillic-ext' => esc_html__( 'Cyrillic Extended', 'greenpath-core' ),
						'greek'        => esc_html__( 'Greek', 'greenpath-core' ),
						'greek-ext'    => esc_html__( 'Greek Extended', 'greenpath-core' ),
						'vietnamese'   => esc_html__( 'Vietnamese', 'greenpath-core' ),
					),
				)
			);

			$page_repeater = $page->add_repeater_element(
				array(
					'name'        => 'qodef_custom_fonts',
					'title'       => esc_html__( 'Custom Fonts', 'greenpath-core' ),
					'description' => esc_html__( 'Add custom fonts', 'greenpath-core' ),
					'button_text' => esc_html__( 'Add New Custom Font', 'greenpath-core' ),
				)
			);

			$page_repeater->add_field_element(
				array(
					'field_type' => 'file',
					'name'       => 'qodef_custom_font_ttf',
					'title'      => esc_html__( 'Custom Font TTF', 'greenpath-core' ),
					'args'       => array(
						'allowed_type' => 'font/ttf',
					),
				)
			);

			$page_repeater->add_field_element(
				array(
					'field_type' => 'file',
					'name'       => 'qodef_custom_font_otf',
					'title'      => esc_html__( 'Custom Font OTF', 'greenpath-core' ),
					'args'       => array(
						'allowed_type' => 'font/otf',
					),
				)
			);

			$page_repeater->add_field_element(
				array(
					'field_type' => 'file',
					'name'       => 'qodef_custom_font_woff',
					'title'      => esc_html__( 'Custom Font WOFF', 'greenpath-core' ),
					'args'       => array(
						'allowed_type' => 'font/woff',
					),
				)
			);

			$page_repeater->add_field_element(
				array(
					'field_type' => 'file',
					'name'       => 'qodef_custom_font_woff2',
					'title'      => esc_html__( 'Custom Font WOFF2', 'greenpath-core' ),
					'args'       => array(
						'allowed_type' => 'font/woff2',
					),
				)
			);

			$page_repeater->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_custom_font_name',
					'title'      => esc_html__( 'Custom Font Name', 'greenpath-core' ),
				)
			);

			// Hook to include additional options after module options
			do_action( 'greenpath_core_action_after_page_fonts_options_map', $page );
		}
	}

	add_action( 'greenpath_core_action_default_options_init', 'greenpath_core_add_fonts_options', greenpath_core_get_admin_options_map_position( 'fonts' ) );
}
