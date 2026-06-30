<div <?php qode_framework_class_attribute( $holder_classes ); ?> <?php qode_framework_inline_style( $holder_styles ); ?> <?php qode_framework_inline_attr( $data_attr, 'data-options' ); ?>>
	<?php
	if ( 'yes' === $enable_custom_filter && 'simple' === $filter_type ) {
		$params['enable_filter'] = 'yes';
		// Include global filter from theme
		greenpath_core_theme_template_part( 'filter', 'templates/filter', '', $params );
	} elseif ( 'yes' === $enable_custom_filter && 'advanced' === $filter_type ) {
		// Include global filter from theme
		greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter/filter', $advanced_filter_type, $params );
	}
	?>
	<?php
	if ( 'yes' === $enable_custom_filter && 'advanced' === $filter_type && 'sidebar' === $advanced_filter_type ) {
		// Include global filter from theme
		greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter/filter', $advanced_filter_type . '-top', $params );
	}

	if ( 'yes' === $enable_custom_filter && 'advanced' === $filter_type ) {
		// Include mobile filter
		greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter/filter', 'mobile', $params );
	}
	?>
	<ul class="qodef-grid-inner">
		<?php
		// Include global masonry template from theme
		greenpath_core_theme_template_part( 'masonry', 'templates/sizer-gutter', '', $params['behavior'] );

		// Include items
		greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/loop', '', $params );
		?>
	</ul>
	<?php
	// Include global pagination from theme
	greenpath_core_theme_template_part( 'pagination', 'templates/pagination', $params['pagination_type'], $params );
	?>
</div>
