<?php
/**
 * Hybrid checkout accounts — guest checkout + login reminder + post-order account creation.
 *
 * @package NutterlyGood
 */

if ( ! function_exists( 'nuttergood_farmley_configure_hybrid_checkout_accounts' ) ) {
	/**
	 * Guest checkout stays on; login reminder + delayed account creation enabled.
	 */
	function nuttergood_farmley_configure_hybrid_checkout_accounts() {
		update_option( 'woocommerce_enable_guest_checkout', 'yes' );
		update_option( 'woocommerce_enable_checkout_login_reminder', 'yes' );
		update_option( 'woocommerce_enable_delayed_account_creation', 'yes' );
		update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'no' );
		update_option( 'woocommerce_registration_generate_username', 'yes' );
		update_option( 'woocommerce_registration_generate_password', 'yes' );
	}

	add_action( 'after_setup_theme', 'nuttergood_farmley_configure_hybrid_checkout_accounts', 25 );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_login_message' ) ) {
	function nuttergood_farmley_checkout_login_message( $message ) {
		return __( 'Already have an account?', 'nuttergood' );
	}
	add_filter( 'woocommerce_checkout_login_message', 'nuttergood_farmley_checkout_login_message' );
}

if ( ! function_exists( 'nuttergood_farmley_get_thankyou_order' ) ) {
	/**
	 * Resolve order on the order-received endpoint.
	 *
	 * @return WC_Order|false
	 */
	function nuttergood_farmley_get_thankyou_order() {
		if ( ! function_exists( 'is_order_received_page' ) || ! is_order_received_page() ) {
			return false;
		}

		global $wp;
		$order_id = isset( $wp->query_vars['order-received'] ) ? absint( $wp->query_vars['order-received'] ) : 0;

		if ( ! $order_id ) {
			return false;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return false;
		}

		$order_key = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $order_key && ! hash_equals( $order->get_order_key(), $order_key ) ) {
			return false;
		}

		return $order;
	}
}

