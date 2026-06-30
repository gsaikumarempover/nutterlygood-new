<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

?>

<div <?php qode_quick_view_for_woocommerce_class_attribute( $wrapper_classes ); ?>>
	<a role="button" tabindex="0" <?php qode_quick_view_for_woocommerce_class_attribute( $holder_classes ); ?> <?php qode_quick_view_for_woocommerce_inline_attrs( $holder_data ); ?>>
		<?php qode_quick_view_for_woocommerce_template_part( 'quick-view/shortcodes/quick-view-button', 'templates/parts/content', '', $params ); ?>
	</a>
</div>
