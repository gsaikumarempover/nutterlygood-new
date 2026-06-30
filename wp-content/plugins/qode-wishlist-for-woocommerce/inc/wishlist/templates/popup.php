<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$added_text_option = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_successfully_added_text' );
$added_text        = ! empty( $added_text_option ) ? $added_text_option : esc_html__( 'Item is added', 'qode-wishlist-for-woocommerce' );
?>
<p class="qwfw-m-response qwfw--added">
	<?php
	qode_wishlist_for_woocommerce_svg_icon( 'check', 'qwfw-m-response-icon' );
	echo wp_kses_post( $added_text );
	?>
</p>
