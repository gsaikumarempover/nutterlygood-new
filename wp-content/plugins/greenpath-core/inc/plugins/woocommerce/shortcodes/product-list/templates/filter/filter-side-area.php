<div class="qodef-filter-content">
	<div class="qodef-info-top">
		<h4 class="qodef-e-title"><?php echo esc_html__( 'Filter', 'greenpath-core' ); ?></h4>
		<a class="qodef-filter-close">
			<?php greenpath_core_render_svg_icon( 'close' ); ?>
		</a>
	</div>
	<?php if ( is_active_sidebar( 'qodef-product-list-side-area-widget-area' ) ) { ?>
		<div class="qodef-e-widget-area">
			<?php dynamic_sidebar( 'qodef-product-list-side-area-widget-area' ); ?>
		</div>
	<?php } ?>
	<?php
	// Include active filters
	greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/content-vertical', '', $params );
	?>
</div>
<div class="qodef-product-list-filter-holder qodef-filter-top-bar">
	<div class="qodef-e-info-left">
		<?php
		// Include filter opener
		greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/filter-opener', '', $params );

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
