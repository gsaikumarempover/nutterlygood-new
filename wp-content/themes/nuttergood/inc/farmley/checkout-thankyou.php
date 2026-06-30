<?php
/**
 * Order received / thank you page — accordions + continue shopping.
 *
 * @package NutterlyGood
 */

if ( ! function_exists( 'nuttergood_farmley_thankyou_setup' ) ) {
	/**
	 * Replace default flat order details with accordion layout.
	 */
	function nuttergood_farmley_thankyou_setup() {
		if ( ! function_exists( 'is_order_received_page' ) || ! is_order_received_page() ) {
			return;
		}

		remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
	}

	add_action( 'wp', 'nuttergood_farmley_thankyou_setup', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_thankyou_accordion_open' ) ) {
	/**
	 * @param string $id    Accordion id.
	 * @param string $title Visible title.
	 */
	function nuttergood_farmley_thankyou_accordion_open( $id, $title ) {
		printf(
			'<details class="ng-farmley-thankyou-accordion" id="%1$s"><summary class="ng-farmley-thankyou-accordion__summary"><span class="ng-farmley-thankyou-accordion__title">%2$s</span><span class="ng-farmley-thankyou-accordion__icon" aria-hidden="true"></span></summary><div class="ng-farmley-thankyou-accordion__panel">',
			esc_attr( $id ),
			esc_html( $title )
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_thankyou_accordion_close' ) ) {
	function nuttergood_farmley_thankyou_accordion_close() {
		echo '</div></details>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_thankyou_render_order_overview' ) ) {
	/**
	 * @param WC_Order $order Order object.
	 */
	function nuttergood_farmley_thankyou_render_order_overview( $order ) {
		if ( ! $order instanceof WC_Order ) {
			return;
		}
		?>
		<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
			<li class="woocommerce-order-overview__order order">
				<?php esc_html_e( 'Order number:', 'woocommerce' ); ?>
				<strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
			</li>

			<li class="woocommerce-order-overview__date date">
				<?php esc_html_e( 'Date:', 'woocommerce' ); ?>
				<strong><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
			</li>

			<?php if ( $order->get_billing_email() ) : ?>
				<li class="woocommerce-order-overview__email email">
					<?php esc_html_e( 'Email:', 'woocommerce' ); ?>
					<strong><?php echo esc_html( $order->get_billing_email() ); ?></strong>
				</li>
			<?php endif; ?>

			<li class="woocommerce-order-overview__total total">
				<?php esc_html_e( 'Total:', 'woocommerce' ); ?>
				<strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
			</li>

			<?php if ( $order->get_payment_method_title() ) : ?>
				<li class="woocommerce-order-overview__payment-method method">
					<?php esc_html_e( 'Payment method:', 'woocommerce' ); ?>
					<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
				</li>
			<?php endif; ?>

			<?php if ( $order->get_status() ) : ?>
				<li class="woocommerce-order-overview__status status">
					<?php esc_html_e( 'Status:', 'nuttergood' ); ?>
					<strong><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></strong>
				</li>
			<?php endif; ?>
		</ul>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_thankyou_render_order_details' ) ) {
	/**
	 * @param WC_Order $order Order object.
	 */
	function nuttergood_farmley_thankyou_render_order_details( $order ) {
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		add_filter( 'ng_farmley_thankyou_suppress_customer_details', '__return_true' );
		woocommerce_order_details_table( $order->get_id() );
		remove_filter( 'ng_farmley_thankyou_suppress_customer_details', '__return_true' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_thankyou_render_addresses' ) ) {
	/**
	 * @param WC_Order $order Order object.
	 */
	function nuttergood_farmley_thankyou_render_addresses( $order ) {
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		$show_customer_details = (int) $order->get_user_id() === (int) get_current_user_id();

		if ( ! $show_customer_details ) {
			echo '<p class="ng-farmley-thankyou-accordion__empty">' . esc_html__( 'Address details are not available for this order.', 'nuttergood' ) . '</p>';
			return;
		}

		wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
	}
}

if ( ! function_exists( 'nuttergood_farmley_thankyou_render_accordions' ) ) {
	/**
	 * @param WC_Order $order Order object.
	 */
	function nuttergood_farmley_thankyou_render_accordions( $order ) {
		if ( ! $order instanceof WC_Order || $order->has_status( 'failed' ) ) {
			return;
		}
		?>
		<div class="ng-farmley-thankyou-accordions">
			<?php
			nuttergood_farmley_thankyou_accordion_open( 'ng-thankyou-order-summary', __( 'Order summary', 'nuttergood' ) );
			nuttergood_farmley_thankyou_render_order_overview( $order );
			nuttergood_farmley_thankyou_accordion_close();

			nuttergood_farmley_thankyou_accordion_open( 'ng-thankyou-order-details', __( 'Order details', 'nuttergood' ) );
			nuttergood_farmley_thankyou_render_order_details( $order );
			nuttergood_farmley_thankyou_accordion_close();

			nuttergood_farmley_thankyou_accordion_open( 'ng-thankyou-address-details', __( 'Address details', 'nuttergood' ) );
			nuttergood_farmley_thankyou_render_addresses( $order );
			nuttergood_farmley_thankyou_accordion_close();
			?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_thankyou_get_shop_url' ) ) {
	function nuttergood_farmley_thankyou_get_shop_url() {
		if ( function_exists( 'wc_get_page_permalink' ) ) {
			$shop_url = wc_get_page_permalink( 'shop' );
			if ( $shop_url ) {
				return $shop_url;
			}
		}

		return home_url( '/shop/' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_thankyou_continue_shopping' ) ) {
	function nuttergood_farmley_thankyou_continue_shopping( $order = null ) {
		if ( $order instanceof WC_Order && $order->has_status( 'failed' ) ) {
			return;
		}
		?>
		<div class="ng-farmley-thankyou-actions">
			<a class="ng-farmley-thankyou-continue button" href="<?php echo esc_url( nuttergood_farmley_thankyou_get_shop_url() ); ?>">
				<?php esc_html_e( 'Continue shopping', 'nuttergood' ); ?>
			</a>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_thankyou_page_titles' ) ) {
	function nuttergood_farmley_thankyou_page_titles() {
		if ( ! function_exists( 'is_order_received_page' ) || ! is_order_received_page() ) {
			return;
		}
		?>
		<style>
			body.ng-farmley-order-received-page .entry-title,
			body.ng-farmley-order-received-page .qodef-page-title,
			body.ng-farmley-order-received-page #qodef-page-content > .qodef-m-title {
				display: none !important;
			}
		</style>
		<?php
	}
	add_action( 'wp_head', 'nuttergood_farmley_thankyou_page_titles', 99 );
}
