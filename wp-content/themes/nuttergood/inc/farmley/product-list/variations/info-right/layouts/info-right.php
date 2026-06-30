<?php
/** @var string $item_classes */
/** @var array<string, mixed> $params */
?>
<li <?php wc_product_class( $item_classes . ' ng-farmley-popular-item' ); ?>>
	<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/countdown' ); ?>
	<div class="qodef-e-inner ng-farmley-popular-card">
		<div class="qodef-e-content ng-farmley-popular-card__body">
			<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/title', '', $params ); ?>
			<?php
			if ( function_exists( 'nuttergood_farmley_render_popular_product_weight' ) ) {
				nuttergood_farmley_render_popular_product_weight();
			}
			?>
			<div class="ng-farmley-popular-card__price">
				<?php
				if ( function_exists( 'nuttergood_farmley_render_product_card_price' ) ) {
					nuttergood_farmley_render_product_card_price();
				} else {
					greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/price', '', $params );
				}
				?>
			</div>
			<div class="ng-farmley-popular-card__actions">
				<?php
				if ( function_exists( 'nuttergood_farmley_popular_product_actions_open' ) ) {
					nuttergood_farmley_popular_product_actions_open();
				}
				greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/add-to-cart' );
				if ( function_exists( 'nuttergood_farmley_popular_product_actions_close' ) ) {
					nuttergood_farmley_popular_product_actions_close();
				}
				?>
			</div>
		</div>
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="qodef-e-media ng-farmley-popular-card__media">
				<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/image', '', $params ); ?>
				<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/link' ); ?>
			</div>
		<?php } ?>
	</div>
</li>
