<?php
/**
 * Product loop item — Farmley card on category/tag archives; default hooks elsewhere.
 *
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$use_farmley_card = function_exists( 'nuttergood_farmley_is_woo_archive_loop' ) && nuttergood_farmley_is_woo_archive_loop();

if ( $use_farmley_card ) {
	wc_get_template( 'content-product-farmley.php' );
	return;
}
?>
<li <?php wc_product_class( '', $product ); ?>>
	<?php
	do_action( 'woocommerce_before_shop_loop_item' );
	do_action( 'woocommerce_before_shop_loop_item_title' );
	do_action( 'woocommerce_shop_loop_item_title' );
	do_action( 'woocommerce_after_shop_loop_item_title' );
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</li>
