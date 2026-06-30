<li <?php wc_product_class( $item_classes ); ?>>
	<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/countdown' ); ?>
	<div class="qodef-e-inner ng-farmley-card">
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="qodef-e-media">
				<?php
				if ( function_exists( 'nuttergood_farmley_render_product_card_media' ) ) {
					nuttergood_farmley_render_product_card_media( $params );
				} else {
					greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/image', '', $params );
				}
				?>
				<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/link' ); ?>
			</div>
		<?php } ?>
		<div class="qodef-e-content">
			<div class="ng-farmley-card-foot">
				<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/title', '', $params ); ?>
				<?php
				if ( function_exists( 'nuttergood_farmley_render_product_weight_badges' ) ) {
					nuttergood_farmley_render_product_weight_badges();
				} elseif ( function_exists( 'nuttergood_farmley_render_product_weight' ) ) {
					nuttergood_farmley_render_product_weight();
				}
				?>
				<div class="ng-farmley-card-footer">
					<div class="ng-farmley-card-footer__price">
						<?php
						if ( function_exists( 'nuttergood_farmley_render_product_card_price' ) ) {
							nuttergood_farmley_render_product_card_price();
						} else {
							greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/price', '', $params );
						}
						?>
					</div>
					<div class="ng-farmley-card-footer__cart">
						<?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/add-to-cart' ); ?>
					</div>
				</div>
			</div>
			<div class="qodef-action-holder ng-farmley-card-actions">
				<?php do_action( 'greenpath_core_action_product_list_item_additional_content' ); ?>
			</div>
		</div>
	</div>
</li>