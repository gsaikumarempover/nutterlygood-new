<?php

if ( ! function_exists( 'greenpath_core_add_woo_product_single_variation_big_images' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_woo_product_single_variation_big_images( $variations ) {
		$variations['big-images'] = esc_html__( 'Big Images', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_woo_single_product_layouts', 'greenpath_core_add_woo_product_single_variation_big_images' );
}

if ( ! function_exists( 'greenpath_core_load_single_woo_templates_big_images' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 */
	function greenpath_core_load_single_woo_templates_big_images() {

		// Remove default product single page content
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
		remove_action( 'woocommerce_before_single_product_summary', 'greenpath_core_get_info_panel_section', 28 );
		remove_filter( 'woocommerce_product_tabs', 'woocommerce_default_product_tabs' );

		// Remove slider template added by media option
		//remove_action( 'woocommerce_before_single_product_summary', 'greenpath_woo_product_render_slider_html', 10 );

		// Add big images gallery template to product single page
		add_action( 'woocommerce_before_single_product_summary', 'greenpath_core_woo_single_product_big_images_add_images', 20 );

		// Add thumbnail images to big images gallery
		add_action( 'greenpath_core_action_woo_single_product_gallery_images', 'woocommerce_show_product_thumbnails' );

		// Add product content in entry-summary area
		add_action( 'woocommerce_single_product_summary', 'greenpath_core_woo_single_product_big_images_add_item_content_holder', 54 );
		add_action( 'woocommerce_single_product_summary', 'greenpath_core_woo_single_product_big_images_content_position', 55 );
		add_action( 'woocommerce_single_product_summary', 'greenpath_core_woo_single_product_big_images_add_item_content_holder_end', 56 );

		// Add additional information tab content in entry-summary area
		add_action( 'woocommerce_single_product_summary', 'greenpath_core_woo_single_product_big_images_add_additional_information_holder', 58 );
		add_action( 'woocommerce_single_product_summary', 'greenpath_core_woo_single_product_big_images_additional_information_position', 59 );
		add_action( 'woocommerce_single_product_summary', 'greenpath_core_woo_single_product_big_images_add_additional_information_holder_end', 60 );

		// Add reviews tab content after product single summary
		add_action( 'woocommerce_after_single_product_summary', 'greenpath_core_woo_single_product_big_images_reviews_position', 18 );

		// Set thumbnail image size for gallery
		add_filter( 'woocommerce_gallery_image_size', 'greenpath_core_woo_single_product_images_large_thumb_size' );

		// Add custom details for product single page
		add_action( 'woocommerce_single_product_summary', 'greenpath_core_get_info_panel_section', 61 );
	}

	add_action( 'greenpath_core_action_load_template_hooks_big_images', 'greenpath_core_load_single_woo_templates_big_images' );
}

if ( ! function_exists( 'greenpath_core_woo_single_product_big_images_add_images' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 */
	function greenpath_core_woo_single_product_big_images_add_images() {
		greenpath_core_template_part( 'plugins/woocommerce/single', 'templates/images-holder' );
	}
}

if ( ! function_exists( 'greenpath_core_woo_single_product_big_images_add_item_content_holder' ) ) {
	/**
	 * Function that render additional content around product info on main shop page
	 */
	function greenpath_core_woo_single_product_big_images_add_item_content_holder() {
		echo '<div class="qodef-woo-single-content">';
	}
}

if ( ! function_exists( 'greenpath_core_woo_single_product_big_images_add_item_content_holder_end' ) ) {
	/**
	 * Function that render additional content around product info on main shop page
	 */
	function greenpath_core_woo_single_product_big_images_add_item_content_holder_end() {

		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_core_woo_single_product_big_images_content_position' ) ) {
	/**
	 * Function that outputs the single product content
	 */
	function greenpath_core_woo_single_product_big_images_content_position() {
		the_content();
	}
}

if ( ! function_exists( 'greenpath_core_woo_single_product_big_images_add_additional_information_holder' ) ) {
	/**
	 * Function that render additional content around product info on main shop page
	 */
	function greenpath_core_woo_single_product_big_images_add_additional_information_holder() {
		echo '<h5 class="qodef-woo-single-additional-information-title">' . apply_filters( 'woocommerce_product_additional_information_heading', esc_html__( 'Additional info', 'greenpath-core' ) ) . '</h5>';
		echo '<div class="qodef-woo-single-additional-information">';
	}
}

if ( ! function_exists( 'greenpath_core_woo_single_product_big_images_add_additional_information_holder_end' ) ) {
	/**
	 * Function that render additional content around product info on main shop page
	 */
	function greenpath_core_woo_single_product_big_images_add_additional_information_holder_end() {

		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_core_woo_single_product_big_images_additional_information_position' ) ) {
	/**
	 * Function that outputs the additional information
	 */
	function greenpath_core_woo_single_product_big_images_additional_information_position() {
		do_action( 'woocommerce_product_additional_information', wc_get_product( get_the_ID() ) );
	}
}

if ( ! function_exists( 'greenpath_core_woo_single_product_big_images_reviews_position' ) ) {
	/**
	 * Function that outputs the review section
	 */
	function greenpath_core_woo_single_product_big_images_reviews_position() {
		comments_template();
	}
}

if ( ! function_exists( 'greenpath_core_woo_single_product_images_large_thumb_size' ) ) {

	function greenpath_core_woo_single_product_images_large_thumb_size( $thumb_size ) {

		$thumb_size = 'single';

		return $thumb_size;
	}
}
