<?php $columns_number = isset( $columns ) ? $columns : 1; ?>
<div class="woocommerce-product-gallery qodef-grid qodef-layout--columns qodef-col-num--<?php echo esc_attr( $columns_number ); ?>">
	<div class="woocommerce-product-gallery__wrapper qodef-grid-inner clear">
		<?php do_action( 'greenpath_core_action_woo_single_product_gallery_images' ); ?>
	</div>
</div>