if ( ! function_exists( 'nuttergood_farmley_can_offer_post_order_account' ) ) {
	/**
	 * @param WC_Order $order Order object.
	 */
	function nuttergood_farmley_can_offer_post_order_account( $order ) {
		if ( ! $order instanceof WC_Order || is_user_logged_in() ) {
			return false;
		}

		if ( $order->get_customer_id() ) {
			return false;
		}

		$email = $order->get_billing_email();
		if ( ! $email || email_exists( $email ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'nuttergood_farmley_process_post_order_account' ) ) {
	function nuttergood_farmley_process_post_order_account() {
		if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
			return;
		}

		if ( empty( $_POST['ng_create_account'] ) || empty( $_POST['ng_order_id'] ) ) {
			return;
		}

		if ( empty( $_POST['ng_create_account_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['ng_create_account_nonce'] ) ), 'ng_create_account' ) ) {
			wc_add_notice( __( 'Unable to create your account. Please try again.', 'nuttergood' ), 'error' );
			return;
		}

		$order = wc_get_order( absint( wp_unslash( $_POST['ng_order_id'] ) ) );
		if ( ! $order ) {
			return;
		}

		$order_key = isset( $_POST['ng_order_key'] ) ? wc_clean( wp_unslash( $_POST['ng_order_key'] ) ) : '';
		if ( ! $order_key || ! hash_equals( $order->get_order_key(), $order_key ) ) {
			wc_add_notice( __( 'This order link is invalid.', 'nuttergood' ), 'error' );
			return;
		}

		if ( ! nuttergood_farmley_can_offer_post_order_account( $order ) ) {
			return;
		}

		$email = sanitize_email( $order->get_billing_email() );
		$customer_id = wc_create_new_customer(
			$email,
			'',
			'',
			array(
				'first_name' => $order->get_billing_first_name(),
				'last_name'  => $order->get_billing_last_name(),
				'source'     => 'nuttergood-delayed-account',
			)
		);

		if ( is_wp_error( $customer_id ) ) {
			wc_add_notice( $customer_id->get_error_message(), 'error' );
			return;
		}

		$order->set_customer_id( $customer_id );
		$order->save();

		if ( class_exists( '\Automattic\WooCommerce\StoreApi\Utilities\OrderController' ) ) {
			$order_controller = new \Automattic\WooCommerce\StoreApi\Utilities\OrderController();
			$order_controller->sync_customer_data_with_order( $order );
		}

		wc_set_customer_auth_cookie( $customer_id );

		wp_safe_redirect(
			add_query_arg(
				'ng_account_created',
				'1',
				$order->get_checkout_order_received_url()
			)
		);
		exit;
	}
	add_action( 'template_redirect', 'nuttergood_farmley_process_post_order_account', 6 );
}

if ( ! function_exists( 'nuttergood_farmley_thankyou_create_account_card' ) ) {
	function nuttergood_farmley_thankyou_create_account_card( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order || $order->has_status( 'failed' ) ) {
			return;
		}

		if ( is_user_logged_in() && (int) $order->get_customer_id() === get_current_user_id() ) {
			?>
			<div class="ng-farmley-create-account ng-farmley-create-account--success">
				<div class="ng-farmley-create-account__icon ng-farmley-create-account__icon--success" aria-hidden="true"></div>
				<h2 class="ng-farmley-create-account__title"><?php esc_html_e( 'You are signed in', 'nuttergood' ); ?></h2>
				<p class="ng-farmley-create-account__text">
					<?php esc_html_e( 'Track this order anytime from your account.', 'nuttergood' ); ?>
				</p>
				<a class="ng-farmley-create-account__btn" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ) ); ?>">
					<?php esc_html_e( 'View my orders', 'nuttergood' ); ?>
				</a>
			</div>
			<?php
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['ng_account_created'] ) && is_user_logged_in() ) {
			?>
			<div class="ng-farmley-create-account ng-farmley-create-account--success is-visible">
				<div class="ng-farmley-create-account__icon ng-farmley-create-account__icon--success" aria-hidden="true"></div>
				<h2 class="ng-farmley-create-account__title"><?php esc_html_e( 'Account created!', 'nuttergood' ); ?></h2>
				<p class="ng-farmley-create-account__text">
					<?php esc_html_e( 'Your order is now linked to your account. We sent login details to your email.', 'nuttergood' ); ?>
				</p>
				<a class="ng-farmley-create-account__btn" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ) ); ?>">
					<?php esc_html_e( 'Go to my orders', 'nuttergood' ); ?>
				</a>
			</div>
			<?php
			return;
		}

		if ( ! nuttergood_farmley_can_offer_post_order_account( $order ) ) {
			return;
		}

		$email = $order->get_billing_email();
		?>
		<div class="ng-farmley-create-account">
			<div class="ng-farmley-create-account__icon" aria-hidden="true"></div>
			<h2 class="ng-farmley-create-account__title"><?php esc_html_e( 'Save your details for next time', 'nuttergood' ); ?></h2>
			<p class="ng-farmley-create-account__text">
				<?php
				printf(
					/* translators: %s: customer email */
					esc_html__( 'Create an account with %s to track this order, reorder faster, and manage your addresses.', 'nuttergood' ),
					'<strong>' . esc_html( $email ) . '</strong>'
				);
				?>
			</p>
			<ul class="ng-farmley-create-account__benefits">
				<li><?php esc_html_e( 'Track orders & delivery updates', 'nuttergood' ); ?></li>
				<li><?php esc_html_e( 'Faster checkout next time', 'nuttergood' ); ?></li>
				<li><?php esc_html_e( 'Wishlist & saved addresses', 'nuttergood' ); ?></li>
			</ul>
			<form class="ng-farmley-create-account__form" method="post">
				<input type="hidden" name="ng_create_account" value="1" />
				<input type="hidden" name="ng_order_id" value="<?php echo esc_attr( (string) $order->get_id() ); ?>" />
				<input type="hidden" name="ng_order_key" value="<?php echo esc_attr( $order->get_order_key() ); ?>" />
				<?php wp_nonce_field( 'ng_create_account', 'ng_create_account_nonce' ); ?>
				<button type="submit" class="ng-farmley-create-account__btn">
					<?php esc_html_e( 'Create account — one click', 'nuttergood' ); ?>
				</button>
				<p class="ng-farmley-create-account__fineprint">
					<?php esc_html_e( 'We will email you a password to sign in. No extra steps needed.', 'nuttergood' ); ?>
				</p>
			</form>
		</div>
		<?php
	}
	add_action( 'woocommerce_thankyou', 'nuttergood_farmley_thankyou_create_account_card', 8 );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_accounts_assets' ) ) {
	function nuttergood_farmley_checkout_accounts_assets() {
		$load = ( function_exists( 'nuttergood_farmley_is_checkout_flow' ) && nuttergood_farmley_is_checkout_flow() )
			|| ( function_exists( 'is_order_received_page' ) && is_order_received_page() );

		if ( ! $load ) {
			return;
		}

		$js = get_template_directory() . '/assets/js/farmley-checkout-accounts.js';
		if ( ! file_exists( $js ) ) {
			return;
		}

		wp_enqueue_script(
			'nuttergood-farmley-checkout-accounts',
			get_template_directory_uri() . '/assets/js/farmley-checkout-accounts.js',
			array( 'jquery', 'woocommerce' ),
			filemtime( $js ),
			true
		);

		if ( function_exists( 'nuttergood_farmley_enqueue_button_loader' ) ) {
			nuttergood_farmley_enqueue_button_loader();
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_checkout_accounts_assets', 37 );
}

if ( ! function_exists( 'nuttergood_farmley_account_button_loader' ) ) {
	function nuttergood_farmley_account_button_loader() {
		$is_account = function_exists( 'is_account_page' ) && is_account_page();
		if ( ! is_page( 'signup' ) && ! $is_account ) {
			return;
		}

		if ( function_exists( 'nuttergood_farmley_enqueue_button_loader' ) ) {
			nuttergood_farmley_enqueue_button_loader();
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_account_button_loader', 38 );
}
