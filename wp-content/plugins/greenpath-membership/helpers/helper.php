<?php

if ( ! function_exists( 'greenpath_membership_include_membership_is_installed' ) ) {
	/**
	 * Function that set case is installed element for framework functionality
	 *
	 * @param bool $installed
	 * @param string $plugin - plugin name
	 *
	 * @return bool
	 */
	function greenpath_membership_include_membership_is_installed( $installed, $plugin ) {

		if ( 'membership' === $plugin ) {
			return class_exists( 'GreenPathMembership' );
		}

		return $installed;
	}

	add_filter( 'qode_framework_filter_is_plugin_installed', 'greenpath_membership_include_membership_is_installed', 10, 2 );
}

if ( ! function_exists( 'greenpath_membership_get_membership_redirect_url' ) ) {
	/**
	 * Function that return url for login redirection
	 *
	 * @param string $redirect_url
	 *
	 * @return string
	 */
	function greenpath_membership_get_membership_redirect_url( $redirect_url = '' ) {
		$page_id       = qode_framework_get_page_id();
		$redirect_uri  = esc_url( home_url( '/' ) );
		$dashboard_url = greenpath_membership_get_dashboard_page_url();

		if ( isset( $redirect_url ) && ! empty( $redirect_url ) ) {
			$redirect_uri = wp_unslash( $redirect_url );
		} elseif ( ! empty( $dashboard_url ) ) {
			$redirect_uri = $dashboard_url;
		} elseif ( $page_id > 0 ) {
			$redirect_uri = get_permalink( $page_id );
		}

		return apply_filters( 'greenpath_membership_filter_redirect_url', esc_url( $redirect_uri ) );
	}
}

if ( ! function_exists( 'greenpath_membership_get_dashboard_page_url' ) ) {
	/**
	 * Function that return main dashboard page url
	 *
	 * @return string
	 */
	function greenpath_membership_get_dashboard_page_url() {
		$url                = '';
		$pages              = get_all_page_ids();
		$dashboard_template = apply_filters( 'greenpath_membership_filter_dashboard_template_name', '' );

		if ( ! empty( $dashboard_template ) && ! empty( $pages ) ) {
			foreach ( $pages as $page ) {
				if ( 'publish' === get_post_status( $page ) && get_page_template_slug( $page ) === $dashboard_template ) {
					$url = esc_url( get_the_permalink( $page ) );
					break;
				} else if ( qode_framework_is_installed( 'woocommerce' ) ) {
					$url = esc_url( get_the_permalink( wc_get_page_id( 'myaccount' ) ) );
				}
			}
		}

		return $url;
	}
}

if ( ! function_exists( 'greenpath_membership_template_part' ) ) {
	/**
	 * Echo module template part.
	 *
	 * @param string $module name of the module from inc folder
	 * @param string $template full path of the template to load
	 * @param string $slug
	 * @param array $params array of parameters to pass to template
	 *
	 */
	function greenpath_membership_template_part( $module, $template, $slug = '', $params = array() ) {
		echo greenpath_membership_get_template_part( $module, $template, $slug, $params );
	}
}

if ( ! function_exists( 'greenpath_membership_get_template_part' ) ) {
	/**
	 * Loads module template part.
	 *
	 * @param string $module name of the module from inc folder
	 * @param string $template full path of the template to load
	 * @param string $slug
	 * @param array $params array of parameters to pass to template
	 *
	 * @return string - string containing html of template
	 */
	function greenpath_membership_get_template_part( $module, $template, $slug = '', $params = array() ) {
		$root = GREENPATH_MEMBERSHIP_INC_PATH;

		return qode_framework_get_template_part( $root, $module, $template, $slug, $params );
	}
}

if ( ! function_exists( 'greenpath_membership_get_grid_gutter_classes' ) ) {
	/**
	 * Function that returns classes for the gutter when sidebar is enabled
	 *
	 * @return string
	 */
	function greenpath_membership_get_grid_gutter_classes() {
		return qode_framework_is_installed( 'theme' ) ? greenpath_get_grid_gutter_classes() : '';
	}
}

if ( ! function_exists( 'greenpath_membership_render_svg_icon' ) ) {
	/**
	 * Function that print svg html icon
	 *
	 * @param string $name       - icon name
	 * @param string $class_name - custom html tag class name
	 */
	function greenpath_membership_render_svg_icon( $name, $class_name = '' ) {
		echo greenpath_membership_get_svg_icon( $name, $class_name );
	}
}

if ( ! function_exists( 'greenpath_membership_get_svg_icon' ) ) {
	/**
	 * Returns svg html
	 *
	 * @param string $name       - icon name
	 * @param string $class_name - custom html tag class name
	 *
	 * @return string|html
	 */
	function greenpath_membership_get_svg_icon( $name, $class_name = '' ) {
		return qode_framework_is_installed( 'theme' ) ? greenpath_get_svg_icon( $name, $class_name ) : '';
	}
}

