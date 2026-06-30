<?php
/**
 * Checkout page — Farmley styling and layout helpers.
 */

if ( ! function_exists( 'nuttergood_farmley_is_order_pay_page' ) ) {
	function nuttergood_farmley_is_order_pay_page() {
		return function_exists( 'is_checkout' )
			&& is_checkout()
			&& function_exists( 'is_wc_endpoint_url' )
			&& is_wc_endpoint_url( 'order-pay' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_is_checkout_flow' ) ) {
	function nuttergood_farmley_is_checkout_flow() {
		return function_exists( 'is_checkout' )
			&& is_checkout()
			&& ! is_order_received_page()
			&& ! nuttergood_farmley_is_order_pay_page();
	}
}

if ( ! function_exists( 'nuttergood_farmley_checkout_body_class' ) ) {
	function nuttergood_farmley_checkout_body_class( $classes ) {
		if ( nuttergood_farmley_is_checkout_flow() ) {
			$classes[] = 'ng-farmley-checkout-page';
		}

		if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
			$classes[] = 'ng-farmley-order-received-page';
		}

		if ( nuttergood_farmley_is_order_pay_page() ) {
			$classes[] = 'ng-farmley-order-pay-page';
		}

		return $classes;
	}
	add_filter( 'body_class', 'nuttergood_farmley_checkout_body_class' );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_page_inner_classes' ) ) {
	function nuttergood_farmley_checkout_page_inner_classes( $classes ) {
		if (
			nuttergood_farmley_is_checkout_flow()
			|| nuttergood_farmley_is_order_pay_page()
			|| ( function_exists( 'is_order_received_page' ) && is_order_received_page() )
		) {
			return 'qodef-content-full-width';
		}

		return $classes;
	}
	add_filter( 'greenpath_filter_page_inner_classes', 'nuttergood_farmley_checkout_page_inner_classes' );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_intro' ) ) {
	function nuttergood_farmley_checkout_intro() {
		if ( ! nuttergood_farmley_is_checkout_flow() ) {
			return;
		}
		?>
		<div class="ng-farmley-checkout">
			<header class="ng-farmley-checkout__hero">
				<p class="ng-farmley-checkout__eyebrow"><?php esc_html_e( 'Secure checkout', 'nuttergood' ); ?></p>
				<h1 class="ng-farmley-checkout__title"><?php esc_html_e( 'Complete your order', 'nuttergood' ); ?></h1>
				<p class="ng-farmley-checkout__lead">
					<?php esc_html_e( 'Enter your details below. We will pack your order with care and keep you updated until delivery.', 'nuttergood' ); ?>
				</p>
				<ul class="ng-farmley-checkout__trust" aria-label="<?php esc_attr_e( 'Checkout benefits', 'nuttergood' ); ?>">
					<li><?php esc_html_e( 'Secure payments', 'nuttergood' ); ?></li>
					<li><?php esc_html_e( 'Freshly packed', 'nuttergood' ); ?></li>
					<li><?php esc_html_e( 'Pan-India delivery', 'nuttergood' ); ?></li>
				</ul>
			</header>
		<?php
	}
	add_action( 'woocommerce_before_checkout_form', 'nuttergood_farmley_checkout_intro', 8 );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_intro_close' ) ) {
	function nuttergood_farmley_checkout_intro_close() {
		if ( ! nuttergood_farmley_is_checkout_flow() ) {
			return;
		}
		echo '</div>';
	}
	add_action( 'woocommerce_after_checkout_form', 'nuttergood_farmley_checkout_intro_close', 8 );
}

if ( ! function_exists( 'nuttergood_farmley_enqueue_button_loader' ) ) {
	function nuttergood_farmley_enqueue_button_loader() {
		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$js  = $dir . '/assets/js/farmley-button-loader.js';

		if ( ! file_exists( $js ) ) {
			return;
		}

		wp_enqueue_script(
			'nuttergood-farmley-button-loader',
			$uri . '/assets/js/farmley-button-loader.js',
			array( 'jquery' ),
			filemtime( $js ),
			true
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_checkout_assets' ) ) {
	function nuttergood_farmley_checkout_assets() {
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-checkout.css';

		if ( ! file_exists( $css ) ) {
			return;
		}

		wp_enqueue_style(
			'nuttergood-farmley-checkout',
			$uri . '/assets/css/farmley-checkout.css',
			array( 'greenpath-style' ),
			filemtime( $css )
		);

		nuttergood_farmley_enqueue_button_loader();
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_checkout_assets', 36 );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_section_titles' ) ) {
	function nuttergood_farmley_checkout_section_titles() {
		if ( ! nuttergood_farmley_is_checkout_flow() ) {
			return;
		}
		?>
		<style>
			body.ng-farmley-checkout-page .entry-title,
			body.ng-farmley-checkout-page .qodef-page-title,
			body.ng-farmley-checkout-page #qodef-page-content > .qodef-m-title {
				display: none !important;
			}
		</style>
		<?php
	}
	add_action( 'wp_head', 'nuttergood_farmley_checkout_section_titles', 99 );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_order_summary_open' ) ) {
	function nuttergood_farmley_checkout_order_summary_open() {
		if ( ! nuttergood_farmley_is_checkout_flow() ) {
			return;
		}
		echo '<div class="ng-farmley-checkout__summary">';
	}
	add_action( 'woocommerce_checkout_before_order_review_heading', 'nuttergood_farmley_checkout_order_summary_open', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_checkout_order_summary_close' ) ) {
	function nuttergood_farmley_checkout_order_summary_close() {
		if ( ! nuttergood_farmley_is_checkout_flow() ) {
			return;
		}
		echo '</div>';
	}
	add_action( 'woocommerce_checkout_after_order_review', 'nuttergood_farmley_checkout_order_summary_close', 15 );
}

if ( ! function_exists( 'nuttergood_farmley_order_pay_intro' ) ) {
	function nuttergood_farmley_order_pay_intro() {
		if ( ! nuttergood_farmley_is_order_pay_page() ) {
			return;
		}
		?>
		<div class="ng-farmley-order-pay">
			<header class="ng-farmley-checkout__hero">
				<p class="ng-farmley-checkout__eyebrow"><?php esc_html_e( 'Secure payment', 'nuttergood' ); ?></p>
				<h1 class="ng-farmley-checkout__title"><?php esc_html_e( 'Complete your payment', 'nuttergood' ); ?></h1>
				<p class="ng-farmley-checkout__lead">
					<?php esc_html_e( 'Review your order below and pay securely with Razorpay. UPI, cards, and net banking are supported.', 'nuttergood' ); ?>
				</p>
			</header>
		<?php
	}
	add_action( 'before_woocommerce_pay', 'nuttergood_farmley_order_pay_intro', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_order_pay_intro_close' ) ) {
	function nuttergood_farmley_order_pay_intro_close() {
		if ( ! nuttergood_farmley_is_order_pay_page() ) {
			return;
		}
		echo '</div>';
	}
	add_action( 'after_woocommerce_pay', 'nuttergood_farmley_order_pay_intro_close', 99 );
}

if ( ! function_exists( 'nuttergood_farmley_order_pay_section_titles' ) ) {
	function nuttergood_farmley_order_pay_section_titles() {
		if ( ! nuttergood_farmley_is_order_pay_page() ) {
			return;
		}
		?>
		<style>
			body.ng-farmley-order-pay-page .entry-title,
			body.ng-farmley-order-pay-page .qodef-page-title,
			body.ng-farmley-order-pay-page #qodef-page-content > .qodef-m-title {
				display: none !important;
			}
		</style>
		<?php
	}
	add_action( 'wp_head', 'nuttergood_farmley_order_pay_section_titles', 99 );
}

if ( ! function_exists( 'nuttergood_farmley_order_pay_assets' ) ) {
	function nuttergood_farmley_order_pay_assets() {
		if ( ! nuttergood_farmley_is_order_pay_page() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-checkout.css';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-checkout',
				$uri . '/assets/css/farmley-checkout.css',
				array( 'greenpath-style' ),
				filemtime( $css )
			);
		}

		nuttergood_farmley_enqueue_button_loader();
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_order_pay_assets', 40 );
}

if ( ! function_exists( 'nuttergood_farmley_order_pay_script' ) ) {
	/**
	 * Enqueue after Razorpay registers its checkout script during receipt render.
	 */
	function nuttergood_farmley_order_pay_script( $order_id ) {
		if ( ! nuttergood_farmley_is_order_pay_page() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$js  = $dir . '/assets/js/farmley-order-pay.js';

		if ( ! file_exists( $js ) ) {
			return;
		}

		$deps = array( 'nuttergood-farmley-button-loader' );
		if ( wp_script_is( 'razorpay_wc_script', 'registered' ) ) {
			$deps[] = 'razorpay_wc_script';
		}

		wp_enqueue_script(
			'nuttergood-farmley-order-pay',
			$uri . '/assets/js/farmley-order-pay.js',
			$deps,
			filemtime( $js ),
			true
		);

		$order = wc_get_order( $order_id );

		wp_localize_script(
			'nuttergood-farmley-order-pay',
			'ngFarmleyOrderPay',
			array(
				'orderPayUrl' => ( $order && $order->needs_payment() ) ? $order->get_checkout_payment_url() : '',
				'checkoutUrl' => function_exists( 'nuttergood_farmley_get_checkout_url' )
					? nuttergood_farmley_get_checkout_url()
					: ( function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout-2/' ) ),
				'i18n'        => array(
					'processingTitle' => __( 'Processing payment', 'nuttergood' ),
					'processingText'  => __( 'Please wait while we confirm your payment. Do not close this window.', 'nuttergood' ),
					'cancelledTitle'  => __( 'Payment cancelled', 'nuttergood' ),
					'cancelledText'   => __( 'No worries — your order is saved. You can try paying again whenever you are ready.', 'nuttergood' ),
					'modalClosedText' => __( 'Payment window closed. Tap Pay Now when you are ready to continue.', 'nuttergood' ),
					'tryAgain'        => __( 'Try again', 'nuttergood' ),
					'backToCheckout'  => __( 'Back to checkout', 'nuttergood' ),
				),
			)
		);
	}
	add_action( 'woocommerce_receipt_razorpay', 'nuttergood_farmley_order_pay_script', 999 );
}

if ( ! function_exists( 'nuttergood_farmley_order_pay_script_fallback' ) ) {
	/**
	 * Fallback enqueue when payment gateway is not Razorpay on order-pay.
	 */
	function nuttergood_farmley_order_pay_script_fallback() {
		if ( ! nuttergood_farmley_is_order_pay_page() || wp_script_is( 'nuttergood-farmley-order-pay', 'enqueued' ) ) {
			return;
		}

		$order_id = absint( get_query_var( 'order-pay' ) );
		if ( $order_id ) {
			nuttergood_farmley_order_pay_script( $order_id );
		}
	}
	add_action( 'wp_footer', 'nuttergood_farmley_order_pay_script_fallback', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_order_pay_cancel_notice' ) ) {
	function nuttergood_farmley_order_pay_cancel_notice() {
		if ( ! nuttergood_farmley_is_order_pay_page() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_GET['ng_payment'] ) || 'cancelled' !== sanitize_key( wp_unslash( $_GET['ng_payment'] ) ) ) {
			return;
		}

		wc_add_notice(
			__( 'Payment was cancelled. Your order is still pending — you can complete payment below.', 'nuttergood' ),
			'notice'
		);
	}
	add_action( 'before_woocommerce_pay', 'nuttergood_farmley_order_pay_cancel_notice', 8 );
}

if ( ! function_exists( 'nuttergood_farmley_razorpay_cancel_redirect' ) ) {
	/**
	 * Razorpay Cancel submits an empty POST to wc-api and the plugin redirects to checkout.
	 * On localhost the cart is empty at that point, so send the customer back to order-pay.
	 */
	function nuttergood_farmley_razorpay_cancel_redirect( $location ) {
		if ( empty( $_GET['wc-api'] ) || 'razorpay' !== sanitize_key( wp_unslash( $_GET['wc-api'] ) ) ) {
			return $location;
		}

		if ( ! empty( $_POST ) ) {
			return $location;
		}

		$order_key = isset( $_GET['order_key'] ) ? wc_clean( wp_unslash( $_GET['order_key'] ) ) : '';
		if ( ! $order_key ) {
			return $location;
		}

		$order_id = wc_get_order_id_by_order_key( $order_key );
		$order    = $order_id ? wc_get_order( $order_id ) : false;

		if ( ! $order || ! $order->needs_payment() ) {
			return $location;
		}

		return add_query_arg( 'ng_payment', 'cancelled', $order->get_checkout_payment_url() );
	}
	add_filter( 'wp_redirect', 'nuttergood_farmley_razorpay_cancel_redirect', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_order_pay_overlay' ) ) {
	function nuttergood_farmley_order_pay_overlay() {
		if ( ! nuttergood_farmley_is_order_pay_page() ) {
			return;
		}
		?>
		<div id="ng-farmley-payment-overlay" class="ng-farmley-payment-overlay" hidden aria-hidden="true">
			<div class="ng-farmley-payment-overlay__panel" role="dialog" aria-modal="true" aria-labelledby="ng-farmley-payment-overlay-title">
				<div class="ng-farmley-payment-overlay__icon" aria-hidden="true">
					<span class="ng-farmley-payment-overlay__spinner"></span>
				</div>
				<h2 id="ng-farmley-payment-overlay-title" class="ng-farmley-payment-overlay__title"></h2>
				<p class="ng-farmley-payment-overlay__text"></p>
				<div class="ng-farmley-payment-overlay__actions"></div>
			</div>
		</div>
		<?php
	}
	add_action( 'wp_footer', 'nuttergood_farmley_order_pay_overlay', 5 );
}
