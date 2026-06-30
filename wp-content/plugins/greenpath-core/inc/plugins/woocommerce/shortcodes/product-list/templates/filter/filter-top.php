<div class="qodef-product-list-filter-holder">
	<div class="qodef-e-info-left">
		<?php
		// Include filter content
		greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/content-horizontal', '', $params );

		// Include grid filter
		greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/grid-filter', '', $params );

		// Include result count
		greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/result-count', '', $params );
		?>
	</div>
	<div class="qodef-e-info-right">
		<?php
		// Include sort by dropdown
		greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/sort-by', '', $params );
		?>
	</div>
</div>
<?php
// Include loading spinner
if ( ! isset( $pagination_type ) || 'no-pagination' === $pagination_type ) {
//	greenpath_render_svg_icon( 'spinner', 'qodef-filter-spinner' );
}
?>
