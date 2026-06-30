<li <?php wc_product_class( $item_classes ); ?>>
	<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/countdown' ); ?>
	<div class="qodef-e-inner">
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="qodef-e-media">
				<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/image', '', $params ); ?>
				<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/link' ); ?>
			</div>
		<?php } ?>
		<div class="qodef-e-content">
			<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/title', '', $params ); ?>
			<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/rating', '', $params ); ?>
			<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/price', '', $params ); ?>
			<div class="qodef-action-holder">
				<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/add-to-cart' ); ?>
				<?php
				// Hook to include additional content inside product list item content
				do_action( 'greenpath_core_action_product_list_item_additional_content' );
				?>
			</div>
		</div>
	</div>
</li>
