<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$removed_text_option = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_removed_text' );
$removed_text        = ! empty( $removed_text_option ) ? $removed_text_option : esc_html__( 'Item is successfully removed from wishlist.', 'qode-wishlist-for-woocommerce' );
?>
<p class="qwfw-m-response qwfw--removed">
	<?php
	qode_wishlist_for_woocommerce_svg_icon( 'check', 'qwfw-m-response-icon' );
	echo wp_kses_post( $removed_text );
	?>
</p>
