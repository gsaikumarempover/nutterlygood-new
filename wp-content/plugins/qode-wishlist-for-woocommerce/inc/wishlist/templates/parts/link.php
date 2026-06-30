<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$args = array();

if ( isset( $table ) && ! empty( $table ) ) {
	$args['table'] = sanitize_text_field( $table );
}

$wishlist_page_url = qode_wishlist_for_woocommerce_get_wishlist_page_url_with_args( $args );
$shop_page_url     = wc_get_page_permalink( 'shop' );

if ( ! empty( $wishlist_page_url ) ) {
	$browse_label = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_browse_wishlist_label' );
	$button_label = ! empty( $browse_label ) ? $browse_label : esc_html__( 'Browse wishlist', 'qode-wishlist-for-woocommerce' );
	?>
	<div class="qwfw-m-form-response-actions">
		<a class="qwfw-m-form-response-button qwfw--wishlist <?php echo esc_attr( qode_wishlist_for_woocommerce_get_button_classes() ); ?>" href="<?php echo esc_url( $wishlist_page_url ); ?>"><?php echo esc_html( $button_label ); ?></a>
		<a class="qwfw-m-form-response-button qwfw--shop <?php echo esc_attr( qode_wishlist_for_woocommerce_get_button_classes() ); ?>" href="<?php echo esc_url( $shop_page_url ); ?>"><?php esc_html_e( 'Back to Shop', 'qode-wishlist-for-woocommerce' ); ?></a>
	</div>
<?php } ?>
