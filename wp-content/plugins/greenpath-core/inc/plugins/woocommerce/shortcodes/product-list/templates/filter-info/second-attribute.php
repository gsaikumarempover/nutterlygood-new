<?php if ( ! empty( $second_attribute_filter ) ) {
	$attribute_id   = wc_attribute_taxonomy_id_by_name( $second_attribute_filter );
	$attribute_type = wc_get_attribute( $attribute_id )->type;

	if ( ! empty( $params['product_ids'] ) ) {
		$attributes = wp_get_object_terms( $params['product_ids'], 'pa_' . $second_attribute_filter );
	} else {
		$attributes = get_terms( 'pa_' . $second_attribute_filter );
	}
	?>
		<div class="qodef-e-options-wrapper">
			<div class="qodef-e-options-inner">
				<?php
				foreach ( $attributes as $attribute ) {
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
											'taxonomy' => 'pa_' . $second_attribute_filter,
											'field'    => 'slug',
											'terms'    => $attribute->slug,
										),
									),
							)
						);
						$count    = count( $products );
					} else {
						$count = $attribute->count;
					}
					$attribute_color = '';
					$attribute_label = '';

					if ( 'colorpicker' === $attribute_type ) {
						$attribute_color = get_term_meta( $attribute->term_id, '_yith_wccl_value' );
						$attribute_color = $attribute_color ? $attribute_color[0] : '';
					}

					if ( 'label' === $attribute_type ) {
						$attribute_label = get_term_meta( $attribute->term_id, '_yith_wccl_value' );
						$attribute_label = $attribute_label ? $attribute_label[0] : '';
					}

					$id = isset( $mobile_id_prefix ) ? $mobile_id_prefix . $attribute->slug : $attribute->slug;
					?>
					<?php if ( ! empty( $attribute_color ) ) { ?>
						<a href="#" class="qodef-color-holder qodef-e-link" data-id="<?php echo esc_attr( $attribute->slug ); ?>" data-type="<?php echo esc_attr( $second_attribute_filter ); ?>">
							<span class="qodef-e-color" style="background-color: <?php echo esc_attr( $attribute_color ); ?>"></span>
						</a>
					<?php } elseif ( ! empty( $attribute_label ) ) { ?>
						<a href="#" class="qodef-label-holder qodef-e-link" data-id="<?php echo esc_attr( $attribute->slug ); ?>" data-type="<?php echo esc_attr( $second_attribute_filter ); ?>">
							<span class="qodef-e-label"><?php echo esc_html( $attribute_label ); ?></span>
						</a>
					<?php } else { ?>
						<div class="qodef-e-checkbox">
							<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="qodef-product-attribute" title="<?php echo esc_html( $attribute->name ); ?>" data-id="<?php echo esc_attr( $attribute->slug ); ?>" data-type="<?php echo esc_attr( $second_attribute_filter ); ?>" value="">
							<label for="<?php echo esc_attr( $id ); ?>">
								<span class="qodef-e-label"><?php echo esc_html( $attribute->name ); ?></span>
								<span class="qodef-e-number"><?php echo esc_html( $count ); ?></span>
							</label>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/show-more', '', $params ); ?>
	<?php
}
?>
