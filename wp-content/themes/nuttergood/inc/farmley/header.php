<?php
/**
 * Farmley header — widget fallback + main navigation hamburger menu.
 */

if ( ! function_exists( 'nuttergood_farmley_storefront_header_widget_map' ) ) {
	/**
	 * Homepage header chrome — used on every page for a consistent bar.
	 *
	 * @return array<string, string>
	 */
	function nuttergood_farmley_storefront_header_widget_map() {
		return array(
			'header_one'   => 'extended-header-one',
			'header_two'   => 'extended-header-two',
			'sticky_one'   => 'extended-sticky-widgets',
			'sticky_two'   => 'extended-header-two',
		);
	}
}

if ( ! function_exists( 'nuttergood_header_widget_area_global_fallback' ) ) {
	/**
	 * Force the Farmley storefront header (search + menu + cart) on all pages.
	 * Inner pages were using blog-widget-area and missing search/menu widgets.
	 */
	function nuttergood_header_widget_area_global_fallback( $parameters ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return $parameters;
		}

		$widget_area  = $parameters['widget_area'] ?? 'one';
		$default_area = $parameters['default_widget_area'] ?? '';
		$map          = nuttergood_farmley_storefront_header_widget_map();

		if ( false !== strpos( $default_area, 'sticky-header-widget-area' ) ) {
			if ( 'two' === $widget_area ) {
				$parameters['custom_widget_area'] = $map['sticky_two'];
			} else {
				$parameters['custom_widget_area'] = $map['sticky_one'];
			}
			return $parameters;
		}

		if ( false !== strpos( $default_area, 'header-widget-area' ) ) {
			if ( 'two' === $widget_area ) {
				$parameters['custom_widget_area'] = $map['header_two'];
			} else {
				$parameters['custom_widget_area'] = $map['header_one'];
			}
			return $parameters;
		}

		return $parameters;
	}

	add_filter( 'greenpath_core_filter_header_widget_area', 'nuttergood_header_widget_area_global_fallback', 99 );
}

if ( ! function_exists( 'nuttergood_farmley_sync_header_meta_all_pages' ) ) {
	/**
	 * One-time: align per-page header meta with the homepage storefront bar.
	 */
	function nuttergood_farmley_sync_header_meta_all_pages() {
		if ( get_option( 'ng_farmley_header_sync_v1' ) ) {
			return;
		}

		$home_id = (int) get_option( 'page_on_front' );
		$map     = nuttergood_farmley_storefront_header_widget_map();

		$sync = array(
			'qodef_header_layout'                         => 'standard-extended',
			'qodef_header_custom_widget_area_one'         => $map['header_one'],
			'qodef_header_custom_widget_area_two'         => $map['header_two'],
			'qodef_sticky_header_custom_widget_area_one'  => $map['sticky_one'],
			'qodef_show_header_widget_areas'              => 'yes',
			'qodef_logo_height'                           => '48',
			'qodef_standard_extended_header_in_grid'      => 'yes',
		);

		if ( $home_id > 0 ) {
			foreach ( array_keys( $sync ) as $key ) {
				$home_val = get_post_meta( $home_id, $key, true );
				if ( ! empty( $home_val ) ) {
					$sync[ $key ] = $home_val;
				}
			}
		}

		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		foreach ( $pages as $page_id ) {
			if ( (int) $page_id === $home_id ) {
				continue;
			}
			foreach ( $sync as $key => $value ) {
				update_post_meta( $page_id, $key, $value );
			}
		}

		update_option( 'ng_farmley_header_sync_v1', 1, false );
	}
	add_action( 'init', 'nuttergood_farmley_sync_header_meta_all_pages', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_unify_header_logo_styles' ) ) {
	function nuttergood_farmley_unify_header_logo_styles( $style ) {
		$style .= '
#qodef-page-header .qodef-header-logo-link {
	height: auto !important;
	min-height: 0 !important;
	padding-top: 8px !important;
	padding-bottom: 8px !important;
}
#qodef-page-header .qodef-header-logo-link img.qodef-header-logo-image {
	max-height: 48px !important;
	width: auto !important;
	height: auto !important;
}
#qodef-page-mobile-header .qodef-mobile-header-logo-link img {
	max-height: 40px !important;
	width: auto !important;
	height: auto !important;
	display: block;
}
';
		return $style;
	}
	add_filter( 'greenpath_filter_add_inline_style', 'nuttergood_farmley_unify_header_logo_styles', 50 );
}

/*
 * Legacy "Shop by Categories" dropdown — replaced by Farmley main-menu hamburger below.
 * greenpath_core_get_extended_dropdown_menu() now shows Home / Shop / About Us / Contact Us.
 */
if ( ! function_exists( 'nuttergood_farmley_disable_bottom_hamburger_slot' ) ) {
	/**
	 * Menu is rendered after search in the top header (Farmley layout).
	 */
	function nuttergood_farmley_disable_bottom_hamburger_slot( $value ) {
		return 'no';
	}
	add_filter( 'qode_framework_filter_value_through_levels_qodef_standard_extended_show_extended_dropdown', 'nuttergood_farmley_disable_bottom_hamburger_slot' );
}

