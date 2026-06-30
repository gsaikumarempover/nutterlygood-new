<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
$description = qode_compare_for_woocommerce_get_product_description( $product );

echo wp_kses_post( $description );
