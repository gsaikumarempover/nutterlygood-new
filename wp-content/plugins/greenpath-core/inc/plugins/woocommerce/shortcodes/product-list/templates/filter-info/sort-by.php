<?php if ( 'yes' === $enable_ordering_filter ) { ?>
	<div class="qodef-product-list-ordering qodef-dropdown-holder">
		<div id="order-current" class="qodef-current-value">
			<span class="qodef-e-text">
				<?php echo esc_html__( 'Default sorting', 'greenpath-core' ); ?>
			</span>
			<?php greenpath_core_render_svg_icon( 'chevron' ); ?>
		</div>
		<div class="qodef-dropdown-options">
			<?php echo greenpath_core_get_product_list_sorting_filter(); ?>
		</div>
	</div>
<?php } ?>
