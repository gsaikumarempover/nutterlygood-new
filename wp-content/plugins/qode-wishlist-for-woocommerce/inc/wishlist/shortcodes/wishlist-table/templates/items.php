<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$table_items = qode_wishlist_for_woocommerce_get_wishlist_table_items( $params );
?>
<thead class="qwfw-m-items-heading">
	<tr>
		<?php
		// Set additional hook before module for 3rd party elements.
		do_action( 'qode_wishlist_for_woocommerce_action_wishlist_table_before_heading', $params );

		foreach ( $table_items as $table_item_key => $table_item_value ) {
			$table_item_class = 'product-' . str_replace( '_', '-', $table_item_key );
			?>
			<th class="qwfw-m-items-heading-item <?php echo esc_attr( $table_item_class ); ?>">
				<?php
				// Set additional hook before module for 3rd party elements.
				do_action( 'qode_wishlist_for_woocommerce_action_wishlist_table_item_before_heading', $table_item_key );

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo apply_filters(
					'qode_wishlist_for_woocommerce_filter_wishlist_table_item_heading',
					wp_kses_post( $table_item_value['label'] ),
					$table_item_key
				);

				// Set additional hook after module for 3rd party elements.
				do_action( 'qode_wishlist_for_woocommerce_action_wishlist_table_item_after_heading', $table_item_key );
				?>
			</th>
			<?php
		}

		// Set additional hook before module for 3rd party elements.
		do_action( 'qode_wishlist_for_woocommerce_action_wishlist_table_after_heading', $params );
		?>
	</tr>
</thead>
<tbody class="qwfw-m-items-content">
	<?php
	foreach ( $items as $item_key => $item ) {
		/**
		 * Product
		 *
		 * @var WC_Product|WC_Product_Variation $product
		 */
		global $product;

		$item_id = $item['product_id'] ?? '';
		$product = apply_filters( 'qode_wishlist_for_woocommerce_filter_wishlist_table_item_product', wc_get_product( $item_id ), $item_id );

		// If the current item is not product, skip it.
		if ( empty( $product ) ) {
			continue;
		}

		$product_permalink = apply_filters( 'qode_wishlist_for_woocommerce_filter_wishlist_table_item_permalink', $product->is_visible() ? $product->get_permalink( $item_id ) : '', $item_id );

		$item_classes   = array();
		$item_classes[] = 'qwfw--' . esc_attr( $product->get_type() );
		if ( $product->is_type( 'variation' ) && ! empty( $product->get_attributes() ) ) {
			$item_classes[] = 'qwfw--has-variations';
		}
		?>
		<tr class="qwfw-m-items-content-row qwfw-e <?php echo esc_attr( implode( ' ', $item_classes ) ); ?>" data-item-id="<?php echo intval( $item_id ); ?>">
			<?php
			// Set additional hook before module for 3rd party elements.
			do_action( 'qode_wishlist_for_woocommerce_action_wishlist_table_before_item_content', $product, $item_id, $params );

			foreach ( $table_items as $table_item_key => $table_item_value ) {
				$item_class = 'qwfw-e-item product-' . str_replace( '_', '-', $table_item_key );
				$item_args  = array(
					'product'           => $product,
					'product_permalink' => $product_permalink,
					'item'              => $item,
					'item_id'           => $item_id,
				);

				if ( empty( $table_item_value ) ) {
					continue;
				}
				?>
				<td class="<?php echo esc_attr( $item_class ); ?>">
					<div class="qwfw-e-item-inner">
						<?php
						$templates = is_array( $table_item_value['template'] ) ? $table_item_value['template'] : array( $table_item_value['template'] );

						// Set additional hook before module for 3rd party elements.
						do_action( 'qode_wishlist_for_woocommerce_action_wishlist_table_item_before_column_content', $table_item_key, $product, $item_id, $params );

						foreach ( $templates as $template ) {
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo apply_filters(
								'qode_wishlist_for_woocommerce_filter_wishlist_table_item_content',
								qode_wishlist_for_woocommerce_get_template_part( 'wishlist/shortcodes/wishlist-table', 'templates/item-parts/' . esc_attr( $template ), '', $item_args ),
								$table_item_key,
								$template,
								$item_args,
								$item_id
							);
						}

						// Set additional hook after module for 3rd party elements.
						do_action( 'qode_wishlist_for_woocommerce_action_wishlist_table_item_after_column_content', $table_item_key, $product, $item_id, $params );
						?>
					</div>
				</td>
				<?php
			}

			// Set additional hook before module for 3rd party elements.
			do_action( 'qode_wishlist_for_woocommerce_action_wishlist_table_item_after_content', $product, $item_id, $params );
			?>
		</tr>
	<?php } ?>
</tbody>
