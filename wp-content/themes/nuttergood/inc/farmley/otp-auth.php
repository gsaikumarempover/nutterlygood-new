<?php
/**
 * OTP login + registration (mobile or email). SMS Country ready; test mode by default.
 */

$config_file = get_template_directory() . '/inc/farmley/otp-auth-config.php';
if ( file_exists( $config_file ) ) {
	require_once $config_file;
}

if ( ! function_exists( 'nuttergood_farmley_otp_normalize_phone' ) ) {
	function nuttergood_farmley_otp_normalize_phone( $phone ) {
		$digits = preg_replace( '/\D+/', '', (string) $phone );
		if ( '' === $digits ) {
			return '';
		}
		if ( 10 === strlen( $digits ) ) {
			$digits = '91' . $digits;
		}
		return $digits;
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_parse_identifier' ) ) {
	/**
	 * @return array{type: string, value: string, display: string}|null
	 */
	function nuttergood_farmley_otp_parse_identifier( $raw ) {
		$raw = trim( (string) $raw );
		if ( '' === $raw ) {
			return null;
		}

		if ( is_email( $raw ) ) {
			return array(
				'type'    => 'email',
				'value'   => sanitize_email( $raw ),
				'display' => sanitize_email( $raw ),
			);
		}

		$phone = nuttergood_farmley_otp_normalize_phone( $raw );
		if ( strlen( $phone ) >= 12 && strlen( $phone ) <= 15 ) {
			return array(
				'type'    => 'phone',
				'value'   => $phone,
				'display' => '+' . $phone,
			);
		}

		return null;
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_generate_code' ) ) {
	function nuttergood_farmley_otp_generate_code() {
		$config = nuttergood_farmley_otp_config();
		if ( nuttergood_farmley_otp_is_test_mode() ) {
			return (string) $config['test_otp'];
		}

		$length = max( 4, min( 8, (int) $config['otp_length'] ) );
		$max    = (int) pow( 10, $length ) - 1;
		$min    = (int) pow( 10, $length - 1 );
		return (string) wp_rand( $min, $max );
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_transient_key' ) ) {
	function nuttergood_farmley_otp_transient_key( $purpose, $identifier ) {
		return 'ng_otp_' . $purpose . '_' . md5( strtolower( (string) $identifier ) );
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_store' ) ) {
	function nuttergood_farmley_otp_store( $purpose, $identifier, $payload ) {
		$config = nuttergood_farmley_otp_config();
		set_transient(
			nuttergood_farmley_otp_transient_key( $purpose, $identifier ),
			$payload,
			(int) $config['otp_ttl']
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_get_store' ) ) {
	function nuttergood_farmley_otp_get_store( $purpose, $identifier ) {
		$data = get_transient( nuttergood_farmley_otp_transient_key( $purpose, $identifier ) );
		return is_array( $data ) ? $data : null;
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_delete_store' ) ) {
	function nuttergood_farmley_otp_delete_store( $purpose, $identifier ) {
		delete_transient( nuttergood_farmley_otp_transient_key( $purpose, $identifier ) );
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_send_sms_country' ) ) {
	function nuttergood_farmley_otp_send_sms_country( $phone, $message ) {
		$config = nuttergood_farmley_otp_config();
		$sms    = $config['sms_country'];
		$key    = $sms['auth_key'] ?? '';
		$token  = $sms['auth_token'] ?? '';

		if ( '' === $key || '' === $token ) {
			return new WP_Error( 'sms_config', __( 'SMS Country credentials are not configured.', 'nuttergood' ) );
		}

		$url  = sprintf( $sms['api_url'], rawurlencode( $key ) );
		$body = wp_json_encode(
			array(
				'Text'     => $message,
				'Number'   => $phone,
				'SenderId' => $sms['sender_id'] ?? 'NUTGUD',
			)
		);

		$response = wp_remote_post(
			$url,
			array(
				'timeout' => 15,
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Basic ' . base64_encode( $key . ':' . $token ),
				),
				'body'    => $body,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			return new WP_Error( 'sms_failed', __( 'SMS could not be sent. Please try again.', 'nuttergood' ) );
		}

		return true;
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_deliver_code' ) ) {
	function nuttergood_farmley_otp_deliver_code( $identifier, $otp ) {
		$config  = nuttergood_farmley_otp_config();
		$message = sprintf(
			/* translators: 1: site name, 2: OTP code */
			__( '%1$s verification code: %2$s. Valid for 10 minutes.', 'nuttergood' ),
			get_bloginfo( 'name' ),
			$otp
		);

		if ( 'email' === $identifier['type'] ) {
			wp_mail(
				$identifier['value'],
				sprintf( __( '%s — your verification code', 'nuttergood' ), get_bloginfo( 'name' ) ),
				$message,
				array( 'Content-Type: text/plain; charset=UTF-8' )
			);
			return true;
		}

		if ( nuttergood_farmley_otp_is_test_mode() ) {
			return true;
		}

		return nuttergood_farmley_otp_send_sms_country( $identifier['value'], $message );
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_find_user_by_identifier' ) ) {
	function nuttergood_farmley_otp_find_user_by_identifier( $identifier ) {
		if ( 'email' === $identifier['type'] ) {
			return get_user_by( 'email', $identifier['value'] );
		}

		$users = get_users(
			array(
				'meta_query' => array(
					'relation' => 'OR',
					array(
						'key'   => 'ng_auth_phone',
						'value' => $identifier['value'],
					),
					array(
						'key'   => 'billing_phone',
						'value' => $identifier['value'],
					),
				),
				'number'     => 1,
				'fields'     => 'all',
			)
		);

		return ! empty( $users[0] ) ? $users[0] : false;
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_json' ) ) {
	function nuttergood_farmley_otp_json( $success, $message, $extra = array() ) {
		wp_send_json(
			array_merge(
				array(
					'success' => (bool) $success,
					'message' => (string) $message,
				),
				$extra
			)
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_ajax_send' ) ) {
	function nuttergood_farmley_otp_ajax_send() {
		check_ajax_referer( 'ng_farmley_otp', 'nonce' );

		$purpose = isset( $_POST['purpose'] ) ? sanitize_key( wp_unslash( $_POST['purpose'] ) ) : 'login';
		if ( ! in_array( $purpose, array( 'login', 'register' ), true ) ) {
			nuttergood_farmley_otp_json( false, __( 'Invalid request.', 'nuttergood' ) );
		}

		$identifier = nuttergood_farmley_otp_parse_identifier( isset( $_POST['identifier'] ) ? wp_unslash( $_POST['identifier'] ) : '' );
		if ( ! $identifier ) {
			nuttergood_farmley_otp_json( false, __( 'Enter a valid mobile number or email address.', 'nuttergood' ) );
		}

		$user = nuttergood_farmley_otp_find_user_by_identifier( $identifier );
		if ( 'login' === $purpose && ! $user ) {
			nuttergood_farmley_otp_json( false, __( 'No account found with this mobile or email. Please register first.', 'nuttergood' ) );
		}
		if ( 'register' === $purpose && $user ) {
			nuttergood_farmley_otp_json( false, __( 'An account already exists. Please log in with OTP.', 'nuttergood' ) );
		}

		$existing = nuttergood_farmley_otp_get_store( $purpose, $identifier['value'] );
		$config   = nuttergood_farmley_otp_config();
		if ( $existing && ! empty( $existing['sent_at'] ) && ( time() - (int) $existing['sent_at'] ) < (int) $config['resend_cooldown'] ) {
			nuttergood_farmley_otp_json( false, __( 'Please wait a moment before requesting another OTP.', 'nuttergood' ) );
		}

		$otp      = nuttergood_farmley_otp_generate_code();
		$delivery = nuttergood_farmley_otp_deliver_code( $identifier, $otp );
		if ( is_wp_error( $delivery ) ) {
			nuttergood_farmley_otp_json( false, $delivery->get_error_message() );
		}

		$payload = array(
			'otp'        => $otp,
			'sent_at'    => time(),
			'type'       => $identifier['type'],
			'identifier' => $identifier['value'],
		);

		if ( 'register' === $purpose ) {
			$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
			$payload['name'] = $name;
			$email_field = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
			$phone_field = isset( $_POST['phone'] ) ? nuttergood_farmley_otp_normalize_phone( wp_unslash( $_POST['phone'] ) ) : '';
			if ( $email_field && is_email( $email_field ) ) {
				$payload['email'] = $email_field;
			}
			if ( $phone_field ) {
				$payload['phone'] = $phone_field;
			}
		}

		nuttergood_farmley_otp_store( $purpose, $identifier['value'], $payload );

		$response = array(
			'channel' => $identifier['type'],
			'test_mode' => nuttergood_farmley_otp_is_test_mode(),
		);
		if ( nuttergood_farmley_otp_is_test_mode() ) {
			$response['test_hint'] = sprintf(
				/* translators: %s: test OTP code */
				__( 'Test mode: use OTP %s', 'nuttergood' ),
				$config['test_otp']
			);
		}

		nuttergood_farmley_otp_json( true, __( 'OTP sent successfully.', 'nuttergood' ), $response );
	}
	add_action( 'wp_ajax_nopriv_ng_farmley_otp_send', 'nuttergood_farmley_otp_ajax_send' );
	add_action( 'wp_ajax_ng_farmley_otp_send', 'nuttergood_farmley_otp_ajax_send' );
}

if ( ! function_exists( 'nuttergood_farmley_otp_login_user' ) ) {
	function nuttergood_farmley_otp_login_user( $user, $remember = false ) {
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID, $remember );
		do_action( 'wp_login', $user->user_login, $user );
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_create_user' ) ) {
	function nuttergood_farmley_otp_create_user( $payload, $identifier ) {
		$config = nuttergood_farmley_otp_config();
		$name   = ! empty( $payload['name'] ) ? $payload['name'] : '';
		$email  = '';
		$phone  = '';

		if ( ! empty( $payload['email'] ) && is_email( $payload['email'] ) ) {
			$email = $payload['email'];
		} elseif ( 'email' === $identifier['type'] ) {
			$email = $identifier['value'];
		}

		if ( ! empty( $payload['phone'] ) ) {
			$phone = $payload['phone'];
		} elseif ( 'phone' === $identifier['type'] ) {
			$phone = $identifier['value'];
		}

		if ( ! $email && $phone ) {
			$email = $phone . '@' . $config['placeholder_domain'];
		}

		if ( ! $email ) {
			return new WP_Error( 'missing_email', __( 'Email or mobile is required to create an account.', 'nuttergood' ) );
		}

		if ( email_exists( $email ) ) {
			return new WP_Error( 'exists', __( 'An account already exists with this email.', 'nuttergood' ) );
		}

		$username = sanitize_user( current( explode( '@', $email ) ), true );
		if ( ! $username || username_exists( $username ) ) {
			$username = 'ng_' . wp_generate_password( 8, false, false );
		}

		$password = wp_generate_password( 20, true, true );
		$user_id  = wp_create_user( $username, $password, $email );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		if ( $name ) {
			wp_update_user(
				array(
					'ID'           => $user_id,
					'display_name' => $name,
					'first_name'   => $name,
				)
			);
		}

		if ( $phone ) {
			update_user_meta( $user_id, 'ng_auth_phone', $phone );
			update_user_meta( $user_id, 'billing_phone', '+' . $phone );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			$customer = new WC_Customer( $user_id );
			if ( $phone ) {
				$customer->set_billing_phone( '+' . $phone );
			}
			if ( $email ) {
				$customer->set_billing_email( $email );
			}
			$customer->save();
		}

		return get_user_by( 'id', $user_id );
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_ajax_verify' ) ) {
	function nuttergood_farmley_otp_ajax_verify() {
		check_ajax_referer( 'ng_farmley_otp', 'nonce' );

		$purpose = isset( $_POST['purpose'] ) ? sanitize_key( wp_unslash( $_POST['purpose'] ) ) : 'login';
		$otp_in  = isset( $_POST['otp'] ) ? sanitize_text_field( wp_unslash( $_POST['otp'] ) ) : '';
		$remember = ! empty( $_POST['remember'] );

		$identifier = nuttergood_farmley_otp_parse_identifier( isset( $_POST['identifier'] ) ? wp_unslash( $_POST['identifier'] ) : '' );
		if ( ! $identifier || '' === $otp_in ) {
			nuttergood_farmley_otp_json( false, __( 'Enter your mobile/email and OTP.', 'nuttergood' ) );
		}

		$stored = nuttergood_farmley_otp_get_store( $purpose, $identifier['value'] );
		if ( ! $stored || empty( $stored['otp'] ) || (string) $stored['otp'] !== (string) $otp_in ) {
			nuttergood_farmley_otp_json( false, __( 'Invalid or expired OTP. Please request a new code.', 'nuttergood' ) );
		}

		if ( 'login' === $purpose ) {
			$user = nuttergood_farmley_otp_find_user_by_identifier( $identifier );
			if ( ! $user ) {
				nuttergood_farmley_otp_json( false, __( 'Account not found.', 'nuttergood' ) );
			}
			nuttergood_farmley_otp_delete_store( $purpose, $identifier['value'] );
			nuttergood_farmley_otp_login_user( $user, $remember );
			$redirect = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
			nuttergood_farmley_otp_json( true, __( 'Login successful.', 'nuttergood' ), array( 'redirect' => $redirect ) );
		}

		$user = nuttergood_farmley_otp_create_user( $stored, $identifier );
		if ( is_wp_error( $user ) ) {
			nuttergood_farmley_otp_json( false, $user->get_error_message() );
		}

		nuttergood_farmley_otp_delete_store( $purpose, $identifier['value'] );
		nuttergood_farmley_otp_login_user( $user, true );
		$redirect = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
		nuttergood_farmley_otp_json( true, __( 'Account created successfully.', 'nuttergood' ), array( 'redirect' => $redirect ) );
	}
	add_action( 'wp_ajax_nopriv_ng_farmley_otp_verify', 'nuttergood_farmley_otp_ajax_verify' );
	add_action( 'wp_ajax_ng_farmley_otp_verify', 'nuttergood_farmley_otp_ajax_verify' );
}

if ( ! function_exists( 'nuttergood_farmley_otp_field_icon_svg' ) ) {
	function nuttergood_farmley_otp_field_icon_svg( $type ) {
		switch ( $type ) {
			case 'otp':
				return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 11V7a5 5 0 0 1 10 0v4M5 11h14v10H5z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
			case 'contact':
				return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
			default:
				return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
		}
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_render_field' ) ) {
	function nuttergood_farmley_otp_render_field( $context, $form_id, $field_suffix, $label, $input_html, $icon = 'user' ) {
		$field_id = $form_id . '-' . $field_suffix;
		$is_account = 'account' === $context;
		?>
		<div class="ng-farmley-otp-form__field">
			<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $label ); ?></label>
			<?php if ( $is_account ) : ?>
				<div class="ng-farmley-otp-form__control">
					<span class="ng-farmley-otp-form__icon"><?php echo nuttergood_farmley_otp_field_icon_svg( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php echo $input_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php else : ?>
				<?php echo $input_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_render_form' ) ) {
	function nuttergood_farmley_otp_render_form( $context = 'page', $purpose = 'login' ) {
		$is_register = 'register' === $purpose;
		if ( 'modal' === $context ) {
			$form_id = $is_register ? 'qodef-membership-register-modal-part' : 'qodef-membership-login-modal-part';
		} else {
			$form_id = 'ng-farmley-otp-' . $purpose . '-' . $context;
		}
		$config      = nuttergood_farmley_otp_config();
		?>
		<form
			id="<?php echo esc_attr( $form_id ); ?>"
			class="ng-farmley-otp-form ng-farmley-otp-form--<?php echo esc_attr( $purpose ); ?> ng-farmley-otp-form--<?php echo esc_attr( $context ); ?>"
			method="post"
			novalidate
			data-purpose="<?php echo esc_attr( $purpose ); ?>"
		>
			<?php if ( nuttergood_farmley_otp_is_test_mode() && 'modal' === $context ) : ?>
				<div class="ng-farmley-otp-form__test-banner" role="status">
					<?php
					printf(
						/* translators: %s: test OTP */
						esc_html__( 'Test OTP mode is ON. Use code %s for any mobile or email.', 'nuttergood' ),
						esc_html( $config['test_otp'] )
					);
					?>
				</div>
			<?php endif; ?>

			<?php
			if ( $is_register ) {
				ob_start();
				?>
				<input type="text" id="<?php echo esc_attr( $form_id ); ?>-name" name="name" autocomplete="name" placeholder="<?php esc_attr_e( 'Your name', 'nuttergood' ); ?>" />
				<?php
				nuttergood_farmley_otp_render_field(
					$context,
					$form_id,
					'name',
					__( 'Full name', 'nuttergood' ),
					ob_get_clean(),
					'user'
				);
			}

			if ( 'account' === $context ) :
				?>
				<div class="ng-farmley-otp-form__method" role="tablist" aria-label="<?php esc_attr_e( 'Login method', 'nuttergood' ); ?>">
					<button type="button" class="ng-farmley-otp-form__method-btn is-active" data-login-method="email" role="tab" aria-selected="true">
						<?php esc_html_e( 'Email', 'nuttergood' ); ?>
					</button>
					<button type="button" class="ng-farmley-otp-form__method-btn" data-login-method="phone" role="tab" aria-selected="false">
						<?php esc_html_e( 'Mobile', 'nuttergood' ); ?>
					</button>
				</div>
				<?php
			endif;
			$identifier_label = 'account' === $context
				? __( 'Email address', 'nuttergood' )
				: __( 'Mobile number or email', 'nuttergood' );
			$identifier_placeholder = 'account' === $context
				? __( 'Enter your email', 'nuttergood' )
				: __( 'Mobile or email', 'nuttergood' );
			$identifier_type = 'account' === $context ? 'email' : 'text';

			ob_start();
			?>
			<input
				type="<?php echo esc_attr( $identifier_type ); ?>"
				id="<?php echo esc_attr( $form_id ); ?>-identifier"
				name="identifier"
				class="ng-farmley-otp-form__identifier"
				data-login-method="<?php echo 'account' === $context ? 'email' : 'any'; ?>"
				required
				autocomplete="username"
				placeholder="<?php echo esc_attr( $identifier_placeholder ); ?>"
			/>
			<?php
			nuttergood_farmley_otp_render_field(
				$context,
				$form_id,
				'identifier',
				$identifier_label,
				ob_get_clean(),
				'account' === $context ? 'email' : 'contact'
			);
			?>

			<div class="ng-farmley-otp-form__otp-row is-hidden">
				<?php
				ob_start();
				?>
				<input
					type="text"
					id="<?php echo esc_attr( $form_id ); ?>-otp"
					name="otp"
					class="ng-farmley-otp-form__otp"
					inputmode="numeric"
					pattern="[0-9]*"
					maxlength="8"
					placeholder="<?php esc_attr_e( '6-digit code', 'nuttergood' ); ?>"
				/>
				<?php
				nuttergood_farmley_otp_render_field(
					$context,
					$form_id,
					'otp',
					__( 'Enter OTP', 'nuttergood' ),
					ob_get_clean(),
					'otp'
				);
				?>
			</div>

			<?php if ( ! $is_register ) : ?>
				<label class="ng-farmley-otp-form__remember">
					<input type="checkbox" name="remember" value="1" checked />
					<span><?php esc_html_e( 'Remember me', 'nuttergood' ); ?></span>
				</label>
			<?php endif; ?>

			<div class="ng-farmley-otp-form__actions">
				<button type="button" class="ng-farmley-otp-form__send button">
					<?php esc_html_e( 'Send OTP', 'nuttergood' ); ?>
				</button>
				<button type="submit" class="ng-farmley-otp-form__verify button alt is-hidden">
					<?php echo $is_register ? esc_html__( 'Verify & Register', 'nuttergood' ) : esc_html__( 'Verify & Login', 'nuttergood' ); ?>
				</button>
			</div>

			<p class="ng-farmley-otp-form__message" aria-live="polite"></p>
		</form>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_override_membership_forms' ) ) {
	function nuttergood_farmley_otp_override_membership_forms() {
		remove_action( 'greenpath_membership_action_login_modal_content', 'greenpath_membership_include_login_template', 10 );
		remove_action( 'greenpath_membership_action_login_modal_content', 'greenpath_membership_include_register_template', 15 );

		add_action(
			'greenpath_membership_action_login_modal_content',
			function () {
				nuttergood_farmley_otp_render_form( 'modal', 'login' );
			},
			10
		);
		add_action(
			'greenpath_membership_action_login_modal_content',
			function () {
				nuttergood_farmley_otp_render_form( 'modal', 'register' );
			},
			15
		);
	}
	add_action( 'init', 'nuttergood_farmley_otp_override_membership_forms', 40 );
}

if ( ! function_exists( 'nuttergood_farmley_otp_disable_password_login' ) ) {
	function nuttergood_farmley_otp_disable_password_login() {
		if ( is_admin() ) {
			return;
		}
		?>
		<style>
			#qodef-membership-login-modal-part .qodef-m-user-password,
			#qodef-membership-register-modal-part .qodef-m-user-password,
			#qodef-membership-register-modal-part .qodef-m-user-confirm-password,
			#qodef-membership-register-modal-part .qodef-m-user-name:not(.ng-farmley-otp-form input) {
				display: none !important;
			}
		</style>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_otp_assets' ) ) {
	function nuttergood_farmley_otp_assets() {
		if ( is_admin() ) {
			return;
		}

		$should_load = ( function_exists( 'is_account_page' ) && is_account_page() )
			|| is_page( 'signup' )
			|| ! is_user_logged_in();

		if ( ! $should_load ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-otp-auth.css';
		$js  = $dir . '/assets/js/farmley-otp-auth.js';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-otp-auth',
				$uri . '/assets/css/farmley-otp-auth.css',
				array( 'nuttergood-farmley-account', 'greenpath-style' ),
				filemtime( $css )
			);
		}

		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'nuttergood-farmley-otp-auth',
				$uri . '/assets/js/farmley-otp-auth.js',
				array( 'jquery' ),
				filemtime( $js ),
				true
			);
			wp_localize_script(
				'nuttergood-farmley-otp-auth',
				'ngFarmleyOtp',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'ng_farmley_otp' ),
					'i18n'    => array(
						'sendOtp'   => __( 'Send OTP', 'nuttergood' ),
						'resendOtp' => __( 'Resend OTP', 'nuttergood' ),
						'resendIn'  => __( 'Resend in %ss', 'nuttergood' ),
						'sending'   => __( 'Sending OTP...', 'nuttergood' ),
						'verifying' => __( 'Verifying...', 'nuttergood' ),
						'sent'      => __( 'OTP sent. Check your phone or email.', 'nuttergood' ),
						'error'     => __( 'Something went wrong. Please try again.', 'nuttergood' ),
					),
					'resendCooldown' => (int) nuttergood_farmley_otp_config()['resend_cooldown'],
				)
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_otp_assets', 45 );
}