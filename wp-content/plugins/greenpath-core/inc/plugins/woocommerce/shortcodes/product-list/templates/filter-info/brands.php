<div class="qodef-e-options-wrapper">
	<div class="qodef-e-options-inner">
		<?php
		if ( ! empty( $params['product_ids'] ) ) {
			$product_brands = wp_get_object_terms( $params['product_ids'], 'product_brand' );
		} else {
			$product_brands = get_terms( 'product_brand' );
		}
		foreach ( $product_brands as $product_brand ) {
			if ( ! empty( $params['tax_slug'] ) ) {
				$products = get_posts(
					array(
						'post_type'      => 'product',
						'posts_per_page' => - 1,
						'fields'         => 'ids',
						'tax_query'      =>
							array(
								'relation' => 'AND',
								array(
									'taxonomy' => 'product_cat',
									'field'    => 'slug',
									'terms'    => explode( ',', $params['tax_slug'] ),
								),
								array(
									'taxonomy' => 'product_brand',
									'field'    => 'slug',
									'terms'    => $product_brand->slug,
								),
							),
					)
				);
				$count    = count( $products );
			} else {
				$count = $product_brand->count;
			}

			$id = isset( $mobile_id_prefix ) ? $mobile_id_prefix . $product_brand->slug : $product_brand->slug;
			?>
			<div class="qodef-e-checkbox">

				<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="qodef-product-brand" title="<?php echo esc_html( $product_brand->name ); ?>" data-id="<?php echo esc_attr( $product_brand->slug ); ?>" value="">
				<label for="<?php echo esc_attr( $id ); ?>">
					<span class="qodef-e-label"><?php echo esc_html( $product_brand->name ); ?></span>
					<span class="qodef-e-number"><?php echo esc_html( '(' . $count . ')' ); ?></span>
				</label>
			</div>
		<?php } ?>
	</div>
</div>
<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/show-more', '', $params ); ?>
