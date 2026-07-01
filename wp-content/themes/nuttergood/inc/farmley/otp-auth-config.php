<?php
/**
 * OTP auth configuration — toggle test/live SMS here or via wp-config.php constants.
 *
 * Toggle live SMS Country:
 *   define( 'NG_OTP_MODE', 'live' );
 *   define( 'NG_SMSCOUNTRY_AUTH_KEY', 'your-auth-key' );
 *   define( 'NG_SMSCOUNTRY_AUTH_TOKEN', 'your-auth-token' );
 *   define( 'NG_SMSCOUNTRY_SENDER_ID', 'NUTGUD' );
 *
 * Test mode (default):
 *   define( 'NG_OTP_MODE', 'test' );
 *   define( 'NG_OTP_TEST_CODE', '123456' );
 */

if ( ! function_exists( 'nuttergood_farmley_otp_config' ) ) {
	/**
	 * @return array<string, mixed>
	 */
	function nuttergood_farmley_otp_config() {
		$config = array(
			'mode'               => 'test',
			'test_otp'           => '123456',
			'otp_length'         => 6,
			'otp_ttl'            => 600,
			'resend_cooldown'    => 45,
			'sms_country'        => array(
				'auth_key'   => '',
				'auth_token' => '',
				'sender_id'  => 'NUTGUD',
				'api_url'    => 'https://restapi.smscountry.com/v0.1/Accounts/%s/SMSes/',
			),
			'email_from_name'    => 'Nutterly Good',
			'placeholder_domain' => 'otp.nutterlygood.local',
		);

		if ( defined( 'NG_OTP_MODE' ) ) {
			$config['mode'] = NG_OTP_MODE;
		}
		if ( defined( 'NG_OTP_TEST_CODE' ) ) {
			$config['test_otp'] = NG_OTP_TEST_CODE;
		}
		if ( defined( 'NG_SMSCOUNTRY_AUTH_KEY' ) ) {
			$config['sms_country']['auth_key'] = NG_SMSCOUNTRY_AUTH_KEY;
		}
		if ( defined( 'NG_SMSCOUNTRY_AUTH_TOKEN' ) ) {
			$config['sms_country']['auth_token'] = NG_SMSCOUNTRY_AUTH_TOKEN;
		}
		if ( defined( 'NG_SMSCOUNTRY_SENDER_ID' ) ) {
			$config['sms_country']['sender_id'] = NG_SMSCOUNTRY_SENDER_ID;
		}

		return apply_filters( 'nuttergood_farmley_otp_config', $config );
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_is_test_mode' ) ) {
	function nuttergood_farmley_otp_is_test_mode() {
		$config = nuttergood_farmley_otp_config();
		return 'live' !== ( $config['mode'] ?? 'test' );
	}
}