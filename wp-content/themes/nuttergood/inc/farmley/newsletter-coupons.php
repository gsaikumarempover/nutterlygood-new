<?php
/**
 * Homepage newsletter signup → WooCommerce 20% coupon + auto-apply on login.
 *
 * @package NutterlyGood
 */

if ( ! function_exists( 'nuttergood_farmley_newsletter_coupon_percent' ) ) {
	function nuttergood_farmley_newsletter_coupon_percent() {
		return (float) apply_filters( 'nuttergood_farmley_newsletter_coupon_percent', 20 );
	}
}

if ( ! function_exists( 'nuttergood_farmley_newsletter_coupon_code_for_email' ) ) {
	function nuttergood_farmley_newsletter_coupon_code_for_email( $email ) {
		$email = strtolower( sanitize_email( $email ) );
		return 'NG20' . strtoupper( substr( md5( $email ), 0, 6 ) );
	}
}

if ( ! function_exists( 'nuttergood_farmley_newsletter_get_signups' ) ) {
	function nuttergood_farmley_newsletter_get_signups() {
		$signups = get_option( 'ng_farmley_newsletter_signups', array() );
		return is_array( $signups ) ? $signups : array();
	}
}

if ( ! function_exists( 'nuttergood_farmley_newsletter_find_signup' ) ) {
	/**
	 * @return array<string, mixed>|null
	 */
	function nuttergood_farmley_newsletter_find_signup( $email ) {
		$email = strtolower( sanitize_email( $email ) );
		if ( ! is_email( $email ) ) {
			return null;
		}

		foreach ( nuttergood_farmley_newsletter_get_signups() as $signup ) {
			if ( ! empty( $signup['email'] ) && strtolower( $signup['email'] ) === $email ) {
				return $signup;
			}
		}

		return null;
	}
}

if ( ! function_exists( 'nuttergood_farmley_newsletter_save_signup' ) ) {
	function nuttergood_farmley_newsletter_save_signup( $email, $coupon_code ) {
		$email   = sanitize_email( $email );
		$signups = nuttergood_farmley_newsletter_get_signups();
		$found   = false;

		foreach ( $signups as $index => $signup ) {
			if ( ! empty( $signup['email'] ) && strtolower( $signup['email'] ) === strtolower( $email ) ) {
				$signups[ $index ]['coupon_code'] = $coupon_code;
				$signups[ $index ]['time']        = current_time( 'mysql' );
				$found                            = true;
				break;
			}
		}

		if ( ! $found ) {
			$signups[] = array(
				'email'       => $email,
				'coupon_code' => $coupon_code,
				'time'        => current_time( 'mysql' ),
			);
		}

		update_option( 'ng_farmley_newsletter_signups', array_slice( $signups, -500 ), false );
	}
}

if ( ! function_exists( 'nuttergood_farmley_newsletter_create_wc_coupon' ) ) {
	/**
	 * Create or return existing newsletter coupon for an email.
	 */
	function nuttergood_farmley_newsletter_create_wc_coupon( $email ) {
		if ( ! function_exists( 'WC' ) || ! class_exists( 'WC_Coupon' ) ) {
			return '';
		}

		$email = sanitize_email( $email );
		if ( ! is_email( $email ) ) {
			return '';
		}

		$code   = nuttergood_farmley_newsletter_coupon_code_for_email( $email );
		$coupon = new WC_Coupon( $code );

		if ( $coupon->get_id() ) {
			return $code;
		}

		$coupon = new WC_Coupon();
		$coupon->set_code( $code );
		$coupon->set_description( __( 'Newsletter signup — 20% off next purchase', 'nuttergood' ) );
		$coupon->set_discount_type( 'percent' );
		$coupon->set_amount( nuttergood_farmley_newsletter_coupon_percent() );
		$coupon->set_individual_use( true );
		$coupon->set_usage_limit( 1 );
		$coupon->set_usage_limit_per_user( 1 );
		$coupon->set_email_restrictions( array( $email ) );
		$coupon->save();

		return $code;
	}
}

if ( ! function_exists( 'nuttergood_farmley_newsletter_issue_coupon' ) ) {
	function nuttergood_farmley_newsletter_issue_coupon( $email ) {
		$code = nuttergood_farmley_newsletter_create_wc_coupon( $email );
		if ( $code ) {
			nuttergood_farmley_newsletter_save_signup( $email, $code );
		}
		return $code;
	}
}

