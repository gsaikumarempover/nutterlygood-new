<?php

if ( ! function_exists( 'greenpath_core_add_product_category_options' ) ) {
	/**
	 * Function that add global taxonomy options for current module
	 */
	function greenpath_core_add_product_category_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope' => array( 'product_cat' ),
				'type'  => 'taxonomy',
				'slug'  => 'product_cat',
			)
		);

		if ( $page ) {
			$page->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_product_category_masonry_size',
					'title'       => esc_html__( 'Image Size', 'greenpath-core' ),
					'description' => esc_html__( 'Choose image size for list shortcode item if masonry layout > fixed image size is selected in product category list shortcode', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'masonry_image_dimension' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type'  => 'textarea',
					'name'        => 'qodef_product_category_alternate_svg',
					'title'       => esc_html__( 'Alternate SVG', 'greenpath-core' ),
					'description' => esc_html__( 'Add a SVG to be used for the Product Category List shortcode', 'greenpath-core' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type'  => 'color',
					'name'        => 'qodef_product_category_svg_bg',
					'title'       => esc_html__( 'SVG Background Color', 'greenpath-core' ),
				)
			);
		}
	}

	add_action( 'greenpath_core_action_register_cpt_tax_fields', 'greenpath_core_add_product_category_options' );
}
