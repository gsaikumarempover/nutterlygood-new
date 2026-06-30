<?php

if ( ! function_exists( 'greenpath_core_add_woo_product_single_variation_big_gallery' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_woo_product_single_variation_big_gallery( $variations ) {
		$variations['big-gallery'] = esc_html__( 'Big Gallery', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_woo_single_product_layouts', 'greenpath_core_add_woo_product_single_variation_big_gallery' );
}

if ( ! function_exists( 'greenpath_core_load_single_woo_templates_big_gallery' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 */
	function greenpath_core_load_single_woo_templates_big_gallery() {

		// Remove default product single page content
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		remove_action( 'woocommerce_after_single_product_summary', 'greenpath_add_product_single_content_holder_end', 5 );

		// Remove slider template added by media option
		//remove_action( 'woocommerce_before_single_product_summary', 'greenpath_woo_product_render_slider_html', 10 );

		// Remove default cart showcase
		remove_action( 'woocommerce_after_single_product_summary', 'greenpath_core_single_cart_showcase_wrapper', 18 );
		remove_action( 'woocommerce_after_single_product_summary', 'greenpath_core_single_display_cart_showcase', 19 );
		remove_action( 'woocommerce_after_single_product_summary', 'greenpath_core_single_cart_showcase_wrapper_end', 20 );

		// Remove default product tabs
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

		// Set full width layout
		add_filter( 'greenpath_filter_page_inner_classes', 'greenpath_core_woo_single_product_full_width_class', 12 );

		// Add big gallery template to product single page
		add_action( 'woocommerce_before_single_product_summary', 'greenpath_core_woo_single_product_big_gallery_add_images', 20 );

		// Add thumbnail images to big gallery
		add_action( 'greenpath_core_action_woo_single_product_gallery_images', 'woocommerce_show_product_thumbnails' );

		// Set thumbnail image size for gallery
		add_filter( 'woocommerce_gallery_image_size', 'greenpath_core_woo_single_product_images_large_thumb_size' );

		// Move end of qodef-woo-single-inner wrapper to include new holder
		add_action( 'woocommerce_after_single_product_summary', 'greenpath_add_product_single_content_holder_end', 20 );

		// Add holder for tabs and related products
		add_action( 'woocommerce_after_single_product_summary', 'greenpath_core_woo_single_product_big_gallery_add_item_content_holder', 6 );
		add_action( 'woocommerce_after_single_product_summary', 'greenpath_core_woo_single_product_big_gallery_add_item_content_holder_end', 14 );

		// Add product tabs in new location
		add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 8 );

		// Add cart showcase
		add_action( 'woocommerce_after_single_product_summary', 'greenpath_core_single_cart_showcase_wrapper', 9 );
		add_action( 'woocommerce_after_single_product_summary', 'greenpath_core_single_display_cart_showcase', 10 );
		add_action( 'woocommerce_after_single_product_summary', 'greenpath_core_single_cart_showcase_wrapper_end', 11 );

		// Move related products to new holder
		add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 12 );

		//wrapper start for sticky columns
		add_action( 'woocommerce_single_product_summary', 'greenpath_core_woo_sticky_start', 1 );

		//wrapper end for sticky columns
		add_action( 'woocommerce_single_product_summary', 'greenpath_core_woo_sticky_end', 70 );
	}

	add_action( 'greenpath_core_action_load_template_hooks_big_gallery', 'greenpath_core_load_single_woo_templates_big_gallery' );
}

if ( ! function_exists( 'greenpath_core_woo_single_product_big_gallery_add_images' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 */
	function greenpath_core_woo_single_product_big_gallery_add_images() {
		greenpath_core_template_part( 'plugins/woocommerce/single', 'templates/images-holder' );
	}
}

if ( ! function_exists( 'greenpath_core_woo_single_product_big_gallery_add_item_content_holder' ) ) {
	/**
	 * Function that render additional content around product info on main shop page
	 */
	function greenpath_core_woo_single_product_big_gallery_add_item_content_holder() {
		echo '<div class="qodef-woo-single-content">';
	}
}

if ( ! function_exists( 'greenpath_core_woo_single_product_big_gallery_add_item_content_holder_end' ) ) {
	/**
	 * Function that render additional content around product info on main shop page
	 */
	function greenpath_core_woo_single_product_big_gallery_add_item_content_holder_end() {

		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_core_woo_single_product_full_width_class' ) ) {

	function greenpath_core_woo_single_product_full_width_class( $classes ) {

		$classes = 'qodef-content-full-width';

		return $classes;
	}
}

if ( ! function_exists( 'greenpath_core_woo_single_product_images_large_thumb_size' ) ) {

	function greenpath_core_woo_single_product_images_large_thumb_size( $thumb_size ) {

		$thumb_size = 'single';

		return $thumb_size;
	}
}