if ( ! function_exists( 'greenpath_membership_get_admin_options_map_position' ) ) {
	/**
	 * Function that set dashboard admin options map position
	 *
	 * @param string $map
	 *
	 * @return int
	 */
	function greenpath_membership_get_admin_options_map_position( $map ) {
		return qode_framework_is_installed( 'core' ) ? greenpath_core_get_admin_options_map_position( $map ) : 10;
	}
}

if ( ! function_exists( 'greenpath_membership_media_settings' ) ) {
	/**
	 * Added function because of the WP User Avatar interfering with upload
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	function greenpath_membership_media_settings( $settings ) {
		$dashboard_template = apply_filters( 'greenpath_membership_filter_dashboard_template_name', '' );

		//only change on 0 if on dashboard
		if ( class_exists( 'WP_User_Avatar_Setup' ) && ! empty( $dashboard_template ) && is_page_template( $dashboard_template ) ) {
			if ( is_user_logged_in() && current_user_can( 'upload_files' ) ) {
				$settings['post']['id'] = 0;
			}
		}

		return $settings;
	}

	add_filter( 'media_view_settings', 'greenpath_membership_media_settings', 15, 1 );
}

if ( ! function_exists( 'greenpath_membership_get_my_account_page_url' ) ) {
	/**
	 * Function that returns my account page url if woo is installed and set properly
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	function greenpath_membership_get_my_account_page_url( $items ) {

		if ( qode_framework_is_installed( 'woocommerce' ) ) {
			$my_account_page_id = get_option( 'woocommerce_myaccount_page_id' );

			if ( isset( $my_account_page_id ) && ! empty( $my_account_page_id ) ) {

				$items['my-account'] = array(
					'url'         => esc_url( get_permalink( $my_account_page_id ) ),
					'text'        => esc_html__( 'My Account', 'greenpath-membership' ),
					'user_action' => 'my-account',
					'icon'        => '<svg viewBox="0 0 15 11" xml:space="preserve"><circle cx="4.687" cy="2.614" r="2.375"/><path d="M0.416,9.261h8.541c0,0-0.416-3.667-4.27-3.667C0.833,5.594,0.416,9.261,0.416,9.261z"/><circle cx="11.077" cy="2.781" r="2.12"/><path d="M9.469,8.694h5.115c0,0-0.371-3.272-3.811-3.272c-1.123,0-1.914,0.35-2.477,0.818c1.012,1.066,1.172,2.445,1.172,2.445"/></svg>',
				);
			}
		}

		return $items;
	}

	add_filter( 'greenpath_membership_filter_dashboard_navigation_action_pages', 'greenpath_membership_get_my_account_page_url' );
}
if ( ! function_exists( 'greenpath_membership_set_woo_profile_key' ) ) {
	/**
	 * Function that returns membership profile page key for WooCommerce page
	 *
	 * @return string
	 */
	function greenpath_membership_set_woo_profile_key() {
		return apply_filters( 'greenpath_membership_dashboard_profile_key', 'greenpath_membership_profile' );
	}
}

if ( ! function_exists( 'greenpath_membership_extend_woo_navigation' ) ) {
	/**
	 * Function that extend WooCommerce navigations on MyAccount page
	 *
	 * @param array $navigation
	 *
	 * @return array
	 */
	function greenpath_membership_extend_woo_navigation( $navigation ) {

		if ( greenpath_membership_get_dashboard_page_url() !== '' ) {
			$navigation[ greenpath_membership_set_woo_profile_key() ] = esc_html__( 'Nutterlygood Dashboard', 'greenpath-membership' );
		}

		return $navigation;
	}

	add_filter( 'woocommerce_account_menu_items', 'greenpath_membership_extend_woo_navigation' );
}

if ( ! function_exists( 'greenpath_membership_set_woo_navigation_membership_profile' ) ) {
	/**
	 * Function that gets the URL for an endpoint
	 *
	 * @param string $url Permalink
	 * @param string $endpoint Endpoint slug
	 *
	 * @return string
	 */
	function greenpath_membership_set_woo_navigation_membership_profile( $url, $endpoint ) {
		if ( greenpath_membership_set_woo_profile_key() === $endpoint ) {
			return greenpath_membership_get_dashboard_page_url();
		} else {
			return $url;
		}
	}

	add_filter( 'woocommerce_get_endpoint_url', 'greenpath_membership_set_woo_navigation_membership_profile', 10, 2 );
}

if ( ! function_exists( 'greenpath_membership_prevent_woo_redirection_on_user_dashboard_page' ) ) {
	/**
	 * Function that prevents redirection on our user dashboard page.
	 * When the media upload modal is opened, he triggers the admin_init hook, and the WooCommerce function redirects to my account page.
	 *
	 * @see prevent_admin_access function
	 *
	 * @param bool $prevent_access
	 *
	 * @return bool
	 */
	function greenpath_membership_prevent_woo_redirection_on_user_dashboard_page( $prevent_access ) {

		if ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], 'user-action' ) ) {
			$prevent_access = false;
		}

		return $prevent_access;
	}

	add_filter( 'woocommerce_prevent_admin_access', 'greenpath_membership_prevent_woo_redirection_on_user_dashboard_page' );
}
