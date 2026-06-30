<?php

if ( ! function_exists( 'greenpath_register_required_plugins' ) ) {
	/**
	 * Function that registers theme required and optional plugins. Hooks to tgmpa_register hook
	 */
	function greenpath_register_required_plugins() {
		$plugins = array(
			array(
				'name'               => esc_html__( 'Theme Framework', 'nuttergood' ),
				'slug'               => 'qode-framework',
				'source'             => GREENPATH_INC_ROOT_DIR . '/plugins/qode-framework.zip',
				'version'            => '1.2.2',
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'               => esc_html__( 'Nuttergood Core', 'nuttergood' ),
				'slug'               => 'greenpath-core',
				'source'             => GREENPATH_INC_ROOT_DIR . '/plugins/greenpath-core.zip',
				'version'            => '1.0',
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'               => esc_html__( 'Nuttergood Membership', 'nuttergood' ),
				'slug'               => 'greenpath-membership',
				'source'             => GREENPATH_INC_ROOT_DIR . '/plugins/greenpath-membership.zip',
				'version'            => '1.0',
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'               => esc_html__( 'Revolution Slider', 'nuttergood' ),
				'slug'               => 'revslider',
				'source'             => GREENPATH_INC_ROOT_DIR . '/plugins/revslider.zip',
				'version'            => '6.6.20',
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'     => esc_html__( 'Elementor Page Builder', 'nuttergood' ),
				'slug'     => 'elementor',
				'required' => true,
			),
			array(
				'name'     => esc_html__( 'Qi Addons for Elementor', 'nuttergood' ),
				'slug'     => 'qi-addons-for-elementor',
				'required' => true,
			),
			array(
				'name'     => esc_html__( 'Qi Blocks', 'nuttergood' ),
				'slug'     => 'qi-blocks',
				'required' => true,
			),
			array(
				'name'     => esc_html__( 'WooCommerce Plugin', 'nuttergood' ),
				'slug'     => 'woocommerce',
				'required' => false,
			),
			array(
				'name'     => esc_html__( 'Contact Form 7', 'nuttergood' ),
				'slug'     => 'contact-form-7',
				'required' => false,
			),
			array(
				'name'     => esc_html__( 'Instagram Feed', 'nuttergood' ),
				'slug'     => 'instagram-feed',
				'required' => false,
			),
			array(
				'name'     => esc_html__( 'YITH Quick View', 'nuttergood' ),
				'slug'     => 'yith-woocommerce-quick-view',
				'required' => false,
			),
			array(
				'name'     => esc_html__( 'YITH Wishlist', 'nuttergood' ),
				'slug'     => 'yith-woocommerce-wishlist',
				'required' => false,
			),
			array(
				'name'     => esc_html__( 'YITH WooCommerce Compare', 'nuttergood' ),
				'slug'     => 'yith-woocommerce-compare',
				'required' => false,
			),
			array(
				'name'     => esc_html__( 'YITH Color and Label Variations', 'nuttergood' ),
				'slug'     => 'yith-color-and-label-variations-for-woocommerce',
				'required' => false,
			),
		);

		$config = array(
			'domain'       => 'nuttergood',
			'default_path' => '',
			'parent_slug'  => 'themes.php',
			'capability'   => 'edit_theme_options',
			'menu'         => 'install-required-plugins',
			'has_notices'  => true,
			'is_automatic' => false,
			'message'      => '',
			'strings'      => array(
				'page_title'                      => esc_html__( 'Install Required Plugins', 'nuttergood' ),
				'menu_title'                      => esc_html__( 'Install Plugins', 'nuttergood' ),
				'installing'                      => esc_html__( 'Installing Plugin: %s', 'nuttergood' ),
				'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'nuttergood' ),
				'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'nuttergood' ),
				'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'nuttergood' ),
				'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this website for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'nuttergood' ),
				'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'nuttergood' ),
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'nuttergood' ),
				'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this website for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'nuttergood' ),
				'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'nuttergood' ),
				'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this website for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'nuttergood' ),
				'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'nuttergood' ),
				'activate_link'                   => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'nuttergood' ),
				'return'                          => esc_html__( 'Return to Required Plugins Installer', 'nuttergood' ),
				'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'nuttergood' ),
				'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'nuttergood' ),
				'nag_type'                        => 'updated',
			),
		);

		tgmpa( $plugins, $config );
	}

	add_action( 'tgmpa_register', 'greenpath_register_required_plugins' );
}
