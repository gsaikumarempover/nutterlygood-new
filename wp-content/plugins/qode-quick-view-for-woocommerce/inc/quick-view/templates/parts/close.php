<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$icon_html  = qode_quick_view_for_woocommerce_get_svg_icon( 'close' );
$icon_class = 'qqvfw-icon--predefined';
?>
<a class="qqvfw-m-close <?php echo esc_attr( apply_filters( 'qode_quick_view_for_woocommerce_filter_quick_view_button_close_icon_class', $icon_class ) ); ?>" href="#" rel="noopener noreferrer">
	<?php echo do_shortcode( apply_filters( 'qode_quick_view_for_woocommerce_filter_quick_view_button_close_icon', $icon_html ) ); ?>
</a>
