<div class="qodef-e-options-wrapper">
	<div class="qodef-e-options-inner">
		<?php
		if ( ! empty( $product_categories ) ) {
			$active_slug = isset( $params['tax_slug'] ) ? $params['tax_slug'] : '';
			foreach ( $product_categories as $category ) {
				$id            = isset( $mobile_id_prefix ) ? $mobile_id_prefix . $category->slug : $category->slug;
				$is_active_cat = ( $active_slug !== '' && $category->slug === $active_slug );
				?>
				<div class="qodef-e-checkbox">
					<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="qodef-product-category" title="<?php echo esc_html( $category->name ); ?>" data-id="<?php echo esc_attr( $category->slug ); ?>" value="" <?php checked( $is_active_cat, true ); ?>>
					<label for="<?php echo esc_attr( $id ); ?>">
						<span class="qodef-e-label"><?php echo esc_html( $category->name ); ?></span>
						<span class="qodef-e-number"><?php echo esc_html( '(' . $category->count . ')' ); ?></span>
					</label>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>
<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/show-more', '', $params ); ?>
