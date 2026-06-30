<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_is_installed' ) ) {
	/**
	 * Function check is some plugin is installed
	 *
	 * @param string $plugin name
	 *
	 * @return bool
	 */
	function qode_wishlist_for_woocommerce_is_installed( $plugin ) {
		switch ( $plugin ) :
			case 'wishlist-premium':
				return defined( 'QODE_WISHLIST_FOR_WOOCOMMERCE_PREMIUM_VERSION' );
			case 'quick-view':
				return defined( 'QODE_QUICK_VIEW_FOR_WOOCOMMERCE_VERSION' );
			case 'wpbakery':
				return class_exists( 'WPBakeryVisualComposerAbstract' );
			case 'elementor':
				return defined( 'ELEMENTOR_VERSION' );
			case 'woocommerce':
				return class_exists( 'WooCommerce' );
			case 'wpml':
				return defined( 'ICL_SITEPRESS_VERSION' );
			default:
				return apply_filters( 'qode_wishlist_for_woocommerce_filter_is_plugin_installed', false, $plugin );

		endswitch;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_sanitize_module_template_part' ) ) {
	/**
	 * Sanitize module template part.
	 *
	 * @param string $template temp path to file that is being loaded
	 *
	 * @return string - string with template path
	 */
	function qode_wishlist_for_woocommerce_sanitize_module_template_part( $template ) {
		$available_characters = '/[^A-Za-z0-9\_\-\/]/';

		if ( ! empty( $template ) && is_scalar( $template ) ) {
			$template = preg_replace( $available_characters, '', $template );
		} else {
			$template = '';
		}

		return $template;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_template_with_slug' ) ) {
	/**
	 * Loads module template part.
	 *
	 * @param string $temp temp path to file that is being loaded
	 * @param string $slug slug that should be checked if exists
	 *
	 * @return string - string with template path
	 */
	function qode_wishlist_for_woocommerce_get_template_with_slug( $temp, $slug ) {
		$template = '';

		if ( ! empty( $temp ) ) {
			$slug = qode_wishlist_for_woocommerce_sanitize_module_template_part( $slug );

			if ( ! empty( $slug ) ) {
				$template = "$temp-$slug.php";

				if ( ! file_exists( $template ) ) {
					$template = $temp . '.php';
				}
			} else {
				$template = $temp . '.php';
			}
		}

		return $template;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_template_part' ) ) {
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
	function qode_wishlist_for_woocommerce_get_template_part( $module, $template, $slug = '', $params = array() ) {
		$module   = qode_wishlist_for_woocommerce_sanitize_module_template_part( $module );
		$template = qode_wishlist_for_woocommerce_sanitize_module_template_part( $template );

		$temp = QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/' . $module . '/' . $template;

		$template = qode_wishlist_for_woocommerce_get_template_with_slug( $temp, $slug );

		if ( ! empty( $template ) && file_exists( $template ) ) {
			// Extract params so they could be used in template.
			if ( is_array( $params ) && count( $params ) ) {
				// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
				extract( $params, EXTR_SKIP ); // @codingStandardsIgnoreLine
			}

			ob_start();

			// nosemgrep audit.php.lang.security.file.inclusion-arg.
			include qode_wishlist_for_woocommerce_get_template_with_slug( $temp, $slug );

			$html = ob_get_clean();

			return $html;
		} else {
			return '';
		}
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_template_part' ) ) {
	/**
	 * Echo module template part.
	 *
	 * @param string $module name of the module from inc folder
	 * @param string $template full path of the template to load
	 * @param string $slug
	 * @param array $params array of parameters to pass to template
	 */
	function qode_wishlist_for_woocommerce_template_part( $module, $template, $slug = '', $params = array() ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_wishlist_for_woocommerce_get_template_part( $module, $template, $slug, $params );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_option_value' ) ) {
	/**
	 * Function that returns option value using framework function but providing its own scope
	 *
	 * @param string $type option type
	 * @param string $name name of option
	 * @param string $default_value option default value
	 * @param int $post_id id of
	 *
	 * @return string value of option
	 */
	function qode_wishlist_for_woocommerce_get_option_value( $type, $name, $default_value = '', $post_id = null ) {
		$scope = QODE_WISHLIST_FOR_WOOCOMMERCE_OPTIONS_NAME;

		return qode_wishlist_for_woocommerce_framework_get_option_value( $scope, $type, $name, $default_value, $post_id );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_post_value_through_levels' ) ) {
	/**
	 * Function that returns meta value if exists, otherwise global value using framework function but providing its own scope
	 *
	 * @param string $name name of option
	 * @param int $post_id id of
	 *
	 * @return string|array value of option
	 */
	function qode_wishlist_for_woocommerce_get_post_value_through_levels( $name, $post_id = null ) {
		$scope = QODE_WISHLIST_FOR_WOOCOMMERCE_OPTIONS_NAME;

		return qode_wishlist_for_woocommerce_framework_get_post_value_through_levels( $scope, $name, $post_id );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_svg_icon' ) ) {
	/**
	 * Function that print svg html icon
	 *
	 * @param string $name - icon name
	 * @param string $class_name - custom html tag class name
	 */
	function qode_wishlist_for_woocommerce_svg_icon( $name, $class_name = '' ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_wishlist_for_woocommerce_get_svg_icon( $name, $class_name );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_svg_icon' ) ) {
	/**
	 * Returns svg html
	 *
	 * @param string $name - icon name
	 * @param string $class_name - custom html tag class name
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_get_svg_icon( $name, $class_name = '' ) {
		$html  = '';
		$class = isset( $class_name ) && ! empty( $class_name ) ? 'class=' . esc_attr( $class_name ) : '';

		switch ( $name ) {
			case 'expand':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="92px" height="92px" viewBox="0 0 92 92" xml:space="preserve"><path d="M90,6l0,20c0,2.2-1.8,4-4,4l0,0c-2.2,0-4-1.8-4-4V15.7L58.8,38.9c-0.8,0.8-1.8,1.2-2.8,1.2c-1,0-2-0.4-2.8-1.2c-1.6-1.6-1.6-4.1,0-5.7L76.3,10H66c-2.2,0-4-1.8-4-4c0-2.2,1.8-4,4-4h20c1.1,0,2.1,0.4,2.8,1.2C89.6,3.9,90,4.9,90,6z M86,62c-2.2,0-4,1.8-4,4v10.3L59.2,53.7c-1.6-1.6-4.2-1.6-5.8,0c-1.6,1.6-1.6,4.1-0.1,5.7L75.9,82H65.6c0,0,0,0,0,0c-2.2,0-4,1.8-4,4s1.8,4,4,4l20,0l0,0c1.1,0,2.3-0.4,3-1.2c0.8-0.8,1.4-1.8,1.4-2.8V66C90,63.8,88.2,62,86,62zM32.8,53.5L10,76.3V66c0-2.2-1.8-4-4-4h0c-2.2,0-4,1.8-4,4l0,20c0,1.1,0.4,2.1,1.2,2.8C4,89.6,5,90,6.1,90h20c2.2,0,4-1.8,4-4c0-2.2-1.8-4-4-4H15.7l22.8-22.8c1.6-1.6,1.5-4.1,0-5.7C37,51.9,34.4,51.9,32.8,53.5z M15.7,10.4l10.3,0h0c2.2,0,4-1.8,4-4s-1.8-4-4-4l-20,0h0c-1.1,0-2.1,0.4-2.8,1.2C2.4,4.3,2,5.3,2,6.4l0,20c0,2.2,1.8,4,4,4c2.2,0,4-1.8,4-4V16l23.1,23.1c0.8,0.8,1.8,1.2,2.8,1.2c1,0,2-0.4,2.8-1.2c1.6-1.6,1.6-4.1,0-5.7L15.7,10.4z"/></svg>';
				break;
			case 'remove':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" width="12.25" height="14" viewBox="0 0 12.25 14"><path d="M12.25,2.625v.4375a.4374.4374,0,0,1-.4375.4375H11.375v9.1875A1.3128,1.3128,0,0,1,10.0625,14H2.1875A1.3128,1.3128,0,0,1,.875,12.6875V3.5H.4375A.4374.4374,0,0,1,0,3.0625V2.625a.4374.4374,0,0,1,.4375-.4375H2.6909l.93-1.55A1.4556,1.4556,0,0,1,4.7466,0H7.5039A1.4556,1.4556,0,0,1,8.6294.6372l.93,1.55h2.2534A.4374.4374,0,0,1,12.25,2.625ZM10.0625,3.5H2.1875v9.1875h7.875Zm-6.125,7.5469V5.1406a.3282.3282,0,0,1,.3281-.3281h.6563a.3282.3282,0,0,1,.3281.3281v5.9063a.3282.3282,0,0,1-.3281.3281H4.2656A.3282.3282,0,0,1,3.9375,11.0469Zm.2842-8.8594H8.0283l-.4775-.7954a.1818.1818,0,0,0-.1406-.08H4.8394a.1818.1818,0,0,0-.1406.08ZM7,11.0469V5.1406a.3282.3282,0,0,1,.3281-.3281h.6563a.3282.3282,0,0,1,.3281.3281v5.9063a.3282.3282,0,0,1-.3281.3281H7.3281A.3282.3282,0,0,1,7,11.0469Z"/></svg>';
				break;
			case 'close':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" width="18.1213" height="18.1213" viewBox="0 0 18.1213 18.1213" stroke-miterlimit="10" stroke-width="2"><line x1="1.0607" y1="1.0607" x2="17.0607" y2="17.0607"/><line x1="17.0607" y1="1.0607" x2="1.0607" y2="17.0607"/></svg>';
				break;
			case 'add':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"><rect y="6" width="14" height="2"/><rect y="6" width="14" height="2" transform="translate(0 14) rotate(-90)"/></svg>';
				break;
			case 'check':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" width="16.818" height="12.783" viewBox="0 0 16.818 12.783"><path d="M1,7l4.987,4L15,1" transform="translate(0.406 0.412)" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"/></svg>';
				break;
			case 'shopping-cart':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="128" height="128"><path d="M 5 7 C 4.449219 7 4 7.449219 4 8 C 4 8.550781 4.449219 9 5 9 L 7.21875 9 L 9.84375 19.5 C 10.066406 20.390625 10.863281 21 11.78125 21 L 23.25 21 C 24.152344 21 24.917969 20.402344 25.15625 19.53125 L 27.75 10 L 11 10 L 11.5 12 L 25.15625 12 L 23.25 19 L 11.78125 19 L 9.15625 8.5 C 8.933594 7.609375 8.136719 7 7.21875 7 Z M 22 21 C 20.355469 21 19 22.355469 19 24 C 19 25.644531 20.355469 27 22 27 C 23.644531 27 25 25.644531 25 24 C 25 22.355469 23.644531 21 22 21 Z M 13 21 C 11.355469 21 10 22.355469 10 24 C 10 25.644531 11.355469 27 13 27 C 14.644531 27 16 25.644531 16 24 C 16 22.355469 14.644531 21 13 21 Z M 13 23 C 13.5625 23 14 23.4375 14 24 C 14 24.5625 13.5625 25 13 25 C 12.4375 25 12 24.5625 12 24 C 12 23.4375 12.4375 23 13 23 Z M 22 23 C 22.5625 23 23 23.4375 23 24 C 23 24.5625 22.5625 25 22 25 C 21.4375 25 21 24.5625 21 24 C 21 23.4375 21.4375 23 22 23 Z"/></svg>';
				break;
			case 'spinner':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M304 48c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-48 368c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zm208-208c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zM96 256c0-26.51-21.49-48-48-48S0 229.49 0 256s21.49 48 48 48 48-21.49 48-48zm12.922 99.078c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.491-48-48-48zm294.156 0c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.49-48-48-48zM108.922 60.922c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.491-48-48-48z"></path></svg>';
				break;
			case 'heart':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" width="21" height="17" viewBox="0 0 21 17" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path fill="none" d="M20,5.4595A4.6116,4.6116,0,0,0,15.25,1,5.2377,5.2377,0,0,0,10.5,3.8378,5.2377,5.2377,0,0,0,5.75,1,4.6116,4.6116,0,0,0,1,5.4595C1,9.4122,6.1277,13.4155,10.5,16,14.8721,13.4155,20,9.4122,20,5.4595Z"/></svg>';
				break;
			case 'heart-o':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" width="21" height="17" viewBox="0 0 21 17"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20,5.4595A4.6116,4.6116,0,0,0,15.25,1,5.2377,5.2377,0,0,0,10.5,3.8378,5.2377,5.2377,0,0,0,5.75,1,4.6116,4.6116,0,0,0,1,5.4595C1,9.4122,6.1277,13.4155,10.5,16,14.8721,13.4155,20,9.4122,20,5.4595Z"/></svg>';
				break;
			case 'hearts':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" width="25.7257" height="22.454" viewBox="0 0 25.7257 22.454" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M15,13.0228a3.53,3.53,0,0,0-3.5-3.5671,3.8286,3.8286,0,0,0-3.5,2.27,3.8286,3.8286,0,0,0-3.5-2.27A3.53,3.53,0,0,0,1,13.0228c0,3.1617,3.7783,6.3639,7,8.4312C11.2215,19.3867,15,16.1845,15,13.0228Z"/><path d="M24.7257,3.3792a2.4409,2.4409,0,0,0-2.5-2.3781,2.7519,2.7519,0,0,0-2.5,1.5133,2.7519,2.7519,0,0,0-2.5-1.5133,2.4409,2.4409,0,0,0-2.5,2.3781c0,2.1078,2.6988,4.2426,5,5.6208C22.0268,7.6218,24.7257,5.487,24.7257,3.3792Z"/></svg>';
				break;
			case 'notify':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" width="10.2139" height="11" viewBox="0 0 10.2139 11"><path d="M10.2139,8.6426a.7963.7963,0,0,1-.7856.7861h-2.75A1.5712,1.5712,0,0,1,5.1069,11,1.5712,1.5712,0,0,1,3.5356,9.4287H.7856a.757.757,0,0,1-.5527-.2334A.7557.7557,0,0,1,0,8.6426a5.3877,5.3877,0,0,0,.5586-.54A4.8583,4.8583,0,0,0,1.08,7.3691a5.5234,5.5234,0,0,0,.4575-.9727,7.172,7.172,0,0,0,.3066-1.2646,10.0089,10.0089,0,0,0,.12-1.5957,2.5545,2.5545,0,0,1,.7183-1.7344A3.0328,3.0328,0,0,1,4.5669.8291.6156.6156,0,0,1,4.5176.59a.5694.5694,0,0,1,.1719-.418.593.593,0,0,1,.835,0A.5713.5713,0,0,1,5.6963.59.6156.6156,0,0,1,5.647.8291a3.0328,3.0328,0,0,1,1.8848.9727A2.5545,2.5545,0,0,1,8.25,3.5361a10.0089,10.0089,0,0,0,.12,1.5957,7.172,7.172,0,0,0,.3066,1.2646,5.5234,5.5234,0,0,0,.4575.9727,4.8583,4.8583,0,0,0,.5215.7334A5.3877,5.3877,0,0,0,10.2139,8.6426ZM5.2051,10.4111a.0871.0871,0,0,0-.0981-.0986.89.89,0,0,1-.8838-.8838.0984.0984,0,1,0-.1968,0,1.0786,1.0786,0,0,0,1.0806,1.08A.0864.0864,0,0,0,5.2051,10.4111Z"/></svg>';
				break;
			case 'share':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>';
				break;
			case 'facebook':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="45px" height="45px" viewBox="0 0 45 45" xml:space="preserve"><path d="M26.4,25.2v18.3h-8.7V25.2h-7.2v-7.8h7.2v-5.7C17.1,6.6,21,2.1,26.4,1.5c0.6,0,1.2,0,1.8,0c2.1,0,4.2,0.3,6.3,0.6v6.6h-3.6c-2.1-0.6-4.2,0.9-4.5,3c0,0.3,0,0.6,0,0.9v4.8h7.8L33,25.2H26.4z"/></svg>';
				break;
			case 'twitter':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="45px" height="45px" viewBox="0 0 45 45" xml:space="preserve"><path d="M35.4,2h6.9L27.3,19.4L45,43H31.1L20.2,28.7L7.8,43H0.9L17,24.4L0,2h14.2l9.8,13.1L35.4,2z M33,38.8h3.8L12.2,5.9H8L33,38.8z"/></svg>';
				break;
			case 'linkedin':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="45px" height="45px" viewBox="0 0 45 45" xml:space="preserve"><path d="M1.5,6.6c0-2.7,2.4-5.1,5.1-5.1s5.1,2.4,5.1,5.1s-2.4,5.1-5.1,5.1S1.5,9.3,1.5,6.6L1.5,6.6z M2.1,43.5V15.6h8.7v27.9H2.1zM34.8,43.5V29.7c0-3.3,0-7.5-4.5-7.5c-4.5,0.3-5.1,3.9-5.1,7.5v13.8h-8.7V15.6h8.4v3.9l0,0c1.8-3,4.8-4.8,8.1-4.5c8.7,0,10.5,5.7,10.5,13.2v15.3H34.8L34.8,43.5z"/></svg>';
				break;
			case 'pinterest':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="45px" height="45px" viewBox="0 0 45 45" xml:space="preserve"><path d="M40.5,14.7c0,8.3-4.5,17.8-14.4,17.8c-2.7,0-5.1-1.1-6.6-3.2c-2.1,8-2.1,9.2-6.6,15.5c-0.6,0.3-0.3,0.3-0.6,0C12,43.1,12,41.4,12,39.7c0-5.5,2.7-13.5,3.9-18.7c-0.6-1.4-0.9-2.9-0.9-4.6c0-7.2,8.7-8.3,8.7-2.3c0,3.4-2.4,6.9-2.4,10c0,2.3,1.8,4,4.2,4l0,0c6.6,0,8.4-8.9,8.4-13.8c0-6.3-4.8-9.8-11.1-9.8C15.9,4.1,9.9,9.5,9.6,16.2c0,0.3,0,0.6,0,0.6c0,3.4,2.1,5.2,2.1,6c0.3,0.9-0.3,3.2-1.2,3.2c-2.4,0-6-3.4-6-9.8c0-10,9.6-16.1,19.2-16.1C32.4,0.1,40.5,5.8,40.5,14.7z"/></svg>';
				break;
			case 'tumblr':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="45px" height="45px" viewBox="0 0 45 45" xml:space="preserve"><path d="M26.7,42C17.1,42,15,35.1,15,31.2V20.4h-3.6c-0.6,0-0.9-0.3-0.9-0.9v-5.1c0-0.6,0.3-0.9,0.9-1.2c3.9-1.5,6.6-4.8,6.6-9C18,3.3,18.6,3,19.2,3h5.7c0.3,0,0.9,0.3,0.9,0.9v8.7h6.6c0.3,0,0.9,0.3,0.9,0.9l0,0v6.3c0,0.3-0.3,0.9-0.9,0.9h-6.6v10.2c0,2.7,1.8,4.2,5.4,2.7c0-0.6,0.6-0.6,0.9-0.6c0.3,0,0.6,0.3,0.6,0.6l1.8,4.8c0,0.3,0.3,0.9,0,1.2C32,41.4,29.4,42,26.7,42z"/></svg>';
				break;
			case 'vk':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="45px" height="45px" viewBox="0 0 45 45" xml:space="preserve"><path d="M37.4,22.2c-0.5,0.8-0.8,1.3,0,2.2c0.3,0.5,6.7,6.5,7.5,9.5c0.5,1.3-0.3,2.2-1.6,2.2h-4.8c-1.9,0-2.4-1.6-5.9-4.9c-2.9-3-4.3-3.2-5.1-3.2c-1.6,0-1.3,0.5-1.3,6.2c0,1.4-0.3,1.9-3.5,1.9c-6.1-0.5-11.7-3.8-14.9-9.2c-6.1-8.6-7.7-15.1-7.7-16.5C0.1,9.5,0.3,9,1.7,9h4.8c1.3,0,1.9,0.5,2.1,1.9c2.4,7,6.4,13.2,8,13.2c0.5,0,0.8-0.3,0.8-1.9v-7.3c0.3-3.2-1.3-3.5-1.3-4.9c0-0.5,0.5-1.1,1.1-1.1l0,0h7.7c1.1,0,1.3,0.5,1.3,1.9v9.7c0,1.1,0.5,1.3,0.8,1.3c0.5,0,1.1-0.3,2.4-1.6c2.4-3,4.5-6.5,6.1-10.3C35.8,9.5,36.6,9,37.7,9h4.8c1.6,0,1.9,0.8,1.6,1.9C43.3,13.9,37.4,22.2,37.4,22.2z"/></svg>';
				break;
			case 'email':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="45px" height="45px" viewBox="0 0 45 45" xml:space="preserve"><path d="M45,9.8v25.5c0,2.3-1.9,4.2-4.2,4.2c0,0,0,0,0,0H4.2c-2.3,0-4.2-1.9-4.2-4.2c0,0,0,0,0,0V9.8c0-2.3,1.9-4.2,4.2-4.3c0,0,0,0,0,0h36.6C43.1,5.5,45,7.4,45,9.8C45,9.7,45,9.7,45,9.8z M4.2,9.8v3.6c2,1.6,5.1,4.1,11.8,9.4c1.5,1.2,4.4,4,6.5,4c2,0,5-2.8,6.5-4c6.7-5.3,9.9-7.8,11.8-9.4V9.8H4.2z M40.8,35.3V18.8c-2,1.6-4.9,3.9-9.2,7.3c-2,1.6-5.3,4.9-9.1,4.9c-3.8,0-7.1-3.3-9.1-4.9c-4.4-3.4-7.2-5.7-9.2-7.3v16.4H40.8z"/></svg>';
				break;
			case 'whatsapp':
				$html = '<svg ' . esc_attr( $class ) . ' xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="45px" height="45px" viewBox="0 0 45 45" xml:space="preserve"><path d="M45,22.3c-0.1,12.3-10.2,22.3-22.5,22.3h0c-3.7,0-7.4-0.9-10.7-2.7L0,45l3.2-11.5C-3,22.8,0.7,9.1,11.3,3c8.7-5,19.8-3.6,26.9,3.6C42.5,10.7,44.9,16.4,45,22.3z M41.2,22.3c-0.1-4.9-2.1-9.7-5.6-13.1C28.4,2,16.6,2,9.4,9.2c-6.1,6.1-7.2,15.6-2.6,22.9l0.4,0.7l-1.9,6.8l7-1.8l0.7,0.4c2.9,1.7,6.1,2.6,9.4,2.6C32.8,40.9,41.1,32.6,41.2,22.3L41.2,22.3zM33.7,27.6c0.2,0.9,0,1.8-0.3,2.7c-0.8,1.4-2.2,2.3-3.8,2.7c-2.3,0.3-4.7-0.2-6.7-1.4c-3.8-1.9-7.1-4.8-9.5-8.4c-1.3-1.7-2.1-3.7-2.3-5.8c0-1.8,0.7-3.4,1.9-4.6c0.4-0.4,0.9-0.7,1.5-0.7c0.4,0,0.7,0,1.1,0c0.3,0,0.8-0.1,1.3,1c0.5,1.1,1.6,3.9,1.7,4.1c0.2,0.3,0.2,0.7,0.1,1c-1.1,2.1-2.2,2.1-1.6,3c1.7,3,4.4,5.3,7.6,6.6c0.6,0.3,0.9,0.2,1.2-0.1c0.3-0.4,1.4-1.6,1.8-2.2c0.4-0.6,0.7-0.5,1.3-0.3c0.5,0.2,3.3,1.5,3.8,1.8C33.2,27.2,33.6,27.4,33.7,27.6L33.7,27.6z"/></svg>';
				break;
		}

		return apply_filters( 'qode_wishlist_for_woocommerce_filter_svg_icon', $html, $name, $class_name );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_icon_html' ) ) {
	/**
	 * Function that return icon html content
	 *
	 * @param string|int $custom_icon - icon value
	 *
	 * @return string - SVG icon or Image
	 */
	function qode_wishlist_for_woocommerce_get_icon_html( $custom_icon ) {
		$check_image_url = wp_get_attachment_url( $custom_icon );

		if ( strpos( $check_image_url, '.svg' ) !== false ) {
			// phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$get_svg_content = @file_get_contents( $check_image_url );

			if ( ! empty( $get_svg_content ) ) {
				$icon_html = qode_wishlist_for_woocommerce_framework_wp_kses_html( 'svg', $get_svg_content );
			} else {
				$icon_html = esc_html__( 'Please upload a valid SVG icon', 'qode-wishlist-for-woocommerce' );
			}
		} else {
			$icon_html = wp_get_attachment_image( $custom_icon, 'full' );
		}

		return $icon_html;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_class_attribute' ) ) {
	/**
	 * Function that echoes class attribute
	 *
	 * @param string|array $value - value of class attribute
	 *
	 * @see qode_wishlist_for_woocommerce_get_class_attribute()
	 */
	function qode_wishlist_for_woocommerce_class_attribute( $value ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_wishlist_for_woocommerce_get_class_attribute( $value );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_class_attribute' ) ) {
	/**
	 * Function that returns generated class attribute
	 *
	 * @param string|array $value - value of class attribute
	 *
	 * @return string generated class attribute
	 *
	 * @see qode_wishlist_for_woocommerce_get_inline_attr()
	 */
	function qode_wishlist_for_woocommerce_get_class_attribute( $value ) {
		return qode_wishlist_for_woocommerce_get_inline_attr( $value, 'class', ' ' );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_id_attribute' ) ) {
	/**
	 * Function that echoes id attribute
	 *
	 * @param string|array $value - value of id attribute
	 *
	 * @see qode_wishlist_for_woocommerce_get_id_attribute()
	 */
	function qode_wishlist_for_woocommerce_id_attribute( $value ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_wishlist_for_woocommerce_get_id_attribute( $value );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_id_attribute' ) ) {
	/**
	 * Function that returns generated id attribute
	 *
	 * @param string|array $value - value of id attribute
	 *
	 * @return string generated id attribute
	 *
	 * @see qode_wishlist_for_woocommerce_get_inline_attr()
	 */
	function qode_wishlist_for_woocommerce_get_id_attribute( $value ) {
		return qode_wishlist_for_woocommerce_get_inline_attr( $value, 'id', ' ' );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_inline_style' ) ) {
	/**
	 * Function that echoes generated style attribute
	 *
	 * @param string|array $value - attribute value
	 *
	 * @see qode_wishlist_for_woocommerce_get_inline_style()
	 */
	function qode_wishlist_for_woocommerce_inline_style( $value ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_wishlist_for_woocommerce_get_inline_style( $value );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_inline_style' ) ) {
	/**
	 * Function that generates style attribute and returns generated string
	 *
	 * @param string|array $value - value of style attribute
	 *
	 * @return string generated style attribute
	 *
	 * @see qode_wishlist_for_woocommerce_get_inline_style()
	 */
	function qode_wishlist_for_woocommerce_get_inline_style( $value ) {
		return qode_wishlist_for_woocommerce_get_inline_attr( $value, 'style', ';' );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_inline_attrs' ) ) {
	/**
	 * Echo multiple inline attributes
	 *
	 * @param array $attrs
	 * @param bool $allow_zero_values
	 */
	function qode_wishlist_for_woocommerce_inline_attrs( $attrs, $allow_zero_values = false ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_wishlist_for_woocommerce_get_inline_attrs( $attrs, $allow_zero_values );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_inline_attrs' ) ) {
	/**
	 * Generate multiple inline attributes
	 *
	 * @param array $attrs
	 * @param bool $allow_zero_values
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_get_inline_attrs( $attrs, $allow_zero_values = false ) {
		$output = '';
		if ( is_array( $attrs ) && count( $attrs ) ) {
			if ( $allow_zero_values ) {
				foreach ( $attrs as $attr => $value ) {
					$output .= ' ' . qode_wishlist_for_woocommerce_get_inline_attr( $value, $attr, '', true );
				}
			} else {
				foreach ( $attrs as $attr => $value ) {
					$output .= ' ' . qode_wishlist_for_woocommerce_get_inline_attr( $value, $attr );
				}
			}
		}

		$output = ltrim( $output );

		return $output;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_inline_attr' ) ) {
	/**
	 * Function that generates html attribute
	 *
	 * @param string|array $value value of html attribute
	 * @param string $attr - name of html attribute to generate
	 * @param string $glue - glue with which to implode $attr. Used only when $attr is arrayed
	 * @param bool $allow_zero_values - allow data to have zero value
	 *
	 * @return string generated html attribute
	 */
	function qode_wishlist_for_woocommerce_get_inline_attr( $value, $attr, $glue = '', $allow_zero_values = false ) {
		if ( $allow_zero_values ) {
			if ( '' !== $value ) {

				if ( is_array( $value ) && count( $value ) ) {
					$properties = implode( $glue, $value );
				} else {
					$properties = $value;
				}

				return $attr . '="' . esc_attr( $properties ) . '"';
			}
		} else {
			if ( ! empty( $value ) ) {

				if ( is_array( $value ) && count( $value ) ) {
					$properties = implode( $glue, $value );
				} elseif ( '' !== $value ) {
					$properties = $value;
				} else {
					return '';
				}

				return $attr . '="' . esc_attr( $properties ) . '"';
			}
		}

		return '';
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_string_ends_with' ) ) {
	/**
	 * Checks if $haystack ends with $needle and returns proper bool value
	 *
	 * @param string $haystack - to check
	 * @param string $needle - on end to match
	 *
	 * @return bool
	 */
	function qode_wishlist_for_woocommerce_string_ends_with( $haystack, $needle ) {
		if ( '' !== $haystack && '' !== $needle ) {
			return ( substr( $haystack, - strlen( $needle ), strlen( $needle ) ) === $needle );
		}

		return false;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_string_ends_with_allowed_units' ) ) {
	/**
	 * Checks if $haystack ends with predefined needles and returns proper bool value
	 *
	 * @param string $haystack - to check
	 *
	 * @return bool
	 */
	function qode_wishlist_for_woocommerce_string_ends_with_allowed_units( $haystack ) {
		$result  = false;
		$needles = array( 'px', '%', 'em', 'rem', 'vh', 'vw', ')' );

		if ( '' !== $haystack ) {
			foreach ( $needles as $needle ) {
				if ( qode_wishlist_for_woocommerce_string_ends_with( $haystack, $needle ) ) {
					$result = true;
				}
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_dynamic_style' ) ) {
	/**
	 * Outputs css based on passed selectors and properties
	 *
	 * @param array|string $selector
	 * @param array $properties
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_dynamic_style( $selector, $properties ) {
		$output = '';
		// check if selector and rules are valid data.
		if ( ! empty( $selector ) && ( is_array( $properties ) && count( $properties ) ) ) {

			if ( is_array( $selector ) && count( $selector ) ) {
				$output .= implode( ', ', $selector );
			} else {
				$output .= $selector;
			}

			$output .= ' { ';
			foreach ( $properties as $prop => $value ) {
				if ( '' !== $prop ) {

					if ( 'font-family' === $prop ) {
						$output .= $prop . ': "' . esc_attr( $value ) . '";';
					} else {
						$output .= $prop . ': ' . esc_attr( $value ) . ';';
					}
				}
			}

			$output .= '}';
		}

		return $output;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_dynamic_style_responsive' ) ) {
	/**
	 * Outputs css based on passed selectors and properties
	 *
	 * @param array|string $selector
	 * @param array $properties
	 * @param string $min_width
	 * @param string $max_width
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_dynamic_style_responsive( $selector, $properties, $min_width = '', $max_width = '' ) {
		$output = '';
		// check if min width or max width is set.
		if ( ! empty( $min_width ) || ! empty( $max_width ) ) {
			$output .= '@media only screen';

			if ( ! empty( $min_width ) ) {
				$output .= ' and (min-width: ' . $min_width . 'px)';
			}

			if ( ! empty( $max_width ) ) {
				$output .= ' and (max-width: ' . $max_width . 'px)';
			}

			$output .= ' { ';

			$output .= qode_wishlist_for_woocommerce_dynamic_style( $selector, $properties );

			$output .= '}';
		}

		return $output;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_pages' ) ) {
	/**
	 * Returns array of pages item
	 *
	 * @param bool $enable_default - add first element empty for default value
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_pages( $enable_default = false ) {
		$options = array();

		$pages = get_all_page_ids();
		if ( ! empty( $pages ) ) {

			if ( $enable_default ) {
				$options[''] = esc_html__( 'Default', 'qode-wishlist-for-woocommerce' );
			}

			foreach ( $pages as $page_id ) {
				$options[ $page_id ] = get_the_title( $page_id );
			}
		}

		return $options;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_users' ) ) {
	/**
	 * Returns array of users
	 *
	 * @param bool $enable_default - add first element empty for default value
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_users( $enable_default = false ) {
		$options = array();

		$users_args = array(
			'orderby' => 'display_name',
		);
		$users      = get_users( $users_args );

		if ( ! empty( $users ) ) {

			if ( $enable_default ) {
				$options[''] = esc_html__( 'Default', 'qode-wishlist-for-woocommerce' );
			}

			foreach ( $users as $user ) {
				$options[ $user->ID ] = $user->display_name;
			}
		}

		return $options;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_cpt_items' ) ) {
	/**
	 * Returns array of custom post items
	 *
	 * @param string $cpt_slug
	 * @param array $args
	 * @param bool $enable_default - add first element empty for default value
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_get_cpt_items( $cpt_slug = 'product', $args = array(), $enable_default = true ) {
		$options    = array();
		$query_args = array(
			'post_status'    => 'publish',
			'post_type'      => $cpt_slug,
			'posts_per_page' => '-1',
			'fields'         => 'ids',
		);

		if ( ! empty( $args ) ) {
			foreach ( $args as $key => $value ) {
				if ( ! empty( $value ) ) {
					$query_args[ $key ] = $value;
				}
			}
		}

		$cpt_items = new WP_Query( $query_args );

		if ( $cpt_items->have_posts() ) {

			if ( $enable_default ) {
				$options[''] = esc_html__( 'Default', 'qode-wishlist-for-woocommerce' );
			}

			foreach ( $cpt_items->posts as $id ) :
				$options[ $id ] = get_the_title( $id );
			endforeach;
		}

		wp_reset_postdata();

		return $options;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_select_type_options_pool' ) ) {
	/**
	 * Function that returns array with pool of options for select fields in framework
	 *
	 * @param string $type - type of select field
	 * @param bool $enable_default - add first element empty for default value
	 * @param array $exclude_options - array of items to exclude
	 * @param array $include_options - array of items to include
	 *
	 * @return array escaped output
	 */
	function qode_wishlist_for_woocommerce_get_select_type_options_pool( $type, $enable_default = true, $exclude_options = array(), $include_options = array() ) {
		$options = array();

		if ( $enable_default ) {
			$options[''] = esc_html__( 'Default', 'qode-wishlist-for-woocommerce' );
		}

		switch ( $type ) {
			case 'title_tag':
				$options['h1'] = esc_html__( 'H1', 'qode-wishlist-for-woocommerce' );
				$options['h2'] = esc_html__( 'H2', 'qode-wishlist-for-woocommerce' );
				$options['h3'] = esc_html__( 'H3', 'qode-wishlist-for-woocommerce' );
				$options['h4'] = esc_html__( 'H4', 'qode-wishlist-for-woocommerce' );
				$options['h5'] = esc_html__( 'H5', 'qode-wishlist-for-woocommerce' );
				$options['h6'] = esc_html__( 'H6', 'qode-wishlist-for-woocommerce' );
				$options['p']  = esc_html__( 'P', 'qode-wishlist-for-woocommerce' );
				break;
			case 'link_target':
				$options['_self']  = esc_html__( 'Same Window', 'qode-wishlist-for-woocommerce' );
				$options['_blank'] = esc_html__( 'New Window', 'qode-wishlist-for-woocommerce' );
				break;
			case 'border_style':
				$options['solid']  = esc_html__( 'Solid', 'qode-wishlist-for-woocommerce' );
				$options['dashed'] = esc_html__( 'Dashed', 'qode-wishlist-for-woocommerce' );
				$options['dotted'] = esc_html__( 'Dotted', 'qode-wishlist-for-woocommerce' );
				break;
			case 'font_weight':
				$options['100'] = esc_html__( 'Thin (100)', 'qode-wishlist-for-woocommerce' );
				$options['200'] = esc_html__( 'Extra Light (200)', 'qode-wishlist-for-woocommerce' );
				$options['300'] = esc_html__( 'Light (300)', 'qode-wishlist-for-woocommerce' );
				$options['400'] = esc_html__( 'Normal (400)', 'qode-wishlist-for-woocommerce' );
				$options['500'] = esc_html__( 'Medium (500)', 'qode-wishlist-for-woocommerce' );
				$options['600'] = esc_html__( 'Semi Bold (600)', 'qode-wishlist-for-woocommerce' );
				$options['700'] = esc_html__( 'Bold (700)', 'qode-wishlist-for-woocommerce' );
				$options['800'] = esc_html__( 'Extra Bold (800)', 'qode-wishlist-for-woocommerce' );
				$options['900'] = esc_html__( 'Black (900)', 'qode-wishlist-for-woocommerce' );
				break;
			case 'font_style':
				$options['normal']  = esc_html__( 'Normal', 'qode-wishlist-for-woocommerce' );
				$options['italic']  = esc_html__( 'Italic', 'qode-wishlist-for-woocommerce' );
				$options['oblique'] = esc_html__( 'Oblique', 'qode-wishlist-for-woocommerce' );
				$options['initial'] = esc_html__( 'Initial', 'qode-wishlist-for-woocommerce' );
				$options['inherit'] = esc_html__( 'Inherit', 'qode-wishlist-for-woocommerce' );
				break;
			case 'text_transform':
				$options['none']       = esc_html__( 'None', 'qode-wishlist-for-woocommerce' );
				$options['capitalize'] = esc_html__( 'Capitalize', 'qode-wishlist-for-woocommerce' );
				$options['uppercase']  = esc_html__( 'Uppercase', 'qode-wishlist-for-woocommerce' );
				$options['lowercase']  = esc_html__( 'Lowercase', 'qode-wishlist-for-woocommerce' );
				$options['initial']    = esc_html__( 'Initial', 'qode-wishlist-for-woocommerce' );
				$options['inherit']    = esc_html__( 'Inherit', 'qode-wishlist-for-woocommerce' );
				break;
			case 'text_decoration':
				$options['none']         = esc_html__( 'None', 'qode-wishlist-for-woocommerce' );
				$options['underline']    = esc_html__( 'Underline', 'qode-wishlist-for-woocommerce' );
				$options['overline']     = esc_html__( 'Overline', 'qode-wishlist-for-woocommerce' );
				$options['line-through'] = esc_html__( 'Line-Through', 'qode-wishlist-for-woocommerce' );
				$options['initial']      = esc_html__( 'Initial', 'qode-wishlist-for-woocommerce' );
				$options['inherit']      = esc_html__( 'Inherit', 'qode-wishlist-for-woocommerce' );
				break;
			case 'yes_no':
				$options['yes'] = esc_html__( 'Yes', 'qode-wishlist-for-woocommerce' );
				$options['no']  = esc_html__( 'No', 'qode-wishlist-for-woocommerce' );
				break;
			case 'no_yes':
				$options['no']  = esc_html__( 'No', 'qode-wishlist-for-woocommerce' );
				$options['yes'] = esc_html__( 'Yes', 'qode-wishlist-for-woocommerce' );
				break;
		}

		if ( ! empty( $exclude_options ) ) {
			foreach ( $exclude_options as $exclude_option ) {
				if ( array_key_exists( $exclude_option, $options ) ) {
					unset( $options[ $exclude_option ] );
				}
			}
		}

		if ( ! empty( $include_options ) ) {
			foreach ( $include_options as $key => $value ) {
				if ( ! array_key_exists( $key, $options ) ) {
					$options[ $key ] = $value;
				}
			}
		}

		return apply_filters( 'qode_wishlist_for_woocommerce_filter_select_type_option', $options, $type, $enable_default, $exclude_options );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_escape_title_tag' ) ) {
	/**
	 * Function that output escape title tag variable for modules
	 *
	 * @param string $title_tag
	 */
	function qode_wishlist_for_woocommerce_escape_title_tag( $title_tag ) {
		echo esc_html( qode_wishlist_for_woocommerce_get_escape_title_tag( $title_tag ) );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_escape_title_tag' ) ) {
	/**
	 * Function that return escape title tag variable for modules
	 *
	 * @param string $title_tag
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_get_escape_title_tag( $title_tag ) {
		$allowed_tags = array(
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'p',
			'span',
			'ul',
			'ol',
		);

		$escaped_title_tag = '';
		$title_tag         = strtolower( sanitize_key( $title_tag ) );

		if ( in_array( $title_tag, $allowed_tags, true ) ) {
			$escaped_title_tag = $title_tag;
		}

		return $escaped_title_tag;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_call_shortcode' ) ) {
	/**
	 * Function that call/render shortcode
	 *
	 * @param      $base - shortcode base
	 * @param      $params - shortcode parameters
	 * @param null $content - shortcode content
	 *
	 * @return mixed|string
	 */
	function qode_wishlist_for_woocommerce_call_shortcode( $base, $params = array(), $content = null ) {
		global $shortcode_tags;

		if ( ! isset( $shortcode_tags[ $base ] ) ) {
			return false;
		}

		if ( is_array( $shortcode_tags[ $base ] ) ) {
			$shortcode = $shortcode_tags[ $base ];

			return call_user_func(
				array(
					$shortcode[0],
					$shortcode[1],
				),
				$params,
				$content,
				$base
			);
		}

		return call_user_func( $shortcode_tags[ $base ], $params, $content, $base );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_ajax_status' ) ) {
	/**
	 * Function that return status from ajax functions
	 *
	 * @param string $status - success or error
	 * @param string $message - ajax message value
	 * @param string|array $data - returned value
	 * @param string $redirect - url address
	 */
	function qode_wishlist_for_woocommerce_get_ajax_status( $status, $message, $data = null, $redirect = '' ) {
		$response = array(
			'status'   => esc_attr( $status ),
			'message'  => wp_kses_post( $message ),
			'data'     => $data,
			'redirect' => ! empty( $redirect ) ? esc_url( $redirect ) : '',
		);

		$output = wp_json_encode( $response );

		exit( $output ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_button_classes' ) ) {
	/**
	 * Function that return theme and plugin classes for button elements
	 *
	 * @param array $additional_classes
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_get_button_classes( $additional_classes = array() ) {
		$classes = array(
			'button',
		);

		if ( function_exists( 'wc_wp_theme_get_element_class_name' ) ) {
			$classes[] = wc_wp_theme_get_element_class_name( 'button' );
		}

		return implode( ' ', array_merge( $classes, $additional_classes ) );
	}
}
