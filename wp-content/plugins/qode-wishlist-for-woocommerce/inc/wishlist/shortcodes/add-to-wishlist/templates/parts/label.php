<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
?>
<span class="qwfw-m-text"><?php echo wp_kses_post( qode_wishlist_for_woocommerce_get_add_to_wishlist_label( $params ) ); ?></span>
