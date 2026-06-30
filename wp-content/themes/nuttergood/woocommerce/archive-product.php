<?php
/**
 * Product archive — main shop uses GreenPath product-list shortcode.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );
do_action( 'woocommerce_shop_loop_header' );

$use_custom_shop_list = function_exists( 'nuttergood_farmley_is_main_shop_page' ) && nuttergood_farmley_is_main_shop_page();

if ( ! $use_custom_shop_list && woocommerce_product_loop() ) {
	do_action( 'woocommerce_before_shop_loop' );

	woocommerce_product_loop_start();

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();
			do_action( 'woocommerce_shop_loop' );
			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	do_action( 'woocommerce_after_shop_loop' );
} elseif ( ! $use_custom_shop_list ) {
	do_action( 'woocommerce_no_products_found' );
}

do_action( 'woocommerce_after_main_content' );
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );