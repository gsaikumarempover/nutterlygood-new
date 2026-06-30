<?php
$product_list_image = get_post_meta( get_the_ID(), 'qodef_product_list_image', true );
$has_image          = ! empty( $product_list_image ) || has_post_thumbnail();

if ( $has_image ) {
	$image_dimension     = isset( $image_dimension ) && ! empty( $image_dimension ) ? esc_attr( $image_dimension['size'] ) : apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
	$custom_image_width  = isset( $custom_image_width ) && '' !== $custom_image_width ? intval( $custom_image_width ) : 0;
	$custom_image_height = isset( $custom_image_height ) && '' !== $custom_image_height ? intval( $custom_image_height ) : 0;
	?>
	<div class="qodef-e-media-image">
		<?php if ( function_exists( 'woocommerce_template_loop_product_link_open' ) ) { woocommerce_template_loop_product_link_open(); } ?>
		<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/mark', 'new' ); ?>
			<?php
			if ( 'catalogue' !== $layout || 'no' !== $enable_price ) {
				greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/mark' );
			}
			?>
			<?php echo greenpath_core_get_list_shortcode_item_image( $image_dimension, $product_list_image, $custom_image_width, $custom_image_height ); ?>
		<?php if ( function_exists( 'woocommerce_template_loop_product_link_close' ) ) { woocommerce_template_loop_product_link_close(); } ?>
	</div>
<?php } ?>
