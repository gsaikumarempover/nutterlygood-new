<div <?php wc_product_cat_class( $item_classes ); ?>>
	<div class="qodef-e-inner">
		<a href="<?php echo get_term_link( $category_slug, 'product_cat' ); ?>">
			<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-category-list', 'templates/post-info/image', '', $params ); ?>
			<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-category-list', 'templates/post-info/title', '', $params ); ?>
		</a>
	</div>
</div>
