<?php
/**
 * Checkout field defaults — India-only addresses, no placeholder OTP emails.
 */

if ( ! function_exists( 'nuttergood_farmley_is_placeholder_auth_email' ) ) {
	function nuttergood_farmley_is_placeholder_auth_email( $email ) {
		$email = strtolower( sanitize_email( (string) $email ) );
		if ( ! is_email( $email ) ) {
			return false;
		}

		$config = function_exists( 'nuttergood_farmley_otp_config' ) ? nuttergood_farmley_otp_config() : array();
		$domain = strtolower( (string) ( $config['placeholder_domain'] ?? 'otp.nutterlygood.local' ) );

		$suffix = '@' . $domain;

		return strlen( $email ) > strlen( $suffix ) && substr( $email, -strlen( $suffix ) ) === $suffix;
	}
}

if ( ! function_exists( 'nuttergood_farmley_configure_india_only_store' ) ) {
	function nuttergood_farmley_configure_india_only_store() {
		if ( get_option( 'ng_farmley_india_only_store' ) ) {
			return;
		}

		update_option( 'woocommerce_default_country', 'IN' );
		update_option( 'woocommerce_allowed_countries', 'specific' );
		update_option( 'woocommerce_specific_allowed_countries', array( 'IN' ) );
		update_option( 'woocommerce_ship_to_countries', 'specific' );
		update_option( 'woocommerce_specific_ship_to_countries', array( 'IN' ) );
		update_option( 'ng_farmley_india_only_store', 1, false );
	}
	add_action( 'after_setup_theme', 'nuttergood_farmley_configure_india_only_store', 22 );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_default_country' ) ) {
	function nuttergood_farmley_checkout_default_country( $country ) {
		return 'IN';
	}
	add_filter( 'default_checkout_billing_country', 'nuttergood_farmley_checkout_default_country', 20 );
	add_filter( 'default_checkout_shipping_country', 'nuttergood_farmley_checkout_default_country', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_country_fields' ) ) {
	function nuttergood_farmley_checkout_country_fields( $fields ) {
		foreach ( array( 'billing', 'shipping' ) as $section ) {
			if ( empty( $fields[ $section ] ) || ! is_array( $fields[ $section ] ) ) {
				continue;
			}

			if ( isset( $fields[ $section ][ $section . '_country' ] ) ) {
				$fields[ $section ][ $section . '_country' ]['type']    = 'hidden';
				$fields[ $section ][ $section . '_country' ]['default'] = 'IN';
				$fields[ $section ][ $section . '_country' ]['required'] = false;
			}
		}

		return $fields;
	}
	add_filter( 'woocommerce_checkout_fields', 'nuttergood_farmley_checkout_country_fields', 30 );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_clear_placeholder_email' ) ) {
	function nuttergood_farmley_checkout_clear_placeholder_email( $value, $input ) {
		if ( 'billing_email' !== $input || ! nuttergood_farmley_is_placeholder_auth_email( $value ) ) {
			return $value;
		}

		return '';
	}
	add_filter( 'woocommerce_checkout_get_value', 'nuttergood_farmley_checkout_clear_placeholder_email', 20, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_billing_email_label' ) ) {
	function nuttergood_farmley_checkout_billing_email_label( $fields ) {
		if ( isset( $fields['billing']['billing_email'] ) ) {
			$fields['billing']['billing_email']['placeholder'] = __( 'Email for order updates', 'nuttergood' );
		}

		return $fields;
	}
	add_filter( 'woocommerce_checkout_fields', 'nuttergood_farmley_checkout_billing_email_label', 35 );
}