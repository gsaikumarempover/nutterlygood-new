<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
foreach ( $table_items as $table_item_key => $table_item_value ) {
	$label            = ! empty( $table_item_value ) ? $table_item_value : '';
	$item_class       = 'qcfw-m-item product-' . str_replace( '_', '-', $table_item_key );
	$item_label_class = 'qcfw-m-label product-' . str_replace( '_', '-', $table_item_key );
	$row_classes      = array( 'qcfw-m-item-wrapper' );
	?>
<div <?php qode_compare_for_woocommerce_class_attribute( $row_classes ); ?>>
	<div class="<?php echo esc_attr( $item_label_class ); ?>"><?php echo esc_html( $label ); ?></div>
	<?php
	foreach ( $items as $item ) {
		$item_id = $item['product_id'] ?? '';
		$product = apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_item_product', wc_get_product( $item_id ), $item_id );

		// If the current item is not product, or it is excluded, skip it.
		if ( empty( $product ) ) {
			continue;
		}

		$product_permalink = apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_item_permalink', $product->is_visible() ? $product->get_permalink( $item_id ) : '', $item_id );

		$item_args = array(
			'product'           => $product,
			'product_permalink' => $product_permalink,
			'item_id'           => $item_id,
		);
		?>
		<div class="<?php echo esc_attr( $item_class ); ?>" data-item-id="<?php echo intval( $item_id ); ?>">
			<div class="qcfw-m-item-inner">
				<?php
				// Set additional hook before module for 3rd party elements.
				do_action( 'qode_compare_for_woocommerce_action_comparison_table_item_before_field_content', $table_item_key, $product, $item_id );

				$temp     = QODE_COMPARE_FOR_WOOCOMMERCE_INC_PATH . '/compare/templates/item-parts/' . $table_item_key;
				$template = qode_compare_for_woocommerce_get_template_with_slug( $temp, '' );

				if ( file_exists( $template ) ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo apply_filters(
						'qode_compare_for_woocommerce_filter_comparison_table_item_content',
						qode_compare_for_woocommerce_execute_template_with_params( $template, $item_args ),
						$table_item_key,
						$item_id
					);
				} elseif ( taxonomy_exists( $table_item_key ) ) {
					$taxonomies = qode_compare_for_woocommerce_get_product_taxonomies( $item_id, $table_item_key );

					echo wp_kses_post( apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_' . $table_item_key, $taxonomies, $product, $item_id ) );
				}
				// Set additional hook after module for 3rd party elements.
				do_action( 'qode_compare_for_woocommerce_action_comparison_table_item_after_field_content', $table_item_key, $product, $item_id );
				?>
			</div>
		</div>
		<?php
	}
	?>
</div>
	<?php
}
