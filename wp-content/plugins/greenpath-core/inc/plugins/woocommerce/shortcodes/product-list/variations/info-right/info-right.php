<?php

if ( ! function_exists( 'greenpath_core_add_product_list_variation_info_right' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function greenpath_core_add_product_list_variation_info_right( $variations ) {
		$variations['info-right'] = esc_html__( 'Info Right', 'greenpath-core' );

		return $variations;
	}

	add_filter( 'greenpath_core_filter_product_list_layouts', 'greenpath_core_add_product_list_variation_info_right' );
}

if ( ! function_exists( 'greenpath_core_register_shop_list_info_right_actions' ) ) {
	/**
	 * Function that override product item layout for current variation type
	 */
	function greenpath_core_register_shop_list_info_right_actions() {

		// IMPORTANT - THIS CODE NEED TO COPY/PASTE ALSO INTO THEME FOLDER MAIN WOOCOMMERCE FILE - set_default_layout method

		// Add additional tags around product list item
		add_action( 'woocommerce_before_shop_loop_item', 'greenpath_add_product_list_item_holder', 5 ); // permission 5 is set because woocommerce_template_loop_product_link_open hook is added on 10
		add_action( 'woocommerce_after_shop_loop_item', 'greenpath_add_product_list_item_holder_end', 30 ); // permission 30 is set because woocommerce_template_loop_add_to_cart hook is added on 10

		// Add additional tags around product list item image
		add_action( 'woocommerce_before_shop_loop_item_title', 'greenpath_add_product_list_item_media_holder', 5 ); // permission 5 is set because woocommerce_show_product_loop_sale_flash hook is added on 10
		add_action( 'woocommerce_before_shop_loop_item_title', 'greenpath_add_product_list_item_media_holder_end', 20 ); // permission 30 is set because woocommerce_template_loop_product_thumbnail hook is added on 10

		// Add additional tags around product list item image
		add_action( 'woocommerce_before_shop_loop_item_title', 'greenpath_add_product_list_item_media_image_holder', 6 ); // permission 5 is set because woocommerce_show_product_loop_sale_flash hook is added on 10
		add_action( 'woocommerce_before_shop_loop_item_title', 'greenpath_add_product_list_item_media_image_holder_end', 14 ); // permission 30 is set because woocommerce_template_loop_product_thumbnail hook is added on 10

		// Add link at the end of woocommerce_before_shop_loop_item_title
		add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 17 ); // permission 28 is set because greenpath_add_product_list_item_media_holder_end is 30
		add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 18 ); // permission 29 is set because greenpath_add_product_list_item_media_holder_end is 30

		// Add additional tags around product list item content
		add_action( 'woocommerce_shop_loop_item_title', 'greenpath_add_product_list_item_content_holder', 5 ); // permission 5 is set because woocommerce_template_loop_product_title hook is added on 10
		add_action( 'woocommerce_after_shop_loop_item', 'greenpath_add_product_list_item_content_holder_end', 20 ); // permission 30 is set because woocommerce_template_loop_add_to_cart hook is added on 10

		// Add additional tags around categories
		add_action( 'woocommerce_after_shop_loop_item_title', 'greenpath_add_product_list_item_price_holder', 8 ); // permission 5 is set because woocommerce_template_loop_product_title hook is added on 10
		add_action( 'woocommerce_after_shop_loop_item_title', 'greenpath_add_price_per_unit_on_product', 11 ); // permission 5 is set because woocommerce_template_loop_product_title hook is added on 10
		add_action( 'woocommerce_after_shop_loop_item_title', 'greenpath_add_product_list_item_price_holder_end', 12 ); // permission 30 is set because woocommerce_template_loop_add_to_cart hook is added on 10

		// Change add to cart position on product list
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 ); // permission 10 is default
	}

	add_action( 'greenpath_core_action_shop_list_item_layout_info-right', 'greenpath_core_register_shop_list_info_right_actions' );
}
