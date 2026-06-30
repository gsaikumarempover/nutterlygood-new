<?php
$taxonomy_image_meta = get_term_meta( $category_id, 'thumbnail_id', true );
$taxonomy_image      = ! empty( $taxonomy_image_meta ) ? $taxonomy_image_meta : get_option( 'woocommerce_placeholder_image', 0 );
$alternate_svg_meta  = get_term_meta( $category_id, 'qodef_product_category_alternate_svg', true );
$alternate_svg       = ! empty( $alternate_svg_meta ) ? $alternate_svg_meta : '';
?>

<div class="qodef-e-image-holder" <?php echo qode_framework_get_inline_style( $image_holder_styles ); ?>>
	<?php if ( ! empty( $alternate_svg_meta ) && ! empty( $use_alternate_image ) && 'yes' === $use_alternate_image ) { ?>
	<span class="qodef-e-custom-svg">
		<?php echo qode_framework_wp_kses_html( 'svg', $alternate_svg_meta ); ?>
	</span>
	<?php } elseif ( ! empty( $taxonomy_image ) ) {
		$image_dimension     = isset( $image_dimension ) && ! empty( $image_dimension ) ? esc_attr( $image_dimension['size'] ) : 'full';
		$custom_image_width  = isset( $custom_image_width ) && '' !== $custom_image_width ? intval( $custom_image_width ) : 0;
		$custom_image_height = isset( $custom_image_height ) && '' !== $custom_image_height ? intval( $custom_image_height ) : 0;

		echo greenpath_core_get_list_shortcode_item_image( $image_dimension, $taxonomy_image, $custom_image_width, $custom_image_height );
	} ?>
</div>
