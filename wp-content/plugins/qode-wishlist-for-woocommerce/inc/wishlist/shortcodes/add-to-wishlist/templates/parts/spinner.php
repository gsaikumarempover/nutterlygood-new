<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
?>
<span class="qwfw-m-spinner qwfw-spinner-icon">
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_spinner_icon', qode_wishlist_for_woocommerce_get_svg_icon( 'spinner' ), $item_id );
	?>
</span>
