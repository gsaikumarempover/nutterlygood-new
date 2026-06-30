<div class="qodef-product-list-filter-mobile">
	<?php $params['mobile_id_prefix'] = 'qodef-mobile-'; ?>
	<div class="qodef-e-info-top">
		<div class="qodef-e-info-left">
			<?php
			// Include filter content
			greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/filter-opener', '', $params );

			// Include grid filter
			greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/grid-filter', '', $params );
			?>
		</div>
		<?php
		// Include result count
		greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/result-count', '', $params );
		?>
	</div>
	<div class="qodef-e-info-scroll">
		<?php
		// Include filter content
		greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/filter-opener', '', $params );

		// Include grid filter
		greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/grid-filter', '', $params );
		?>
	</div>
	<div class="qodef-filter-content-mobile">
		<div class="qodef-info-top">
			<?php
			// Include mobile logo
			greenpath_core_get_mobile_header_logo_image();
			?>
			<a class="qodef-filter-close">
				<?php greenpath_core_render_svg_icon( 'close' ); ?>
			</a>
		</div>
		<div class="qodef-info-bottom">
			<?php
			// Include filter content
			greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/content-vertical', '', $params );
			?>
			<?php if ( 'yes' === $enable_ordering_filter ) { ?>
				<div class="qodef-filter-item">
					<h5 class="qodef-filter-title"><?php echo esc_html__( 'Default sorting', 'greenpath-core' ); ?></h5>
					<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/sort-by-mobile', '', $params ); ?>
				</div>
			<?php } ?>
			<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/filter-info/actions', '', $params ); ?>
		</div>
	</div>
</div>
