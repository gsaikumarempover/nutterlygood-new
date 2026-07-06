<?php
/**
 * GoDaddy mailbox mapping for Nutterly Good.
 *
 * Mailboxes:
 *   support@ — customer contact (public + contact form inbox)
 *   hello@   — general / welcome
 *   offers@  — newsletter, coupons, promotions
 *   orders@  — WooCommerce order emails
 *   noreply@ — OTP + automated transactional (SMTP auth default)
 */

if ( ! function_exists( 'nuttergood_farmley_email_addresses' ) ) {
	/**
	 * @return array<string, string>
	 */
	function nuttergood_farmley_email_addresses() {
		$addresses = array(
			'support' => 'support@nutterlygood.com',
			'hello'   => 'hello@nutterlygood.com',
			'offers'  => 'offers@nutterlygood.com',
			'orders'  => 'orders@nutterlygood.com',
			'noreply' => 'noreply@nutterlygood.com',
		);

		return apply_filters( 'nuttergood_farmley_email_addresses', $addresses );
	}
}

if ( ! function_exists( 'nuttergood_farmley_email_address' ) ) {
	function nuttergood_farmley_email_address( $purpose = 'support' ) {
		$addresses = nuttergood_farmley_email_addresses();
		$purpose   = sanitize_key( (string) $purpose );

		if ( isset( $addresses[ $purpose ] ) ) {
			return sanitize_email( $addresses[ $purpose ] );
		}

		return sanitize_email( $addresses['support'] ?? get_option( 'admin_email' ) );
	}
}

if ( ! function_exists( 'nuttergood_farmley_smtp_config' ) ) {
	/**
	 * GoDaddy Workspace Email SMTP defaults.
	 * Add NG_SMTP_PASSWORD in wp-config.php (see block near the bottom of that file).
	 *
	 * @return array<string, mixed>
	 */
	function nuttergood_farmley_smtp_config() {
		$config = array(
			'enabled'  => true,
			'host'     => 'smtpout.secureserver.net',
			'port'     => 465,
			'secure'   => 'ssl',
			'auth'     => true,
			'user'     => nuttergood_farmley_email_address( 'noreply' ),
			'password' => '',
		);

		if ( defined( 'NG_SMTP_ENABLED' ) ) {
			$config['enabled'] = (bool) NG_SMTP_ENABLED;
		}
		if ( defined( 'NG_SMTP_HOST' ) ) {
			$config['host'] = NG_SMTP_HOST;
		}
		if ( defined( 'NG_SMTP_PORT' ) ) {
			$config['port'] = (int) NG_SMTP_PORT;
		}
		if ( defined( 'NG_SMTP_SECURE' ) ) {
			$config['secure'] = NG_SMTP_SECURE;
		}
		if ( defined( 'NG_SMTP_USER' ) ) {
			$config['user'] = NG_SMTP_USER;
		}
		if ( defined( 'NG_SMTP_PASSWORD' ) ) {
			$config['password'] = NG_SMTP_PASSWORD;
		}

		return apply_filters( 'nuttergood_farmley_smtp_config', $config );
	}
}

if ( ! function_exists( 'nuttergood_farmley_configure_woocommerce_emails' ) ) {
	function nuttergood_farmley_configure_woocommerce_emails() {
		if ( get_option( 'ng_farmley_email_config_v1' ) ) {
			return;
		}

		update_option( 'woocommerce_email_from_address', nuttergood_farmley_email_address( 'orders' ) );
		update_option( 'woocommerce_email_from_name', 'Nutterly Good' );
		update_option( 'ng_farmley_email_config_v1', 1, false );
	}
	add_action( 'after_setup_theme', 'nuttergood_farmley_configure_woocommerce_emails', 18 );
}

if ( ! function_exists( 'nuttergood_farmley_woocommerce_email_from_address' ) ) {
	function nuttergood_farmley_woocommerce_email_from_address( $from_email ) {
		return nuttergood_farmley_email_address( 'orders' );
	}
	add_filter( 'woocommerce_email_from_address', 'nuttergood_farmley_woocommerce_email_from_address', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_woocommerce_email_from_name' ) ) {
	function nuttergood_farmley_woocommerce_email_from_name( $from_name ) {
		return 'Nutterly Good';
	}
	add_filter( 'woocommerce_email_from_name', 'nuttergood_farmley_woocommerce_email_from_name', 20 );
}