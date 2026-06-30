<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

qode_quick_view_for_woocommerce_template_part( 'quick-view', 'variations/pop-up/layouts/pop-up', '', $params );

// Include additional quick view variations.
do_action( 'qode_quick_view_for_woocommerce_action_set_quick_view_variation_template', $params );
?>
<input type="hidden" class="qqvfw-hidden-type" data-quick-view-type="<?php echo esc_attr( $quick_view_type ); ?>" data-quick-view-type-mobile="<?php echo esc_attr( $quick_view_type_mobile ); ?>" data-quick-view-page-id="<?php echo esc_attr( $quick_view_page_id ); ?>" value="">
