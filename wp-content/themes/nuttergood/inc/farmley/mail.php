<?php
/**
 * Outbound mail — purpose-based From addresses + GoDaddy SMTP.
 */

if ( ! function_exists( 'nuttergood_farmley_mail_site_name' ) ) {
	function nuttergood_farmley_mail_site_name() {
		$info = function_exists( 'nuttergood_farmley_contact_info' ) ? nuttergood_farmley_contact_info() : array();

		return ! empty( $info['company'] ) ? (string) $info['company'] : get_bloginfo( 'name' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_mail_from' ) ) {
	/**
	 * @return array{email:string,name:string,purpose:string}
	 */
	function nuttergood_farmley_mail_from( $purpose = 'noreply' ) {
		$purpose = sanitize_key( (string) $purpose );
		$allowed = array( 'support', 'hello', 'offers', 'orders', 'noreply' );

		if ( ! in_array( $purpose, $allowed, true ) ) {
			$purpose = 'noreply';
		}

		$email = function_exists( 'nuttergood_farmley_email_address' )
			? nuttergood_farmley_email_address( $purpose )
			: sanitize_email( get_option( 'admin_email' ) );

		if ( ! is_email( $email ) ) {
			$email = sanitize_email( get_option( 'admin_email' ) );
		}

		return array(
			'email'   => $email,
			'name'    => nuttergood_farmley_mail_site_name(),
			'purpose' => $purpose,
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_mail_headers' ) ) {
	/**
	 * @param array<int, string> $extra_headers
	 * @return array<int, string>
	 */
	function nuttergood_farmley_mail_headers( $extra_headers = array(), $purpose = 'noreply' ) {
		$from = nuttergood_farmley_mail_from( $purpose );

		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			'From: ' . $from['name'] . ' <' . $from['email'] . '>',
		);

		if ( 'noreply' !== $from['purpose'] ) {
			$headers[] = 'Reply-To: ' . $from['name'] . ' <' . $from['email'] . '>';
		} else {
			$support = function_exists( 'nuttergood_farmley_email_address' )
				? nuttergood_farmley_email_address( 'support' )
				: $from['email'];
			$headers[] = 'Reply-To: ' . $from['name'] . ' <' . $support . '>';
		}

		return array_merge( $headers, is_array( $extra_headers ) ? $extra_headers : array() );
	}
}

if ( ! function_exists( 'nuttergood_farmley_mail_get_active_purpose' ) ) {
	function nuttergood_farmley_mail_get_active_purpose() {
		$purpose = 'noreply';

		if ( isset( $GLOBALS['ng_farmley_mail_purpose'] ) ) {
			$purpose = sanitize_key( (string) $GLOBALS['ng_farmley_mail_purpose'] );
		}

		return $purpose;
	}
}

if ( ! function_exists( 'nuttergood_farmley_mail_set_active_purpose' ) ) {
	function nuttergood_farmley_mail_set_active_purpose( $purpose ) {
		$GLOBALS['ng_farmley_mail_purpose'] = sanitize_key( (string) $purpose );
	}
}

if ( ! function_exists( 'nuttergood_farmley_phpmailer_configure' ) ) {
	function nuttergood_farmley_phpmailer_configure( $phpmailer ) {
		$from   = nuttergood_farmley_mail_from( nuttergood_farmley_mail_get_active_purpose() );
		$config = function_exists( 'nuttergood_farmley_smtp_config' ) ? nuttergood_farmley_smtp_config() : array();

		try {
			$phpmailer->setFrom( $from['email'], $from['name'], false );
		} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Host may reject overriding From; headers still apply.
		}

		if ( empty( $config['enabled'] ) || empty( $config['password'] ) || empty( $config['host'] ) ) {
			return;
		}

		$phpmailer->isSMTP();
		$phpmailer->Host       = (string) $config['host'];
		$phpmailer->Port       = (int) ( $config['port'] ?? 465 );
		$phpmailer->SMTPAuth   = ! empty( $config['auth'] );
		$phpmailer->Username   = (string) ( $config['user'] ?? $from['email'] );
		$phpmailer->Password   = (string) $config['password'];
		$phpmailer->SMTPSecure = (string) ( $config['secure'] ?? 'ssl' );
		$phpmailer->SMTPAutoTLS = 'tls' === $config['secure'];
	}
}

if ( ! function_exists( 'nuttergood_farmley_send_mail' ) ) {
	/**
	 * @param array<int, string> $extra_headers
	 * @param string             $purpose       support|hello|offers|orders|noreply
	 */
	function nuttergood_farmley_send_mail( $to, $subject, $body, $extra_headers = array(), $purpose = 'noreply' ) {
		$to = sanitize_email( $to );
		if ( ! is_email( $to ) ) {
			return false;
		}

		nuttergood_farmley_mail_set_active_purpose( $purpose );
		add_action( 'phpmailer_init', 'nuttergood_farmley_phpmailer_configure', 5, 1 );

		$sent = wp_mail(
			$to,
			$subject,
			$body,
			nuttergood_farmley_mail_headers( $extra_headers, $purpose )
		);

		remove_action( 'phpmailer_init', 'nuttergood_farmley_phpmailer_configure', 5 );
		unset( $GLOBALS['ng_farmley_mail_purpose'] );

		return $sent;
	}
}