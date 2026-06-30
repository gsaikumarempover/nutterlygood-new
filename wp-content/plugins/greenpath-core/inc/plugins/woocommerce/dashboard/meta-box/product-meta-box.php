<?php

if ( ! function_exists( 'greenpath_core_add_product_single_meta_box' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function greenpath_core_add_product_single_meta_box( $page ) {

		if ( $page ) {

			$list_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-list',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'List Settings', 'greenpath-core' ),
					'description' => esc_html__( 'Product List settings', 'greenpath-core' ),
				)
			);

			$list_tab->add_field_element(
				array(
					'field_type'  => 'image',
					'name'        => 'qodef_product_list_image',
					'title'       => esc_html__( 'Product List Image', 'greenpath-core' ),
					'description' => esc_html__( 'Upload image to be displayed on product list instead of featured image', 'greenpath-core' ),
				)
			);

			$list_tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_masonry_image_dimension_product',
					'title'       => esc_html__( 'Image Dimension', 'greenpath-core' ),
					'description' => esc_html__( 'Choose an image layout for product list. If you are using fixed image proportions on the list, choose an option other than default', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'masonry_image_dimension' ),
				)
			);

			$list_tab->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_product_short_description',
					'title'       => esc_html__( 'Short Description', 'greenpath-core' ),
					'description' => esc_html__( 'This desription will appear on the product list horizontal layout.', 'greenpath-core' ),
				)
			);

			$list_tab->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_show_new_sign',
					'title'         => esc_html__( 'Show New Sign', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will show "New Sign" mark on product.', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes' ),
					'default_value' => 'no',
				)
			);

			$list_tab->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_show_price_per_unit',
					'title'         => esc_html__( 'Show Price Per Unit', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will show price per unit on the product.', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes' ),
					'default_value' => 'no',
				)
			);
		}
	}

	add_action( 'greenpath_core_action_after_product_single_meta_box_map', 'greenpath_core_add_product_single_meta_box' );
}
