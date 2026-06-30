<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( isset( $compare_items ) && ! empty( $compare_items ) ) {
	?>
	<div class="qcfw-compare-items">
	<?php
	foreach ( $compare_items as $item ) {
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

		$product_image = $product->get_image( 'thumbnail' );
		?>
			<div class="qcfw-compare-item">
			<?php
			if ( ! empty( $product_image ) ) {
				echo wp_kses_post( apply_filters( 'qode_compare_for_woocommerce_filter_widget_thumbnail', $product_image, $product, $item_id ) );
			}
			qode_compare_for_woocommerce_template_part( 'compare', 'templates/item-parts/name', '', $item_args );
			qode_compare_for_woocommerce_template_part( 'compare', 'templates/item-parts/remove', '', $item_args );
			?>
			</div>
			<?php
	}
	?>
	</div>
	<div class="qcfw-compare-bottom">
		<?php if ( $show_compare ?? true ) { ?>
			<a class="qcfw-m-open-compare" rel="noopener noreferrer"  href="<?php echo esc_url( $opener_url ); ?>">
				<?php qode_compare_for_woocommerce_render_svg_icon( 'compare' ); ?>
				<span class="qcfw-m-open-compare--text"><?php esc_html_e( 'Compare products', 'qode-compare-for-woocommerce' ); ?></span>
			</a>
		<?php } ?>
		<a class="qcfw-clear qcfw-m-remove-button qcfw-spinner-item" aria-label="<?php esc_attr__( 'Clear all items', 'qode-compare-for-woocommerce' ); ?>" data-item-id="all" rel="noopener noreferrer">
			<?php qode_compare_for_woocommerce_render_svg_icon( 'trash' ); ?>
			<span class="qcfw-m-remove-button--text"><?php esc_html_e( 'Clear all', 'qode-compare-for-woocommerce' ); ?></span>
			<span class="qcfw-spinner-icon"><?php qode_compare_for_woocommerce_render_svg_icon( 'spinner' ); ?></span>
		</a>
	</div>
	<?php
} else {
	qode_compare_for_woocommerce_template_part( 'compare', 'templates/parts/not-found' );
}
