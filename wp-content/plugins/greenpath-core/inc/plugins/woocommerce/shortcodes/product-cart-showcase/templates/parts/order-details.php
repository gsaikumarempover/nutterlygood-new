<?php

$product_currency = get_woocommerce_currency_symbol();
$price_format     = get_woocommerce_price_format();
$total_price      = 0;
$quantity         = 0;
$price            = 0;

$product_ids          = $params['product_ids'];
$products_ids_array   = array();
$products_price_array = array();

$decimal_separator  = wc_get_price_decimal_separator();
$thousand_separator = wc_get_price_thousand_separator();
$num_decimals       = wc_get_price_decimals();
$decimal            = '';

if ( is_single() && is_product() ) {
	$product            = greenpath_core_woo_get_global_product();
	$current_product_id = $product->get_id();
	$cross_sell_ids     = $product->get_cross_sell_ids();

	if ( ! empty( $cross_sell_ids ) ) {

		foreach ( $cross_sell_ids as $cross_sell_id ) {
			$new_product            = wc_get_product( $cross_sell_id );
			$product_price          = $new_product->get_price();
			$products_ids_array[]   = $cross_sell_id;
			$products_price_array[] = $product_price;
		}
	}
} else if( ! empty( $product_ids ) ) {

	foreach( explode( ',', $product_ids ) as $product_id ) {
		$new_product            = wc_get_product( $product_id );
		$product_price          = $new_product->get_price();
		$products_ids_array[]   = $product_id;
		$products_price_array[] = $product_price;
	}
}

if( ! empty( $products_price_array ) ) {
	$total_price = array_sum( $products_price_array );

	if ( $num_decimals ) {
		$decimal = sprintf( '<span class="qodef--decimal">%0' . $num_decimals . 'd</span>', round( ( $total_price - intval( $total_price ) ) * pow( 10, $num_decimals ) ) );
	}

	$price    = intval( $total_price ) . $decimal;
	$quantity = count( $products_price_array );
}

?>

<div class="qodef-m-order-details">
    <h3 class="qodef-m-order-details-title"><?php esc_html_e( 'Total Price', 'greenpath-core' ); ?></h3>
	<div class="qodef-m-order-details-inner">
		<strong class="qodef-m-order-price">
			<?php if( ! empty( $price ) ) { ?>
				<?php echo sprintf( $price_format, $product_currency, qode_framework_wp_kses_html( 'content', $price ) ) ?>
			<?php } ?>
		</strong>
	    <p class="qodef-m-order-quantity"><?php echo sprintf( esc_html__('for %s item(s)', 'greenpath-core' ), $quantity ); ?></p>
	</div>
	<div class="qodef-m-order-products">
		<?php
			if( ! empty( $products_ids_array ) ) {
				$html = '';

				foreach( $products_ids_array as $product ) {
					$html .= '<div class="qodef-m-order-product">';
					$html .= '<input type="checkbox" name="qodef-product-' . $product . '" id="qodef-product-' . $product . '"  value="' . $product . '" checked=checked />';
					$html .= '<label for="qodef-product-' . $product . '">' . wc_get_product( $product )->get_title() . '</label>';
					$html .= '</div>';
				}

				echo qode_framework_wp_kses_html( 'content custom', $html );
			}
		?>
	</div>
    <input type="hidden" id="product-cart-showcase-ids" name="product-cart-showcase-ids" value="<?php echo implode(',', $products_ids_array ); ?>">
</div>
<?php