if ( ! function_exists( 'nuttergood_farmley_newsletter_suppress_wc_account_email' ) ) {
	function nuttergood_farmley_newsletter_suppress_wc_account_email( $suppress = null ) {
		static $active = false;

		if ( null !== $suppress ) {
			$active = (bool) $suppress;
		}

		return $active;
	}

	function nuttergood_farmley_newsletter_filter_wc_new_account_email( $enabled ) {
		if ( nuttergood_farmley_newsletter_suppress_wc_account_email() ) {
			return false;
		}

		return $enabled;
	}
	add_filter( 'woocommerce_email_enabled_customer_new_account', 'nuttergood_farmley_newsletter_filter_wc_new_account_email' );
}

if ( ! function_exists( 'nuttergood_farmley_newsletter_success_message' ) ) {
	function nuttergood_farmley_newsletter_success_message( $email ) {
		return sprintf(
			/* translators: %s: customer email address */
			__( 'Thank you! We have shared your 20%% off coupon and login details with %s. Please check your inbox and spam folder.', 'nuttergood' ),
			$email
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_newsletter_ensure_customer' ) ) {
	/**
	 * Create a WooCommerce customer for newsletter signups when one does not exist.
	 *
	 * @return array{created:bool,user_id:int,password:string,error:string}
	 */
	function nuttergood_farmley_newsletter_ensure_customer( $email ) {
		$email = sanitize_email( $email );
		$result = array(
			'created'  => false,
			'user_id'  => 0,
			'password' => '',
			'error'    => '',
		);

		if ( ! is_email( $email ) ) {
			$result['error'] = __( 'Invalid email address.', 'nuttergood' );
			return $result;
		}

		$existing = email_exists( $email );
		if ( $existing ) {
			$result['user_id'] = (int) $existing;
			return $result;
		}

		if ( ! function_exists( 'wc_create_new_customer' ) ) {
			$result['error'] = __( 'WooCommerce is not available to create an account.', 'nuttergood' );
			return $result;
		}

		$password = wp_generate_password( 12, false );

		nuttergood_farmley_newsletter_suppress_wc_account_email( true );
		$customer_id = wc_create_new_customer(
			$email,
			'',
			$password,
			array(
				'source' => 'nuttergood-newsletter',
			)
		);
		nuttergood_farmley_newsletter_suppress_wc_account_email( false );

		if ( is_wp_error( $customer_id ) ) {
			$result['error'] = $customer_id->get_error_message();
			return $result;
		}

		$result['created']  = true;
		$result['user_id']  = (int) $customer_id;
		$result['password'] = $password;

		return $result;
	}
}

if ( ! function_exists( 'nuttergood_farmley_newsletter_send_welcome_email' ) ) {
	/**
	 * Email coupon + login instructions (password for new accounts, OTP for everyone).
	 *
	 * @return bool
	 */
	function nuttergood_farmley_newsletter_send_welcome_email( $email, $coupon_code, $account_created, $password = '' ) {
		$email = sanitize_email( $email );
		if ( ! is_email( $email ) || ! $coupon_code ) {
			return false;
		}

		$site_name   = get_bloginfo( 'name' );
		$login_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );
		$percent     = nuttergood_farmley_newsletter_coupon_percent();
		$lines       = array();
		$lines[]     = sprintf( __( 'Hi there,', 'nuttergood' ) );
		$lines[]     = '';
		$lines[]     = sprintf(
			/* translators: %s: site name */
			__( 'Thank you for signing up with %s!', 'nuttergood' ),
			$site_name
		);
		$lines[]     = '';
		$lines[]     = sprintf(
			/* translators: 1: percent discount, 2: coupon code */
			__( 'Your exclusive %1$s%% off coupon code: %2$s', 'nuttergood' ),
			(int) $percent,
			$coupon_code
		);
		$lines[]     = __( 'Use it on your next purchase when you are signed in with this email.', 'nuttergood' );
		$lines[]     = '';
		$lines[]     = __( 'How to sign in:', 'nuttergood' );
		$lines[]     = sprintf( __( 'My Account: %s', 'nuttergood' ), $login_url );
		$lines[]     = sprintf( __( 'Email: %s', 'nuttergood' ), $email );
		$lines[]     = __( 'Quick sign-in: enter your email, tap Send OTP, and use the code we email you.', 'nuttergood' );

		if ( $account_created && $password ) {
			$lines[] = '';
			$lines[] = sprintf(
				/* translators: %s: account password */
				__( 'Your account password: %s', 'nuttergood' ),
				$password
			);
			$lines[] = __( 'You can also use this password to sign in from the My Account page.', 'nuttergood' );
		} else {
			$lines[] = '';
			$lines[] = __( 'You already have an account with us — use OTP or your existing password to sign in.', 'nuttergood' );
		}

		$lines[] = '';
		$lines[] = __( 'Happy shopping!', 'nuttergood' );
		$lines[] = $site_name;

		$subject = sprintf(
			/* translators: %s: site name */
			__( '%s — your newsletter coupon & login details', 'nuttergood' ),
			$site_name
		);

		return wp_mail(
			$email,
			$subject,
			implode( "\n", $lines ),
			array( 'Content-Type: text/plain; charset=UTF-8' )
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_newsletter_setup_account_and_email' ) ) {
	/**
	 * Issue coupon, ensure customer account, and email login + coupon details.
	 *
	 * @return array{success:bool,message:string,coupon_code:string,email_sent:bool}
	 */
	function nuttergood_farmley_newsletter_setup_account_and_email( $email ) {
		$email = sanitize_email( $email );
		if ( ! is_email( $email ) ) {
			return array(
				'success'     => false,
				'message'     => __( 'Please enter a valid email address.', 'nuttergood' ),
				'coupon_code' => '',
				'email_sent'  => false,
			);
		}

		$coupon_code = nuttergood_farmley_newsletter_issue_coupon( $email );
		$account     = nuttergood_farmley_newsletter_ensure_customer( $email );
		$email_sent  = false;

		if ( $coupon_code ) {
			$email_sent = nuttergood_farmley_newsletter_send_welcome_email(
				$email,
				$coupon_code,
				! empty( $account['created'] ),
				! empty( $account['password'] ) ? (string) $account['password'] : ''
			);
		}

		if ( ! empty( $account['error'] ) && empty( $account['user_id'] ) ) {
			return array(
				'success'     => false,
				'message'     => $account['error'],
				'coupon_code' => $coupon_code,
				'email_sent'  => $email_sent,
			);
		}

		return array(
			'success'     => true,
			'message'     => nuttergood_farmley_newsletter_success_message( $email ),
			'coupon_code' => $coupon_code,
			'email_sent'  => $email_sent,
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_newsletter_coupon_for_email' ) ) {
	function nuttergood_farmley_newsletter_coupon_for_email( $email ) {
		$signup = nuttergood_farmley_newsletter_find_signup( $email );
		if ( $signup && ! empty( $signup['coupon_code'] ) ) {
			return (string) $signup['coupon_code'];
		}

		if ( $signup ) {
			return nuttergood_farmley_newsletter_issue_coupon( $email );
		}

		return '';
	}
}

if ( ! function_exists( 'nuttergood_farmley_try_apply_newsletter_coupon_for_email' ) ) {
	function nuttergood_farmley_try_apply_newsletter_coupon_for_email( $email ) {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return false;
		}

		$code = nuttergood_farmley_newsletter_coupon_for_email( $email );
		if ( ! $code ) {
			return false;
		}

		if ( nuttergood_farmley_side_cart_coupon_is_applied( $code ) ) {
			return true;
		}

		$coupon = new WC_Coupon( $code );
		if ( ! $coupon->get_id() ) {
			return false;
		}

		$discounts = new WC_Discounts( WC()->cart );
		$valid     = $discounts->is_coupon_valid( $coupon );
		if ( is_wp_error( $valid ) ) {
			return false;
		}

		$result = WC()->cart->apply_coupon( $code );
		if ( is_wp_error( $result ) ) {
			return false;
		}

		WC()->cart->calculate_totals();
		return true;
	}
}

if ( ! function_exists( 'nuttergood_farmley_auto_apply_newsletter_coupon_on_login' ) ) {
	function nuttergood_farmley_auto_apply_newsletter_coupon_on_login( $user_login, $user ) {
		unset( $user_login );
		if ( ! $user instanceof WP_User || empty( $user->user_email ) ) {
			return;
		}
		nuttergood_farmley_try_apply_newsletter_coupon_for_email( $user->user_email );
	}
	add_action( 'wp_login', 'nuttergood_farmley_auto_apply_newsletter_coupon_on_login', 20, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_auto_apply_newsletter_coupon_on_cart' ) ) {
	function nuttergood_farmley_auto_apply_newsletter_coupon_on_cart() {
		if ( ! is_user_logged_in() || ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return;
		}
		nuttergood_farmley_try_apply_newsletter_coupon_for_email( wp_get_current_user()->user_email );
	}
	add_action( 'woocommerce_cart_loaded_from_session', 'nuttergood_farmley_auto_apply_newsletter_coupon_on_cart', 25 );
}