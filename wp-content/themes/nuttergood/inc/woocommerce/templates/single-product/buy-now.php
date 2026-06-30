<?php
$product = greenpath_woo_get_global_product();
$checkout_url = ! empty ( wc_get_checkout_url() ) ? wc_get_checkout_url() : '' ;
?>
<div class="qodef-e-buy-now-wrapper">
	<span class="qodef-e-buy-now-label"><?php esc_html_e( ' or ', 'nuttergood' ); ?></span>
	<button formaction="<?php echo esc_attr( $checkout_url ); ?>" type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>"><?php esc_html_e( 'Buy Now', 'nuttergood' ); ?></button>
</div>
<?php