if ( ! function_exists( 'nuttergood_farmley_output_main_hamburger_menu' ) ) {
	/**
	 * Render main-navigation hamburger dropdown markup.
	 */
	function nuttergood_farmley_output_main_hamburger_menu() {
		if ( ! function_exists( 'greenpath_core_template_part' ) ) {
			return;
		}

		$page_id      = function_exists( 'qode_framework_get_page_id' ) ? qode_framework_get_page_id() : null;
		$opener_title = '';

		if ( function_exists( 'greenpath_core_get_post_value_through_levels' ) ) {
			$opener_title = greenpath_core_get_post_value_through_levels( 'qodef_standard_extended_extended_dropdown_opener_label', $page_id );
		}

		$opener_title = apply_filters( 'qode_framework_filter_value_through_levels_qodef_standard_extended_extended_dropdown_opener_label', $opener_title );

		if ( empty( $opener_title ) ) {
			$opener_title = __( 'Menu', 'nuttergood' );
		}

		greenpath_core_template_part(
			'header/layouts/standard-extended',
			'templates/extended-dropdown',
			'',
			array(
				'opener_title' => $opener_title,
			)
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_header_sidebar_ids' ) ) {
	/**
	 * Widget areas treated as storefront header chrome.
	 */
	function nuttergood_farmley_header_sidebar_ids() {
		return array(
			'extended-header-one',
			'extended-sticky-widgets',
			'qodef-mobile-header-widget-area-one',
			'qodef-mobile-header-widget-area-two',
			'qodef-mobile-header-widget-area-three',
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_signup_url' ) ) {
	/**
	 * Guest account icon → WooCommerce register form on My Account.
	 */
	function nuttergood_farmley_get_signup_url() {
		if ( function_exists( 'nuttergood_farmley_get_signup_page_id' ) ) {
			$signup_id = nuttergood_farmley_get_signup_page_id();
			if ( $signup_id ) {
				return get_permalink( $signup_id );
			}
		}

		$signup = get_page_by_path( 'signup' );
		if ( $signup ) {
			return get_permalink( $signup );
		}

		if ( function_exists( 'wc_get_page_permalink' ) ) {
			return add_query_arg( 'action', 'register', wc_get_page_permalink( 'myaccount' ) );
		}

		return home_url( '/' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_account_url' ) ) {
	function nuttergood_farmley_get_account_url() {
		if ( ! function_exists( 'wc_get_page_permalink' ) ) {
			return home_url( '/' );
		}

		return wc_get_page_permalink( 'myaccount' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_inject_menu_after_search' ) ) {
	/**
	 * Place hamburger menu immediately after the product search widget.
	 */
	function nuttergood_farmley_inject_menu_after_search( $params ) {
		if (
			empty( $params[0]['widget_id'] )
			|| 0 !== strpos( $params[0]['widget_id'], 'greenpath_core_woo_product_search-' )
			|| empty( $params[0]['id'] )
			|| 'extended-header-one' !== $params[0]['id']
		) {
			return $params;
		}

		static $rendered = false;

		if ( $rendered ) {
			return $params;
		}

		$rendered = true;

		$params[0]['before_widget'] = '<div class="ng-farmley-header-search-menu-group">' . $params[0]['before_widget'];

		ob_start();
		echo '<div class="ng-farmley-header-menu-slot">';
		nuttergood_farmley_output_main_hamburger_menu();
		echo '</div>';
		$menu_html = ob_get_clean();

		// Close search widget first, then render menu, then close the flex group.
		$params[0]['after_widget'] = $params[0]['after_widget'] . $menu_html . '</div>';

		return $params;
	}

	add_filter( 'dynamic_sidebar_params', 'nuttergood_farmley_inject_menu_after_search', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_prune_header_sidebars' ) ) {
	/**
	 * Remove compare counter from header widget areas.
	 */
	function nuttergood_farmley_prune_header_sidebars( $sidebars ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return $sidebars;
		}

		foreach ( nuttergood_farmley_header_sidebar_ids() as $area ) {
			if ( empty( $sidebars[ $area ] ) || ! is_array( $sidebars[ $area ] ) ) {
				continue;
			}

			$sidebars[ $area ] = array_values(
				array_filter(
					$sidebars[ $area ],
					static function ( $widget_id ) {
						return false === strpos( (string) $widget_id, 'qode_compare_for_woocommerce_compare_counter' );
					}
				)
			);
		}

		return $sidebars;
	}

	add_filter( 'sidebars_widgets', 'nuttergood_farmley_prune_header_sidebars' );
}

if ( ! function_exists( 'nuttergood_farmley_filter_header_widgets' ) ) {
	/**
	 * Header widget rules: no compare; wishlist only when logged in; guest account → signup.
	 */
	function nuttergood_farmley_filter_header_widgets( $instance, $widget, $args ) {
		if ( empty( $args['id'] ) || ! in_array( $args['id'], nuttergood_farmley_header_sidebar_ids(), true ) ) {
			return $instance;
		}

		if ( 'qode_compare_for_woocommerce_compare_counter' === $widget->id_base ) {
			return false;
		}

		if ( ! is_user_logged_in() && 'greenpath_core_qode_wishlist' === $widget->id_base ) {
			return false;
		}

		if (
			'greenpath_core_svg_icon' === $widget->id_base
			&& is_array( $instance )
			&& ! empty( $args['widget_id'] )
			&& 'greenpath_core_svg_icon-14' === $args['widget_id']
		) {
			$instance['icon_link'] = is_user_logged_in()
				? nuttergood_farmley_get_account_url()
				: nuttergood_farmley_get_signup_url();
		}

		return $instance;
	}

	add_filter( 'widget_display_callback', 'nuttergood_farmley_filter_header_widgets', 20, 3 );
}



if ( ! function_exists( 'nuttergood_farmley_main_menu_opener_label' ) ) {
	function nuttergood_farmley_main_menu_opener_label( $value ) {
		return __( 'Menu', 'nuttergood' );
	}
	add_filter( 'qode_framework_filter_value_through_levels_qodef_standard_extended_extended_dropdown_opener_label', 'nuttergood_farmley_main_menu_opener_label' );
}

if ( ! function_exists( 'nuttergood_farmley_main_menu_use_main_nav' ) ) {
	/**
	 * Use main-navigation (Home, Shop, About, Contact) instead of category dropdown menu.
	 */
	function nuttergood_farmley_main_menu_use_main_nav( $args ) {
		if ( ! empty( $args['menu_class'] ) && false !== strpos( $args['menu_class'], 'qodef-extended-dropdown' ) ) {
			$args['theme_location'] = 'main-navigation';
			$args['menu_class']     = 'qodef-extended-dropdown ng-farmley-main-menu__list';
		}

		return $args;
	}
	add_filter( 'wp_nav_menu_args', 'nuttergood_farmley_main_menu_use_main_nav', 99 );
}

if ( ! function_exists( 'nuttergood_farmley_mark_main_hamburger_menu' ) ) {
	/**
	 * Add Farmley class to the extended dropdown wrapper for styling/JS.
	 */
	function nuttergood_farmley_mark_main_hamburger_menu( $nav_menu, $args ) {
		if ( empty( $args->menu_class ) || false === strpos( $args->menu_class, 'ng-farmley-main-menu__list' ) ) {
			return $nav_menu;
		}

		return '<div class="ng-farmley-main-menu" data-ng-farmley-menu="1">' . $nav_menu . '</div>';
	}
	add_filter( 'wp_nav_menu', 'nuttergood_farmley_mark_main_hamburger_menu', 10, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_fix_menu_localhost_urls' ) ) {
	/**
	 * Rewrite leftover local dev URLs in nav menus (custom link items).
	 *
	 * @param WP_Post[] $items Menu items.
	 */
	function nuttergood_farmley_fix_menu_localhost_urls( $items ) {
		$live = untrailingslashit( home_url() );
		$from = array(
			'http://localhost/nutterlyGood',
			'https://localhost/nutterlyGood',
			'http://nutterlygood.free.nf',
			'https://nutterlygood.free.nf',
			'http://a1irwktt.infinityfree.com',
			'https://a1irwktt.infinityfree.com',
		);

		foreach ( $items as $item ) {
			if ( empty( $item->url ) ) {
				continue;
			}
			$item->url = str_replace( $from, $live, $item->url );
		}

		return $items;
	}
	add_filter( 'wp_nav_menu_objects', 'nuttergood_farmley_fix_menu_localhost_urls', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_sync_mobile_header_widgets' ) ) {
	/**
	 * Clean mobile header sidebars — remove empty blocks and duplicate drawer widgets.
	 */
	function nuttergood_farmley_sync_mobile_header_widgets() {
		if ( get_option( 'ng_farmley_mobile_header_sync_v1' ) ) {
			return;
		}

		$sidebars = get_option( 'sidebars_widgets', array() );
		if ( ! is_array( $sidebars ) ) {
			return;
		}

		$sidebars['qodef-mobile-header-widget-area-two'] = array();

		$area_three = array(
			'greenpath_membership_login_opener-5',
			'greenpath_core_woo_side_area_cart-7',
			'greenpath_membership_login_button-2',
		);

		$area_one = array(
			'greenpath_core_woo_side_area_cart-3',
			'greenpath_membership_login_opener-4',
		);

		$sidebars['qodef-mobile-header-widget-area-one']   = $area_one;
		$sidebars['qodef-mobile-header-widget-area-three'] = $area_three;

		update_option( 'sidebars_widgets', $sidebars );
		update_option( 'ng_farmley_mobile_header_sync_v1', 1, false );

		$opts = get_option( 'greenpath_core_options', array() );
		if ( is_array( $opts ) ) {
			$opts['qodef_logo_height_mobile'] = '40';
			update_option( 'greenpath_core_options', $opts );
		}
	}
	add_action( 'init', 'nuttergood_farmley_sync_mobile_header_widgets', 25 );
}