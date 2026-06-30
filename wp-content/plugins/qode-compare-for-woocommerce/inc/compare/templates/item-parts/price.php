<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
$price = $product->get_price_html();
if ( ! empty( $price ) ) {
	?>
	<div class="qcfw-m-price price">
	<?php
		echo wp_kses_post( apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_item_price', $price, $product, $item_id ) );
	?>
	</div>
	<?php
}
