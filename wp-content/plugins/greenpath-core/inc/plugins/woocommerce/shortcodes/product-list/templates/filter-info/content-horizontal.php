<div class="qodef-product-list-filter-horizontal">
	<?php if ( ! empty( $product_categories ) ) { ?>
		<div class="qodef-dropdown-holder">
			<span class="qodef-current-value">
				<?php esc_html_e( 'Categories', 'greenpath-core' ); ?>
				<?php greenpath_core_render_svg_icon( 'chevron' ); ?>
			</span>
			<div class="qodef-dropdown-options">
				<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/category', '', $params ); ?>
			</div>
		</div>
	<?php } ?>
	<div class="qodef-dropdown-holder qodef-product-rating">
		<span class="qodef-current-value">
				<?php esc_html_e( 'Rating', 'greenpath-core' ); ?>
				<?php greenpath_core_render_svg_icon( 'chevron' ); ?>
			</span>
		<div class="qodef-dropdown-options">
		<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/rating', '', $params ); ?>
		</div>
	</div>
	<?php if ( ! empty( $first_attribute_filter ) ) { ?>
		<div class="qodef-dropdown-holder">
			<span class="qodef-current-value">
				<?php
				$attribute_id   = wc_attribute_taxonomy_id_by_name( $first_attribute_filter );
				$attribute_name = wc_get_attribute( $attribute_id )->name;
				echo esc_html( $attribute_name );
				?>
				<?php greenpath_core_render_svg_icon( 'chevron' ); ?>
			</span>
			<div class="qodef-dropdown-options">
				<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/first-attribute', '', $params ); ?>
			</div>
		</div>
	<?php } ?>
	<?php if ( ! empty( $second_attribute_filter ) ) { ?>
		<div class="qodef-dropdown-holder">
			<span class="qodef-current-value">
				<?php
				$attribute_id   = wc_attribute_taxonomy_id_by_name( $second_attribute_filter );
				$attribute_name = wc_get_attribute( $attribute_id )->name;
				echo esc_html( $attribute_name );
				?>
				<?php greenpath_core_render_svg_icon( 'chevron' ); ?>
			</span>
			<div class="qodef-dropdown-options">
			<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/second-attribute', '', $params ); ?>
			</div>
		</div>
	<?php } ?>
	<?php if ( ! empty( $product_brands ) ) { ?>
		<div class="qodef-dropdown-holder">
			<span class="qodef-current-value">
				<?php esc_html_e( 'Brands', 'greenpath-core' ); ?>
				<?php greenpath_core_render_svg_icon( 'chevron' ); ?>
			</span>
			<div class="qodef-dropdown-options">
				<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/brands', '', $params ); ?>
			</div>
		</div>
	<?php } ?>
	<div class="qodef-dropdown-holder qodef-price-filter">
		<span class="qodef-current-value">
			<?php esc_html_e( 'Price', 'greenpath-core' ); ?>
			<?php greenpath_core_render_svg_icon( 'chevron' ); ?>
		</span>
		<div class="qodef-dropdown-options">
			<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/price', '', $params ); ?>
		</div>
	</div>
</div>
