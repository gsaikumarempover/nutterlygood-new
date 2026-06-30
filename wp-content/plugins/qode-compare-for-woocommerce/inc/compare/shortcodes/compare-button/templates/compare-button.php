<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
?>
<a role="button" tabindex="0" <?php qode_compare_for_woocommerce_class_attribute( $holder_classes ); ?> href='#' <?php qode_compare_for_woocommerce_inline_attrs( $holder_data ); ?>>
	<span class="qcfw-button-text"><?php echo esc_html( $button_text ); ?></span>
	<span class="qcfw-spinner-icon"><?php qode_compare_for_woocommerce_render_svg_icon( 'spinner' ); ?></span>
</a>
