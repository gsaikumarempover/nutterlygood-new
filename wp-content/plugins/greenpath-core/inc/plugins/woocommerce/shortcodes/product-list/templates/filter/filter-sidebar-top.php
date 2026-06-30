<div class="qodef-product-list-filter-holder qodef-filter-top-bar">
	<div class="qodef-e-info-left">
		<?php
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
