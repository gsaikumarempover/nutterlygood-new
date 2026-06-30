<?php
/**
 * Checkout login — Farmley styled returning customer banner.
 *
 * @see woocommerce/templates/checkout/form-login.php
 * @package NutterlyGood
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

$registration_at_checkout   = WC_Checkout::instance()->is_registration_enabled();
$login_reminder_at_checkout = 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' );

if ( is_user_logged_in() ) {
	return;
}

if ( ! $login_reminder_at_checkout && ! $registration_at_checkout ) {
	return;
}

$show_form = isset( $_POST['login'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
$login_url = function_exists( 'nuttergood_farmley_get_account_url' )
	? nuttergood_farmley_get_account_url()
	: wp_login_url( wc_get_checkout_url() );
?>

<div class="ng-farmley-checkout-login" data-ng-login>
	<div class="ng-farmley-checkout-login__banner">
		<div class="ng-farmley-checkout-login__copy">
			<p class="ng-farmley-checkout-login__eyebrow"><?php esc_html_e( 'Returning customer', 'nuttergood' ); ?></p>
			<p class="ng-farmley-checkout-login__text">
				<?php esc_html_e( 'Sign in for faster checkout and saved addresses.', 'nuttergood' ); ?>
			</p>
		</div>
		<div class="ng-farmley-checkout-login__actions">
			<button type="button" class="ng-farmley-checkout-login__toggle showlogin" aria-expanded="<?php echo $show_form ? 'true' : 'false'; ?>" aria-controls="ng-farmley-checkout-login-form">
				<?php esc_html_e( 'Sign in', 'nuttergood' ); ?>
			</button>
			<a class="ng-farmley-checkout-login__signup" href="<?php echo esc_url( function_exists( 'nuttergood_farmley_get_signup_url' ) ? nuttergood_farmley_get_signup_url() : $login_url ); ?>">
				<?php esc_html_e( 'Create account', 'nuttergood' ); ?>
			</a>
		</div>
	</div>

	<?php if ( $registration_at_checkout || $login_reminder_at_checkout ) : ?>
		<div id="ng-farmley-checkout-login-form" class="ng-farmley-checkout-login__form-wrap<?php echo $show_form ? ' is-open' : ''; ?>" <?php echo $show_form ? '' : 'hidden'; ?>>
			<?php
			woocommerce_login_form(
				array(
					'message'  => esc_html__( 'Welcome back — sign in to continue checkout.', 'nuttergood' ),
					'redirect' => wc_get_checkout_url(),
					'hidden'   => false,
				)
			);
			?>
		</div>
	<?php endif; ?>
</div>
