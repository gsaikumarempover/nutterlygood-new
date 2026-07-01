<?php
/**
 * Farmley product card for native WooCommerce archives (matches home product list).
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) ) {
	return;
}
?>
<li <?php wc_product_class( '', $product ); ?>>
	<div class="qodef-e-inner ng-farmley-card">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="qodef-e-media">
				<?php
				if ( function_exists( 'nuttergood_farmley_render_product_card_media' ) ) {
					nuttergood_farmley_render_product_card_media();
				} else {
					woocommerce_template_loop_product_thumbnail();
				}
				?>
			</div>
		<?php endif; ?>
		<div class="qodef-e-content">
			<div class="ng-farmley-card-foot">
				<h6 class="qodef-woo-product-title woocommerce-loop-product__title">
					<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link"><?php the_title(); ?></a>
				</h6>
				<?php
				if ( function_exists( 'nuttergood_farmley_render_product_weight_badges' ) ) {
					nuttergood_farmley_render_product_weight_badges( $product );
				}
				?>
				<div class="ng-farmley-card-footer">
					<div class="ng-farmley-card-footer__price">
						<?php
						if ( function_exists( 'nuttergood_farmley_render_product_card_price' ) ) {
							nuttergood_farmley_render_product_card_price( $product );
						} else {
							woocommerce_template_loop_price();
						}
						?>
					</div>
					<div class="ng-farmley-card-footer__cart">
						<?php woocommerce_template_loop_add_to_cart(); ?>
					</div>
				</div>
			</div>
			<div class="qodef-action-holder ng-farmley-card-actions">
				<?php do_action( 'greenpath_core_action_product_list_item_additional_content' ); ?>
			</div>
		</div>
	</div>
</li>
