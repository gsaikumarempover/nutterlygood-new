<div class="qodef-filter-content">
	<?php
	// Include active filters
	greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/content-vertical', '', $params );
	?>
	<?php if ( is_active_sidebar( 'qodef-product-list-sidebar-widget-area' ) ) { ?>
		<div class="qodef-e-widget-area">
			<?php dynamic_sidebar( 'qodef-product-list-sidebar-widget-area' ); ?>
		</div>
	<?php } ?>
</div>

<?php
// Include loading spinner
if ( ! isset( $pagination_type ) || 'no-pagination' === $pagination_type ) {
	//	greenpath_render_svg_icon( 'spinner', 'qodef-filter-spinner' );
}
?>

