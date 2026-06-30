<?php
/**
 * Razorpay local development helpers.
 *
 * Skips auto-webhook registration on localhost (Razorpay servers cannot reach it).
 *
 * @package NutterlyGood
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'nuttergood_is_local_dev_site' ) ) {
	/**
	 * Whether the site URL resolves to a private/reserved IP (localhost, LAN, etc.).
	 */
	function nuttergood_is_local_dev_site() {
		$host = parse_url( home_url(), PHP_URL_HOST );

		if ( ! $host ) {
			return false;
		}

		if ( in_array( $host, array( 'localhost', '127.0.0.1' ), true ) ) {
			return true;
		}

		$ip = gethostbyname( $host );

		return ! filter_var(
			$ip,
			FILTER_VALIDATE_IP,
			FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
		);
	}
}

if ( nuttergood_is_local_dev_site() ) {
	add_action(
		'woocommerce_init',
		static function () {
			if ( ! function_exists( 'WC' ) || ! WC()->payment_gateways() ) {
				return;
			}

			$gateways = WC()->payment_gateways()->payment_gateways();

			if ( empty( $gateways['razorpay'] ) || ! is_object( $gateways['razorpay'] ) ) {
				return;
			}

			remove_action(
				'woocommerce_update_options_payment_gateways_razorpay',
				array( $gateways['razorpay'], 'autoEnableWebhook' )
			);
		},
		20
	);

	add_action(
		'admin_notices',
		static function () {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( empty( $_GET['page'] ) || 'wc-settings' !== $_GET['page'] ) {
				return;
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( empty( $_GET['tab'] ) || 'checkout' !== $_GET['tab'] ) {
				return;
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( empty( $_GET['section'] ) || 'razorpay' !== $_GET['section'] ) {
				return;
			}

			$settings = get_option( 'woocommerce_razorpay_settings', array() );

			if ( empty( $settings['key_id'] ) || empty( $settings['key_secret'] ) ) {
				return;
			}

			echo '<div class="notice notice-info is-dismissible"><p>';
			echo esc_html__(
				'Local development: Razorpay webhooks cannot be registered on localhost, but your test keys are saved. Checkout payments still work in test mode. Use a public tunnel (e.g. ngrok) only if you need to test webhooks.',
				'nuttergood'
			);
			echo '</p></div>';
		}
	);
}
