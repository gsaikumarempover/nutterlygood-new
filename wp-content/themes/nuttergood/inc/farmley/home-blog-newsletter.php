<?php
/**
 * Homepage blog section helpers + GreenPath-style newsletter strip above footer.
 */

if ( ! function_exists( 'nuttergood_farmley_home_blog_widget_settings' ) ) {
	/**
	 * @return array<string, string>
	 */
	function nuttergood_farmley_home_blog_widget_settings() {
		return array(
			'posts_per_page'            => '3',
			'orderby'                   => 'date',
			'order'                     => 'DESC',
			'additional_params'         => '',
			'layout'                    => 'date-on-hover',
			'title_tag'                 => 'h5',
			'columns'                   => '3',
			'columns_responsive'        => 'custom',
			'columns_1512'              => '3',
			'columns_1368'              => '3',
			'columns_1200'              => '3',
			'columns_1024'              => '2',
			'columns_880'               => '2',
			'columns_680'               => '1',
			'pagination_type'           => 'no-pagination',
			'space'                     => 'custom',
			'space_custom'              => '25px',
			'space_custom_1512'         => '25px',
			'space_custom_1200'         => '25px',
			'space_custom_880'          => '25px',
			'vertical_space'            => 'custom',
			'vertical_space_custom'     => '60px',
			'vertical_space_custom_1512' => '60px',
			'vertical_space_custom_1200' => '54px',
			'vertical_space_custom_880'  => '54px',
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_is_home_blog_list_widget' ) ) {
	/**
	 * @param \Elementor\Widget_Base $widget Elementor widget instance.
	 */
	function nuttergood_farmley_is_home_blog_list_widget( $widget ) {
		if ( ! is_front_page() || ! is_object( $widget ) || ! method_exists( $widget, 'get_name' ) ) {
			return false;
		}

		return 'greenpath_core_blog_list' === $widget->get_name()
			&& method_exists( $widget, 'get_id' )
			&& '1ea2b9b' === $widget->get_id();
	}
}

if ( ! function_exists( 'nuttergood_farmley_filter_home_blog_list_widget' ) ) {
	/**
	 * @param \Elementor\Widget_Base $widget Elementor widget instance.
	 */
	function nuttergood_farmley_filter_home_blog_list_widget( $widget ) {
		if ( ! nuttergood_farmley_is_home_blog_list_widget( $widget ) ) {
			return;
		}

		$widget->set_settings(
			array_merge(
				$widget->get_settings_for_display(),
				nuttergood_farmley_home_blog_widget_settings()
			)
		);
	}
	add_action( 'elementor/frontend/widget/before_render', 'nuttergood_farmley_filter_home_blog_list_widget', 10, 1 );
}

if ( ! function_exists( 'nuttergood_farmley_render_home_newsletter' ) ) {
	/**
	 * GreenPath-style signup strip (content-bottom area) on the homepage only.
	 */
	function nuttergood_farmley_render_home_newsletter() {
		if ( ! is_front_page() ) {
			return;
		}

		$email = function_exists( 'nuttergood_farmley_contact_email' )
			? nuttergood_farmley_contact_email()
			: get_option( 'admin_email' );
		?>
		<div id="qodef-page-content-bottom" class="qodef-m ng-farmley-home-newsletter" role="contentinfo">
			<div class="qodef-m-inner">
				<div class="qodef-contact-form-7 ng-farmley-newsletter-wrap">
					<div class="qodef-newsletter">
						<div class="qodef-newsletter-content-wrapper">
							<h3 class="qodef-newsletter-content-text">
								<?php esc_html_e( 'Sign up and get', 'nuttergood' ); ?>
								<span class="qodef-highlight"><?php esc_html_e( '20% discount', 'nuttergood' ); ?></span>
								<?php esc_html_e( 'on your next purchase', 'nuttergood' ); ?>
							</h3>
						</div>
						<div class="qodef-newsletter-form-wrapper" data-ng-newsletter-wrap>
							<?php if ( isset( $_GET['newsletter'] ) && 'thanks' === sanitize_text_field( wp_unslash( $_GET['newsletter'] ) ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
								<div class="ng-farmley-newsletter-success" data-ng-newsletter-success role="status">
									<span class="ng-farmley-newsletter-success__icon" aria-hidden="true">✓</span>
									<p class="ng-farmley-newsletter-success__text">
										<?php esc_html_e( 'Thank you! We have shared your 20% off coupon and login details by email. Please check your inbox and spam folder.', 'nuttergood' ); ?>
									</p>
								</div>
							<?php else : ?>
								<form class="ng-farmley-newsletter-form" data-ng-newsletter-form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="post" novalidate>
									<?php wp_nonce_field( 'ng_farmley_newsletter', 'ng_farmley_newsletter_nonce' ); ?>
									<input type="hidden" name="ng_farmley_newsletter" value="1" />
									<span class="wpcf7-form-control-wrap" data-name="your-email">
										<input
											size="40"
											maxlength="400"
											class="wpcf7-form-control wpcf7-email wpcf7-validates-as-required wpcf7-text wpcf7-validates-as-email"
											aria-required="true"
											aria-invalid="false"
											placeholder="<?php esc_attr_e( 'Enter Your Email', 'nuttergood' ); ?>"
											type="email"
											name="your-email"
											required
										/>
									</span>
									<button class="wpcf7-form-control wpcf7-submit qodef-button qodef-size--normal qodef-layout--filled qodef-m" type="submit">
										<span class="qodef-m-text"><?php esc_html_e( 'Submit', 'nuttergood' ); ?></span>
									</button>
								</form>
								<p class="ng-farmley-newsletter-form__feedback" data-ng-newsletter-feedback role="status" aria-live="polite"></p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	add_action( 'greenpath_action_page_footer_template', 'nuttergood_farmley_render_home_newsletter', 4 );
}

if ( ! function_exists( 'nuttergood_farmley_process_newsletter_signup' ) ) {
	/**
	 * @return array{success:bool,message:string}
	 */
	function nuttergood_farmley_process_newsletter_signup( $email ) {
		$email = sanitize_email( $email );
		if ( ! is_email( $email ) ) {
			return array(
				'success' => false,
				'message' => __( 'Please enter a valid email address.', 'nuttergood' ),
			);
		}

		$setup = array();

		if ( function_exists( 'nuttergood_farmley_newsletter_setup_account_and_email' ) ) {
			$setup = nuttergood_farmley_newsletter_setup_account_and_email( $email );
			if ( ! $setup['success'] ) {
				return array(
					'success' => false,
					'message' => $setup['message'],
				);
			}

			if ( is_user_logged_in() && function_exists( 'nuttergood_farmley_try_apply_newsletter_coupon_for_email' ) ) {
				$user = wp_get_current_user();
				if ( $user && strtolower( $user->user_email ) === strtolower( $email ) ) {
					nuttergood_farmley_try_apply_newsletter_coupon_for_email( $email );
				}
			}
		} else {
			$signups   = get_option( 'ng_farmley_newsletter_signups', array() );
			$signups   = is_array( $signups ) ? $signups : array();
			$signups[] = array(
				'email' => $email,
				'time'  => current_time( 'mysql' ),
			);
			update_option( 'ng_farmley_newsletter_signups', array_slice( $signups, -500 ), false );
		}

		$notify_to = function_exists( 'nuttergood_farmley_email_address' )
			? nuttergood_farmley_email_address( 'offers' )
			: get_option( 'admin_email' );
		if ( $notify_to ) {
			if ( function_exists( 'nuttergood_farmley_send_mail' ) ) {
				nuttergood_farmley_send_mail(
					$notify_to,
					'Nutterly Good newsletter signup',
					"A new newsletter signup was received:\n\n{$email}\n",
					array(),
					'noreply'
				);
			} else {
				wp_mail(
					$notify_to,
					'Nutterly Good newsletter signup',
					"A new newsletter signup was received:\n\n{$email}\n"
				);
			}
		}

		$message = isset( $setup['message'] )
			? $setup['message']
			: ( function_exists( 'nuttergood_farmley_newsletter_success_message' )
				? nuttergood_farmley_newsletter_success_message( $email )
				: __( 'Thank you! We have shared your 20% off coupon and login details by email. Please check your inbox and spam folder.', 'nuttergood' ) );

		return array(
			'success'     => true,
			'message'     => $message,
			'coupon_code' => isset( $setup['coupon_code'] ) ? (string) $setup['coupon_code'] : '',
			'email_sent'  => isset( $setup['email_sent'] ) ? (bool) $setup['email_sent'] : true,
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_ajax_newsletter_signup' ) ) {
	function nuttergood_farmley_ajax_newsletter_signup() {
		check_ajax_referer( 'ng_farmley_newsletter', 'security' );

		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$result = nuttergood_farmley_process_newsletter_signup( $email );

		if ( ! $result['success'] ) {
			wp_send_json_error( array( 'message' => $result['message'] ) );
		}

		wp_send_json_success(
			array(
				'message'     => $result['message'],
				'coupon_code' => isset( $result['coupon_code'] ) ? $result['coupon_code'] : '',
				'email_sent'  => isset( $result['email_sent'] ) ? (bool) $result['email_sent'] : true,
			)
		);
	}
	add_action( 'wp_ajax_ng_farmley_newsletter', 'nuttergood_farmley_ajax_newsletter_signup' );
	add_action( 'wp_ajax_nopriv_ng_farmley_newsletter', 'nuttergood_farmley_ajax_newsletter_signup' );
}

if ( ! function_exists( 'nuttergood_farmley_handle_newsletter_signup' ) ) {
	/**
	 * Store newsletter signups in an option list (no CF7 dependency).
	 */
	function nuttergood_farmley_handle_newsletter_signup() {
		if ( empty( $_POST['ng_farmley_newsletter'] ) ) {
			return;
		}

		if ( ! isset( $_POST['ng_farmley_newsletter_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ng_farmley_newsletter_nonce'] ) ), 'ng_farmley_newsletter' ) ) {
			return;
		}

		$email  = isset( $_POST['your-email'] ) ? sanitize_email( wp_unslash( $_POST['your-email'] ) ) : '';
		$result = nuttergood_farmley_process_newsletter_signup( $email );
		if ( ! $result['success'] ) {
			wp_safe_redirect( add_query_arg( 'newsletter', 'error', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
			exit;
		}

		wp_safe_redirect( add_query_arg( 'newsletter', 'thanks', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
		exit;
	}
	add_action( 'template_redirect', 'nuttergood_farmley_handle_newsletter_signup' );
}

if ( ! function_exists( 'nuttergood_farmley_home_newsletter_assets' ) ) {
	function nuttergood_farmley_home_newsletter_assets() {
		if ( ! is_front_page() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-home-newsletter.css';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-home-newsletter',
				$uri . '/assets/css/farmley-home-newsletter.css',
				array( 'nuttergood-farmley-home', 'nuttergood-farmley-footer', 'greenpath-core-style' ),
				filemtime( $css )
			);
		}

		$js = $dir . '/assets/js/farmley-home-newsletter.js';
		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'nuttergood-farmley-home-newsletter',
				$uri . '/assets/js/farmley-home-newsletter.js',
				array( 'jquery' ),
				filemtime( $js ),
				true
			);

			wp_localize_script(
				'nuttergood-farmley-home-newsletter',
				'ngFarmleyNewsletter',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'ng_farmley_newsletter' ),
					'i18n'    => array(
						'submitting' => __( 'Submitting…', 'nuttergood' ),
						'submit'     => __( 'Submit', 'nuttergood' ),
						'failed'     => __( 'Something went wrong. Please try again.', 'nuttergood' ),
						'invalid'    => __( 'Please enter a valid email address.', 'nuttergood' ),
					),
				)
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_home_newsletter_assets', 41 );
}