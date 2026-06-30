<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$added_text_option = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_already_added_text' );
$added_text        = ! empty( $added_text_option ) ? $added_text_option : esc_html__( 'The item is already in your wishlist!', 'qode-wishlist-for-woocommerce' );
?>
<p class="qwfw-m-response qwfw--already-added"><?php echo wp_kses_post( $added_text ); ?></p>
<?php qode_wishlist_for_woocommerce_template_part( 'wishlist', 'templates/parts/link' ); ?>
