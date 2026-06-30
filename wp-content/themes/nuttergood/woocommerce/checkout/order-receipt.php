<?php
/**
 * Order receipt — Farmley styled pay page (Razorpay / post–place order).
 *
 * @see woocommerce/templates/checkout/order-receipt.php
 * @package NutterlyGood
 * @version 3.2.0
 */

defined( 'ABSPATH' ) || exit;

$order_items = $order->get_items();
$totals      = $order->get_order_item_totals();
?>

<div class="ng-farmley-order-pay__layout">
	<section class="ng-farmley-order-pay__card ng-farmley-order-pay__summary" aria-labelledby="ng-farmley-order-summary-title">
		<h2 id="ng-farmley-order-summary-title" class="ng-farmley-order-pay__card-title">
			<?php esc_html_e( 'Order summary', 'nuttergood' ); ?>
		</h2>

		<dl class="ng-farmley-order-pay__meta">
			<div class="ng-farmley-order-pay__meta-row">
				<dt><?php esc_html_e( 'Order number', 'nuttergood' ); ?></dt>
				<dd>#<?php echo esc_html( $order->get_order_number() ); ?></dd>
			</div>
			<div class="ng-farmley-order-pay__meta-row">
				<dt><?php esc_html_e( 'Date', 'nuttergood' ); ?></dt>
				<dd><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></dd>
			</div>
			<?php if ( $order->get_payment_method_title() ) : ?>
				<div class="ng-farmley-order-pay__meta-row">
					<dt><?php esc_html_e( 'Payment method', 'nuttergood' ); ?></dt>
					<dd><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></dd>
				</div>
			<?php endif; ?>
		</dl>

		<?php if ( ! empty( $order_items ) ) : ?>
			<div class="ng-farmley-order-pay__items">
				<h3 class="ng-farmley-order-pay__items-title"><?php esc_html_e( 'Items', 'nuttergood' ); ?></h3>
				<ul class="ng-farmley-order-pay__item-list">
					<?php foreach ( $order_items as $item_id => $item ) : ?>
						<?php
						if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
							continue;
						}
						$product = $item->get_product();
						?>
						<li class="ng-farmley-order-pay__item">
							<div class="ng-farmley-order-pay__item-main">
								<?php if ( $product && $product->get_image_id() ) : ?>
									<span class="ng-farmley-order-pay__item-thumb">
										<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
									</span>
								<?php endif; ?>
								<span class="ng-farmley-order-pay__item-name">
									<?php echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ); ?>
									<span class="ng-farmley-order-pay__item-qty">&times; <?php echo esc_html( $item->get_quantity() ); ?></span>
								</span>
							</div>
							<span class="ng-farmley-order-pay__item-total">
								<?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $totals ) ) : ?>
			<dl class="ng-farmley-order-pay__breakdown">
				<?php foreach ( $totals as $key => $total ) : ?>
					<?php
					if ( in_array( $key, array( 'payment_method' ), true ) ) {
						continue;
					}
					$row_class = ( 'order_total' === $key ) ? ' ng-farmley-order-pay__meta-row--total' : '';
					?>
					<div class="ng-farmley-order-pay__meta-row<?php echo esc_attr( $row_class ); ?>">
						<dt><?php echo wp_kses_post( $total['label'] ); ?></dt>
						<dd><?php echo wp_kses_post( $total['value'] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>
	</section>

	<section class="ng-farmley-order-pay__card ng-farmley-order-pay__payment" aria-labelledby="ng-farmley-order-payment-title">
		<h2 id="ng-farmley-order-payment-title" class="ng-farmley-order-pay__card-title">
			<?php esc_html_e( 'Payment', 'nuttergood' ); ?>
		</h2>
		<p class="ng-farmley-order-pay__payment-lead">
			<?php esc_html_e( 'Complete your payment securely. The Razorpay window will open automatically — or tap Pay Now below.', 'nuttergood' ); ?>
		</p>
		<div class="ng-farmley-order-pay__gateway">
			<?php do_action( 'woocommerce_receipt_' . $order->get_payment_method(), $order->get_id() ); ?>
		</div>
	</section>
</div>

<div class="clear"></div>
