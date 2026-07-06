<?php
/**
 * Ecommerce: signup page, Razorpay + Shiprocket helpers, WooCommerce registration.
 */

if ( ! function_exists( 'nuttergood_farmley_get_signup_page_id' ) ) {
	function nuttergood_farmley_get_signup_page_id() {
		static $page_id = null;

		if ( null !== $page_id ) {
			return $page_id;
		}

		$page = get_page_by_path( 'signup' );
		$page_id = $page ? (int) $page->ID : 0;

		return $page_id;
	}
}

if ( ! function_exists( 'nuttergood_farmley_ensure_signup_page' ) ) {
	/**
	 * Create /signup page once (Elementor-free registration landing).
	 */
	function nuttergood_farmley_ensure_signup_page() {
		if ( nuttergood_farmley_get_signup_page_id() ) {
			return;
		}

		$page_id = wp_insert_post(
			array(
				'post_title'   => __( 'Sign Up', 'nuttergood' ),
				'post_name'    => 'signup',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
			true
		);

		if ( is_wp_error( $page_id ) || ! $page_id ) {
			return;
		}

		update_post_meta( $page_id, '_wp_page_template', 'page-signup.php' );
	}

	add_action( 'after_setup_theme', 'nuttergood_farmley_ensure_signup_page', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_get_wishlist_page_id' ) ) {
	function nuttergood_farmley_get_wishlist_page_id() {
		if ( function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_page_id' ) ) {
			$page_id = (int) qode_wishlist_for_woocommerce_get_wishlist_page_id();
			if ( $page_id > 0 && get_post( $page_id ) ) {
				return $page_id;
			}
		}

		$page = get_page_by_path( 'wishlist' );
		return $page ? (int) $page->ID : 0;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_wishlist_url' ) ) {
	function nuttergood_farmley_get_wishlist_url() {
		if ( function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_page_url' ) ) {
			$url = qode_wishlist_for_woocommerce_get_wishlist_page_url();
			if ( $url ) {
				return $url;
			}
		}

		$page_id = nuttergood_farmley_get_wishlist_page_id();
		return $page_id ? get_permalink( $page_id ) : home_url( '/wishlist/' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_ensure_wishlist_page' ) ) {
	function nuttergood_farmley_ensure_wishlist_page() {
		if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_page_id' ) ) {
			return;
		}

		$page_id = nuttergood_farmley_get_wishlist_page_id();
		if ( ! $page_id ) {
			$page_id = wp_insert_post(
				array(
					'post_title'   => __( 'Wishlist', 'nuttergood' ),
					'post_name'    => 'wishlist',
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_content' => '[qode_wishlist_for_woocommerce_table]',
				),
				true
			);

			if ( is_wp_error( $page_id ) || ! $page_id ) {
				return;
			}
		}

		$opts = get_option( 'qode_wishlist_for_woocommerce_options', array() );
		if ( ! is_array( $opts ) ) {
			$opts = array();
		}

		if ( empty( $opts['qode_wishlist_for_woocommerce_page_template'] ) || (int) $opts['qode_wishlist_for_woocommerce_page_template'] !== (int) $page_id ) {
			$opts['qode_wishlist_for_woocommerce_page_template'] = (string) $page_id;
			update_option( 'qode_wishlist_for_woocommerce_options', $opts );
		}
	}
	add_action( 'after_setup_theme', 'nuttergood_farmley_ensure_wishlist_page', 21 );
}

if ( ! function_exists( 'nuttergood_farmley_enable_woocommerce_registration' ) ) {
	function nuttergood_farmley_enable_woocommerce_registration() {
		if ( 'yes' !== get_option( 'woocommerce_enable_myaccount_registration' ) ) {
			update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
		}
	}

	add_action( 'after_setup_theme', 'nuttergood_farmley_enable_woocommerce_registration', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_signup_page_styles' ) ) {
	function nuttergood_farmley_signup_page_styles() {
		$is_account_page = function_exists( 'is_account_page' ) && is_account_page();

		if ( ! is_page( 'signup' ) && ! $is_account_page ) {
			return;
		}

		$css = get_template_directory() . '/assets/css/farmley-account.css';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-account',
				get_template_directory_uri() . '/assets/css/farmley-account.css',
				array( 'greenpath-main', 'greenpath-style' ),
				filemtime( $css )
			);
		}
	}

	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_signup_page_styles', 30 );
}

if ( ! function_exists( 'nuttergood_farmley_redirect_logged_in_from_signup' ) ) {
	function nuttergood_farmley_redirect_logged_in_from_signup() {
		if ( is_page( 'signup' ) && is_user_logged_in() && function_exists( 'wc_get_page_permalink' ) ) {
			wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
			exit;
		}
	}

	add_action( 'template_redirect', 'nuttergood_farmley_redirect_logged_in_from_signup' );
}

if ( ! function_exists( 'nuttergood_farmley_configure_razorpay_gateway' ) ) {
	/**
	 * Enable Razorpay gateway in WooCommerce (keys added in admin).
	 */
	function nuttergood_farmley_configure_razorpay_gateway() {
		if ( ! class_exists( 'WC_Payment_Gateways' ) ) {
			return;
		}

		$gateways = get_option( 'woocommerce_gateway_order', array() );
		if ( ! in_array( 'razorpay', $gateways, true ) ) {
			$gateways[] = 'razorpay';
			update_option( 'woocommerce_gateway_order', array_values( array_unique( $gateways ) ) );
		}

		$settings = get_option( 'woocommerce_razorpay_settings', array() );
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		if ( empty( $settings['enabled'] ) ) {
			$settings['enabled'] = 'yes';
			$settings['title']   = $settings['title'] ?? 'Pay Online (UPI, Cards, Netbanking)';
			$settings['description'] = $settings['description'] ?? __( 'Secure payment via Razorpay.', 'nuttergood' );
			update_option( 'woocommerce_razorpay_settings', $settings );
		}
	}

	add_action( 'admin_init', 'nuttergood_farmley_configure_razorpay_gateway' );
}

if ( ! function_exists( 'nuttergood_farmley_disable_razorpay_product_widgets_without_keys' ) ) {
	/**
	 * Prevent Razorpay RTB / affordability widgets from running when API keys are missing.
	 */
	function nuttergood_farmley_disable_razorpay_product_widgets_without_keys() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}

		$settings = get_option( 'woocommerce_razorpay_settings', array() );
		if ( ! is_array( $settings ) || empty( $settings['key_id'] ) || empty( $settings['key_secret'] ) ) {
			remove_action( 'woocommerce_after_add_to_cart_button', 'trigger_rtb_widget', 10 );
			remove_action( 'woocommerce_before_single_product', 'trigger_affordability_widget', 10 );
		}
	}

	add_action( 'wp', 'nuttergood_farmley_disable_razorpay_product_widgets_without_keys', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_ecommerce_admin_notice' ) ) {
	function nuttergood_farmley_ecommerce_admin_notice() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$missing = array();

		if ( ! is_plugin_active( 'woo-razorpay/woo-razorpay.php' ) ) {
			$missing[] = 'Razorpay plugin';
		} else {
			$rzp = get_option( 'woocommerce_razorpay_settings', array() );
			if ( empty( $rzp['key_id'] ) || empty( $rzp['key_secret'] ) ) {
				$missing[] = 'Razorpay API keys (WooCommerce → Settings → Payments → Razorpay)';
			}
		}

		if ( ! is_plugin_active( 'shiprocket/class-shiprocket-woocommerce-shipping.php' ) ) {
			$missing[] = 'Shiprocket plugin';
		} elseif ( ! get_option( 'woocommerce_shiprocket_settings' ) && ! get_option( 'shiprocket_api_email' ) ) {
			$missing[] = 'Shiprocket account connection (WooCommerce → Settings → Shipping → Shiprocket)';
		}

		if ( empty( $missing ) ) {
			return;
		}

		printf(
			'<div class="notice notice-warning"><p><strong>Nutterly Good ecommerce:</strong> %s</p></div>',
			esc_html( implode( ' · ', $missing ) )
		);
	}

	add_action( 'admin_notices', 'nuttergood_farmley_ecommerce_admin_notice' );
}