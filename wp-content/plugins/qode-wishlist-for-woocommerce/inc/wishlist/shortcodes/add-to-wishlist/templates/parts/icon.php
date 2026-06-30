<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$is_item_added     = qode_wishlist_for_woocommerce_check_is_wishlist_item_added( $item_id );
$icon              = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_icon' );
$custom_icon       = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_custom_icon' );
$added_icon        = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_added_to_wishlist_icon' );
$added_custom_icon = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_added_to_wishlist_custom_icon' );

if ( $is_item_added ) {

	if ( ! empty( $added_icon ) ) {
		$icon = $added_icon;
	}

	if ( ! empty( $added_custom_icon ) ) {
		$custom_icon = $added_custom_icon;
	}
}

$icon_html = '';
if ( 'custom-icon' === $icon ) {
	$icon_class = 'qwfw--custom';

	if ( ! empty( $custom_icon ) ) {
		$icon_html = qode_wishlist_for_woocommerce_get_icon_html( $custom_icon );
	}
} else {
	$icon_class = 'qwfw--predefined';
	$icon_html  = qode_wishlist_for_woocommerce_get_svg_icon( $icon );
}

if ( ! empty( $icon_html ) ) {
	?>
	<span class="qwfw-m-icon <?php echo esc_attr( $icon_class ); ?>">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_icon', $icon_html, $item_id, $is_item_added );
		?>
	</span>
	<?php if ( 'icon-with-tooltip' === $button_type ) { ?>
		<span class="qwfw-m-tooltip" data-label="<?php echo esc_attr( qode_wishlist_for_woocommerce_get_add_to_wishlist_label( $params ) ); ?>"></span>
	<?php } ?>
<?php } ?>
