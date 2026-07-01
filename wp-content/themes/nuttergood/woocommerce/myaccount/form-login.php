<?php
/**
 * OTP login + registration — single centered card with Login / Register tabs.
 *
 * @see woocommerce/templates/myaccount/form-login.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$default_tab = ( isset( $_GET['action'] ) && 'register' === sanitize_key( wp_unslash( $_GET['action'] ) ) ) ? 'register' : 'login';
?>
<div
	class="ng-farmley-auth ng-farmley-auth--account ng-farmley-auth--tabs"
	id="customer_login"
	data-default-tab="<?php echo esc_attr( $default_tab ); ?>"
>
	<div class="ng-farmley-auth__shell">
		<div class="ng-farmley-auth__header">
			<div class="ng-farmley-auth__mark" aria-hidden="true">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M12 3C8.5 3 6 5.5 6 9c0 4.5 6 12 6 12s6-7.5 6-12c0-3.5-2.5-6-6-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
					<circle cx="12" cy="9" r="2.2" stroke="currentColor" stroke-width="1.8"/>
				</svg>
			</div>
			<h2 class="ng-farmley-auth__title"><?php esc_html_e( 'My Account', 'nuttergood' ); ?></h2>
			<p class="ng-farmley-auth__subtitle"><?php esc_html_e( 'Sign in with email or mobile — we will send you a one-time code', 'nuttergood' ); ?></p>
		</div>

		<div class="ng-farmley-auth__body">
			<div class="ng-farmley-auth__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Account access', 'nuttergood' ); ?>">
				<button
					type="button"
					class="ng-farmley-auth__tab<?php echo 'login' === $default_tab ? ' is-active' : ''; ?>"
					role="tab"
					id="ng-farmley-auth-tab-login"
					aria-selected="<?php echo 'login' === $default_tab ? 'true' : 'false'; ?>"
					aria-controls="ng-farmley-auth-panel-login"
					data-tab="login"
				>
					<span class="ng-farmley-auth__tab-icon" aria-hidden="true">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</span>
					<?php esc_html_e( 'Login', 'nuttergood' ); ?>
				</button>
				<button
					type="button"
					class="ng-farmley-auth__tab<?php echo 'register' === $default_tab ? ' is-active' : ''; ?>"
					role="tab"
					id="ng-farmley-auth-tab-register"
					aria-selected="<?php echo 'register' === $default_tab ? 'true' : 'false'; ?>"
					aria-controls="ng-farmley-auth-panel-register"
					data-tab="register"
				>
					<span class="ng-farmley-auth__tab-icon" aria-hidden="true">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm11 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</span>
					<?php esc_html_e( 'Register', 'nuttergood' ); ?>
				</button>
			</div>

			<div class="ng-farmley-auth__panels">
				<div
					class="ng-farmley-auth__panel ng-farmley-auth__panel--login<?php echo 'login' === $default_tab ? ' is-active' : ''; ?>"
					id="ng-farmley-auth-panel-login"
					role="tabpanel"
					aria-labelledby="ng-farmley-auth-tab-login"
					data-panel="login"
					<?php echo 'login' !== $default_tab ? 'hidden' : ''; ?>
				>
					<?php
					if ( function_exists( 'nuttergood_farmley_otp_render_form' ) ) {
						nuttergood_farmley_otp_render_form( 'account', 'login' );
					}
					?>
				</div>

				<div
					class="ng-farmley-auth__panel ng-farmley-auth__panel--register<?php echo 'register' === $default_tab ? ' is-active' : ''; ?>"
					id="ng-farmley-auth-panel-register"
					role="tabpanel"
					aria-labelledby="ng-farmley-auth-tab-register"
					data-panel="register"
					<?php echo 'register' !== $default_tab ? 'hidden' : ''; ?>
				>
					<?php
					if ( function_exists( 'nuttergood_farmley_otp_render_form' ) ) {
						nuttergood_farmley_otp_render_form( 'account', 'register' );
					}
					?>
				</div>
			</div>

			<p class="ng-farmley-auth__secure">
				<span class="ng-farmley-auth__secure-icon" aria-hidden="true">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
				</span>
				<?php esc_html_e( 'Secure OTP verification', 'nuttergood' ); ?>
			</p>
		</div>
	</div>
</div>