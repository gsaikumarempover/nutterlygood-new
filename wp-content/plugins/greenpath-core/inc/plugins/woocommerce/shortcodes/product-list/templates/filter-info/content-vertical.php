<div class="qodef-product-list-filter-vertical">
	<div class="qodef-filter-items-wrapper">
		<div class="qodef-filter-items">
			<?php if ( ! empty( $product_categories ) ) { ?>
				<div class="qodef-filter-item">
					<h5 class="qodef-filter-title"><?php esc_html_e( 'Categories', 'greenpath-core' ); ?></h5>
					<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/category', '', $params ); ?>
				</div>
			<?php } ?>
			<div class="qodef-filter-item qodef-product-rating">
				<h5 class="qodef-filter-title"><?php esc_html_e( 'Customer Rating', 'greenpath-core' ); ?></h5>
				<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/rating', '', $params ); ?>
			</div>
			<?php if ( ! empty( $first_attribute_filter ) ) { ?>
				<div class="qodef-filter-item">
					<h5 class="qodef-filter-title">
						<?php
						$attribute_id   = wc_attribute_taxonomy_id_by_name( $first_attribute_filter );
						$attribute_name = wc_get_attribute( $attribute_id )->name;
						echo esc_html( $attribute_name );
						?>
					</h5>
					<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/first-attribute', '', $params ); ?>
				</div>
			<?php } ?>
			<?php if ( ! empty( $second_attribute_filter ) ) { ?>
				<div class="qodef-filter-item">
					<h5 class="qodef-filter-title">
						<?php
						$attribute_id   = wc_attribute_taxonomy_id_by_name( $second_attribute_filter );
						$attribute_name = wc_get_attribute( $attribute_id )->name;
						echo esc_html( $attribute_name );
						?>
					</h5>
					<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/second-attribute', '', $params ); ?>
				</div>
			<?php } ?>
			<?php if ( ! empty( $product_brands ) ) { ?>
				<div class="qodef-filter-item">
					<h5 class="qodef-filter-title"><?php esc_html_e( 'Brands', 'greenpath-core' ); ?></h5>
					<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/brands', '', $params ); ?>
				</div>
			<?php } ?>
			<div class="qodef-filter-item">
				<h5 class="qodef-filter-title"><?php esc_html_e( 'Filter by Price', 'greenpath-core' ); ?></h5>
				<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/price', '', $params ); ?>
			</div>
		</div>
	</div>
</div>
