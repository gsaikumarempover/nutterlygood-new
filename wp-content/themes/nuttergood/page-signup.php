<?php
/**
 * Signup landing — WooCommerce customer registration.
 *
 * Template Name: Signup
 */

get_header();

$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
?>
<main id="qodef-page-content" class="ng-farmley-signup" role="main">
	<?php if ( function_exists( 'wc_print_notices' ) ) : ?>
		<?php wc_print_notices(); ?>
	<?php endif; ?>

	<div class="ng-farmley-auth">
		<section class="ng-farmley-auth__intro" aria-labelledby="ng-farmley-signup-title">
			<span class="ng-farmley-auth__eyebrow"><?php esc_html_e( 'Nutterly Good', 'nuttergood' ); ?></span>
			<h1 id="ng-farmley-signup-title" class="ng-farmley-signup__title"><?php esc_html_e( 'Create your account', 'nuttergood' ); ?></h1>
			<p class="ng-farmley-signup__sub"><?php esc_html_e( 'Sign up to save wishlists, track orders, and checkout faster.', 'nuttergood' ); ?></p>
			<ul class="ng-farmley-auth__benefits">
				<li><?php esc_html_e( 'Track every order in one place', 'nuttergood' ); ?></li>
				<li><?php esc_html_e( 'Save favourites for faster shopping', 'nuttergood' ); ?></li>
				<li><?php esc_html_e( 'Get a smoother checkout experience', 'nuttergood' ); ?></li>
			</ul>
		</section>

		<section class="ng-farmley-auth__card" aria-label="<?php esc_attr_e( 'Account registration form', 'nuttergood' ); ?>">
			<?php
			if ( function_exists( 'woocommerce_output_all_notices' ) ) {
				woocommerce_output_all_notices();
			}

			if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) && function_exists( 'wc_get_template' ) ) {
				wc_get_template( 'myaccount/form-login.php' );
			} else {
				echo '<p>' . esc_html__( 'Registration is not enabled. Please contact the store admin.', 'nuttergood' ) . '</p>';
			}
			?>

			<p class="ng-farmley-signup__login-link">
				<?php
				printf(
					/* translators: %s: login URL */
					esc_html__( 'Already have an account? %s', 'nuttergood' ),
					'<a href="' . esc_url( $account_url ) . '">' . esc_html__( 'Log in', 'nuttergood' ) . '</a>'
				);
				?>
			</p>
		</section>
	</div>
</main>
<?php
get_footer();