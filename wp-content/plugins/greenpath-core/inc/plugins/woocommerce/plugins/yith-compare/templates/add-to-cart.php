<?php if ( ! $product->is_in_stock() ) {
    $button_classes = 'button ajax_add_to_cart qodef-button';
} else if ( $product->get_type() === 'variable' ) {
    $button_classes = 'button product_type_variable add_to_cart_button qodef-button';
} else if ( $product->get_type() === 'external' ) {
    $button_classes = 'button product_type_external qodef-button';
} else {
    $button_classes = 'button add_to_cart_button ajax_add_to_cart qodef-button';
}

$product_url = $product->get_permalink();
$button_text = $product->add_to_cart_text();
?>

<div class="qodef-pli-add-to-cart">
	<form class="cart" action="<?php echo esc_url( $product_url ); ?>" method="get">
		<button type="submit" class="single_add_to_cart_button button alt"><?php echo greenpath_core_get_svg_icon( 'button-icon' ) ?><?php echo esc_html( $button_text ); ?></button>
		<?php wc_query_string_form_fields( $product_url ); ?>
	</form>
</div>
