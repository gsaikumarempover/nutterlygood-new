<?php
/**
 * Push official Nutterly Good contact details into WP options, widgets, and product meta.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once get_template_directory() . '/inc/farmley/contact-info.php';

$info = nuttergood_farmley_contact_info();

echo "Updating contact info...\n";
echo "  Email: {$info['email']}\n";
echo "  Phone: {$info['phone']}\n";
echo "  Address: {$info['address']}\n";

// WooCommerce store address.
update_option( 'woocommerce_store_address', $info['address_line1'] );
update_option( 'woocommerce_store_address_2', $info['address_line2'] );
update_option( 'woocommerce_store_city', 'Hyderabad' );
update_option( 'woocommerce_store_postcode', '500075' );
update_option( 'woocommerce_default_country', 'IN:TS' );
echo "OK WooCommerce store address\n";

// Header top bar phone widget (block 13).
$blocks = get_option( 'widget_block', array() );
if ( ! is_array( $blocks ) ) {
	$blocks = array();
}
$blocks['_multiwidget'] = 1;
$blocks[13]             = array(
	'content' => '<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px;">Info: <a href="tel:' . esc_attr( $info['phone_tel'] ) . '">' . esc_html( $info['phone'] ) . '</a></p>
<!-- /wp:paragraph -->',
);
update_option( 'widget_block', $blocks );
echo "OK header top bar phone widget\n";

// Legacy footer SVG widgets (if present).
$svg_widgets = get_option( 'widget_greenpath_core_svg_icon', array() );
if ( is_array( $svg_widgets ) ) {
	$changed = false;
	foreach ( $svg_widgets as $id => $widget ) {
		if ( ! is_array( $widget ) ) {
			continue;
		}
		if ( isset( $widget['text'] ) && str_contains( (string) $widget['text'], '98765' ) ) {
			$svg_widgets[ $id ]['text']      = $info['phone'];
			$svg_widgets[ $id ]['icon_link'] = 'tel:' . $info['phone_tel'];
			$changed                       = true;
		}
		if ( isset( $widget['text'] ) && str_contains( (string) $widget['text'], 'info@nutterlygood.com' ) ) {
			$svg_widgets[ $id ]['text']      = $info['email'];
			$svg_widgets[ $id ]['icon_link'] = 'mailto:' . $info['email'];
			$changed                       = true;
		}
		if ( isset( $widget['text'] ) && str_contains( (string) $widget['text'], 'Main Road, New York' ) ) {
			$svg_widgets[ $id ]['text']      = $info['address'];
			$svg_widgets[ $id ]['icon_link'] = $info['map_url'];
			$changed                       = true;
		}
	}
	if ( $changed ) {
		update_option( 'widget_greenpath_core_svg_icon', $svg_widgets );
		echo "OK legacy footer SVG widgets\n";
	}
}

// Product packed-by meta.
global $wpdb;
$packed_by = $info['packed_by'];
$old_vals  = array(
	'Nutterly Good Foods Pvt. Ltd., Indore, Madhya Pradesh - 452001',
	'Nutterly Good Foods Pvt. Ltd., Khasra No. 17/2/2 & 3, 51/1/2 Kaji Palasiya, Indore Madhya Pradesh - 452001',
);
$updated_products = 0;
foreach ( $old_vals as $old ) {
	$count = (int) $wpdb->query(
		$wpdb->prepare(
			"UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE meta_key = '_ng_packed_by' AND meta_value = %s",
			$packed_by,
			$old
		)
	);
	$updated_products += $count;
}
// Set any missing packed_by on products.
$product_ids = wc_get_products( array( 'limit' => -1, 'status' => 'publish', 'return' => 'ids' ) );
foreach ( $product_ids as $pid ) {
	$current = get_post_meta( $pid, '_ng_packed_by', true );
	if ( '' === $current || in_array( $current, $old_vals, true ) ) {
		update_post_meta( $pid, '_ng_packed_by', $packed_by );
		++$updated_products;
	}
}
echo "OK product packed_by ({$updated_products} rows touched)\n";

wp_cache_flush();
echo "=== Done ===\n";