<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
$repeat_price          = 'no' !== qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_repeat_price' );
$repeat_add_to_cart    = 'yes' === qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_repeat_add_to_cart' );
$selected_table_fields = $selected_table_fields ?? qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_table_fields' );

$items                 = $items ?? array();
$selected_table_fields = is_array( $selected_table_fields ) ? array_filter(
	$selected_table_fields,
	function ( $value ) {
		return ! empty( $value );
	}
) : array();
$all_fields            = qode_compare_for_woocommerce_get_comparison_table_fields();
// Pairs keys with field positions and potentially removes non-existed fields from free plugin that might be still saved in the option.
$selected_table_fields = array_intersect_key( array_flip( $selected_table_fields ), $all_fields );
// Includes only selected fields from all fields.
$selected_table_items = array_intersect_key( $all_fields, $selected_table_fields );
// Selected table items are sorted.
$selected_table_items = array_replace( $selected_table_fields, $selected_table_items );

$table_items = apply_filters(
	'qode_compare_for_woocommerce_filter_table_items',
	array_merge(
		array( 'thumbnail' => '' ),
		$selected_table_items
	),
	$items
);

$additional_table_items = array();

if ( $repeat_price && array_key_exists( 'price', $table_items ) ) {
	$additional_table_items['price'] = $table_items['price'];
}

if ( $repeat_add_to_cart && array_key_exists( 'add-to-cart', $table_items ) ) {
	$additional_table_items['add-to-cart'] = $table_items['add-to-cart'];
}
?>
<div class="qcfw-m-table qcfw-m">
	<?php

	qode_compare_for_woocommerce_template_part(
		'compare/templates',
		'parts/items',
		'',
		array(
			'table_items' => $table_items,
			'items'       => $items,
		)
	);

	if ( ! empty( $additional_table_items ) ) {
		qode_compare_for_woocommerce_template_part(
			'compare/templates',
			'parts/items',
			'',
			array(
				'table_items' => $additional_table_items,
				'items'       => $items,
			)
		);
	}
	?>
</div>
