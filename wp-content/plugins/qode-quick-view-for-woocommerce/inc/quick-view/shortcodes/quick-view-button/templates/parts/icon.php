<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}


$icon_html  = qode_quick_view_for_woocommerce_get_svg_icon( 'quick-view' );
$icon_class = 'qqvfw-icon--predefined';
?>

<span class="qqvfw-m-icon <?php echo esc_attr( apply_filters( 'qode_quick_view_for_woocommerce_filter_quick_view_button_icon_class', $icon_class ) ); ?>">
	<?php echo do_shortcode( apply_filters( 'qode_quick_view_for_woocommerce_filter_quick_view_button_icon', $icon_html, $item_id ) ); ?>
</span>
