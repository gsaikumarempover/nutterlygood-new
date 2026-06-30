<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

?>
<div id="qode-quick-view-for-woocommerce-pop-up" <?php qode_quick_view_for_woocommerce_class_attribute( apply_filters( 'qode_quick_view_for_woocommerce_filter_set_quick_view_pop_up_classes', $quick_view_classes ) ); ?>>
	<div class="qqvfw-m-overlay"></div>
	<div class="qqvfw-m-content">
		<div class="qqvfw-m-content-inner">
			<?php qode_quick_view_for_woocommerce_template_part( 'quick-view', 'templates/parts/close', '', $params ); ?>
			<div class="qqvfw-m-product woocommerce single-product"></div>
			<?php qode_quick_view_for_woocommerce_template_part( 'quick-view', 'templates/parts/spinner', '', $params ); ?>
		</div>
	</div>
</div>
