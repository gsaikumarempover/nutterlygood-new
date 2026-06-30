<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
?>
<div <?php qode_wishlist_for_woocommerce_class_attribute( $wrapper_classes ); ?>>
	<a role="button" tabindex="0" <?php qode_wishlist_for_woocommerce_class_attribute( $holder_classes ); ?> <?php qode_wishlist_for_woocommerce_inline_attrs( $holder_data ); ?>>
		<?php qode_wishlist_for_woocommerce_template_part( 'wishlist/shortcodes/add-to-wishlist', 'templates/parts/content', '', $params ); ?>
	</a>
</div>
