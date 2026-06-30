<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$product = wc_get_product( $item_id );

if ( ! isset( $item_id ) || empty( $product ) ) {
	return;
}

$qqvfw_custom_query = new WP_Query(
	array(
		'post_status'    => 'publish',
		'post_type'      => $product->is_type( 'variation' ) ? 'product_variation' : 'product',
		'posts_per_page' => 1,
		'post__in'       => array( $item_id ),
	)
);

if ( $qqvfw_custom_query->have_posts() ) {
	while ( $qqvfw_custom_query->have_posts() ) :
		$qqvfw_custom_query->the_post();

		if ( qode_quick_view_for_woocommerce_is_installed( 'wpbakery' ) && class_exists( 'WPBMap' ) ) {
			WPBMap::addAllMappedShortcodes();
		}

		// Set our predefined prop for product, so we can use it inside hooks.
		wc_set_loop_prop( 'qode_quick_view', $product->get_id() );
		?>
		<div id="product-<?php echo esc_attr( $product->get_id() ); ?>" <?php wc_product_class( '', $product ); ?> data-product_id="<?php echo esc_attr( $product->get_id() ); ?>">
			<div class="qqvfw-m-media-wrapper">
				<?php
				/**
				 * Hook: qode_quick_view_for_woocommerce_action_product_image.
				 *
				 * @hooked woocommerce_show_product_sale_flash - 10
				 * @hooked woocommerce_show_product_single_images - 20
				 */
				do_action( 'qode_quick_view_for_woocommerce_action_product_image' );
				?>
			</div>
			<div class="qqvfw-m-summary-wrapper">
				<?php
				do_action( 'qode_quick_view_for_woocommerce_action_before_product_summary' );
				?>
				<div class="summary entry-summary">
					<?php
					/**
					 * Hook: qode_quick_view_for_woocommerce_action_product_summary.
					 *
					 * @hooked woocommerce_template_single_title - 5
					 * @hooked woocommerce_template_single_price - 10
					 * @hooked woocommerce_template_single_rating - 15
					 * @hooked woocommerce_template_single_excerpt - 20
					 * @hooked woocommerce_template_single_add_to_cart - 25
					 * @hooked woocommerce_template_single_meta - 30
					 * @hooked woocommerce_template_single_share - 35
					 */
					do_action( 'qode_quick_view_for_woocommerce_action_product_summary' );
					?>
				</div>
				<?php
				do_action( 'qode_quick_view_for_woocommerce_action_after_product_summary' );
				?>
			</div>
		</div>
		<?php
		do_action( 'qode_quick_view_for_woocommerce_action_after_product' );
		?>
		<?php
		// end of the loop.
	endwhile;
}

wp_reset_postdata();
