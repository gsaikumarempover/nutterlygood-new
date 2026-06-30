<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$image_width  = qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_image_width' );
$image_height = qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_image_height' );
$hard_crop    = 'yes' === qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_image_hard_crop' );

if ( ! empty( $image_width ) && ! empty( $image_height ) ) {
	$product_image = qode_compare_for_woocommerce_generate_thumbnail( intval( $product->get_image_id() ), intval( $image_width ), intval( $image_height ), $hard_crop );
} else {
	$product_image = $product->get_image();
}

if ( ! empty( $product_image ) ) {

	if ( $product_permalink ) {
		$product_image = sprintf( '<a href="%s" class="qcfw-m-thumbnail-url">%s</a>', esc_url( apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_item_link', $product_permalink, $product, $item_id ) ), $product_image );
	}

	echo wp_kses_post( apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_item_thumbnail', $product_image, $product, $item_id ) );
}
