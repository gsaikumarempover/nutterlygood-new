<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}

if ( ! function_exists( 'qode_compare_for_woocommerce_add_comparison_table_options' ) ) {
	/**
	 * Function that add button options for this module
	 */
	function qode_compare_for_woocommerce_add_comparison_table_options() {
		$qode_framework = qode_compare_for_woocommerce_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => QODE_COMPARE_FOR_WOOCOMMERCE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'comparison-table',
				'icon'        => 'fa fa-indent',
				'title'       => esc_html__( 'Comparison Table', 'qode-compare-for-woocommerce' ),
				'description' => esc_html__( 'Comparison Table Options', 'qode-compare-for-woocommerce' ),
				'layout'      => apply_filters( 'qode_compare_for_woocommerce_filter_comparison_table_options_page_layout', '' ),
			)
		);

		if ( $page ) {
			do_action( 'qode_compare_for_woocommerce_action_comparison_table_before_options_map', $page );

			$general_tab = $page->add_tab_element(
				array(
					'name'  => 'qode_compare_for_woocommerce_table_general_tab',
					'title' => esc_html__( 'General', 'qode-compare-for-woocommerce' ),
				)
			);

			$general_section = $general_tab->add_section_element(
				array(
					'name' => 'qode_compare_for_woocommerce_general_section',
				)
			);

			$general_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_compare_for_woocommerce_table_title',
					'title'         => esc_html__( 'Enable Table Title', 'qode-compare-for-woocommerce' ),
					'description'   => esc_html__( 'Display comparison table title', 'qode-compare-for-woocommerce' ),
					'default_value' => 'yes',
				)
			);

			$general_section->add_field_element(
				array(
					'field_type'    => 'text',
					'name'          => 'qode_compare_for_woocommerce_table_title_label',
					'title'         => esc_html__( 'Table Title Label', 'qode-compare-for-woocommerce' ),
					'default_value' => apply_filters( 'qode_compare_for_woocommerce_filter_default_table_title_label', esc_html__( 'Compare selected items', 'qode-compare-for-woocommerce' ) ),
					'dependency'    => array(
						'show' => array(
							'qode_compare_for_woocommerce_table_title' => array(
								'values'        => 'yes',
								'default_value' => 'yes',
							),
						),
					),
				)
			);

			// Hook to include additional options after compare table title options.
			do_action( 'qode_compare_for_woocommerce_action_after_compare_table_title_options_map', $page, $general_section );

			$general_section->add_field_element(
				array(
					'field_type'    => 'checkbox',
					'name'          => 'qode_compare_for_woocommerce_table_fields',
					'title'         => esc_html__( 'Attributes to Show', 'qode-compare-for-woocommerce' ),
					'description'   => esc_html__( 'Select attributes to be displayed in the comparison table. You can order the items using drag and drop', 'qode-compare-for-woocommerce' ),
					'options'       => qode_compare_for_woocommerce_get_comparison_table_fields(),
					'default_value' => apply_filters( 'qode_compare_for_woocommerce_filter_attribute_fields_default_value', array_keys( qode_compare_for_woocommerce_get_comparison_table_fields() ) ),
				)
			);

			$general_section->add_field_element(
				array(
					'field_type' => 'hidden',
					'name'       => 'qode_compare_for_woocommerce_table_fields_positions',
					'title'      => esc_html__( 'Table Fields positions', 'qode-compare-for-woocommerce' ),
					'args'       => array(
						'readonly' => true,
					),
				)
			);

			// Hook to include additional options after compare table product fields options.
			do_action( 'qode_compare_for_woocommerce_action_after_compare_table_product_fields_options_map', $page, $general_section );

			$general_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_compare_for_woocommerce_repeat_price',
					'title'         => esc_html__( 'Repeat Price Field', 'qode-compare-for-woocommerce' ),
					'description'   => esc_html__( 'Display an additional "Price" field at the end of the table', 'qode-compare-for-woocommerce' ),
					'default_value' => 'no',
				)
			);

			$general_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_compare_for_woocommerce_repeat_add_to_cart',
					'title'         => esc_html__( 'Repeat Add to Cart Field', 'qode-compare-for-woocommerce' ),
					'description'   => esc_html__( 'Display an additional "Add to Cart" field at the end of the table', 'qode-compare-for-woocommerce' ),
					'default_value' => 'no',
				)
			);

			$general_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_compare_for_woocommerce_removal_confirmation',
					'title'         => esc_html__( 'Removal Confirmation Prompt', 'qode-compare-for-woocommerce' ),
					'description'   => esc_html__( 'Enable to show a confirmation dialog before removing an item', 'qode-compare-for-woocommerce' ),
					'default_value' => 'no',
				)
			);

			// Hook to include additional options after "Repeat Add to Cart Field" option.
			do_action( 'qode_compare_for_woocommerce_action_after_repeat_add_to_cart_field_options_map', $page, $general_section );

			$image_size_section = $general_tab->add_section_element(
				array(
					'name'  => 'qode_compare_for_woocommerce_image_size_section',
					'title' => esc_html__( 'Product Image Size', 'qode-compare-for-woocommerce' ),
				)
			);

			$image_size_section->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_compare_for_woocommerce_image_width',
					'title'      => esc_html__( 'Image Width', 'qode-compare-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
						'suffix'    => 'px',
					),
				)
			);

			$image_size_section->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_compare_for_woocommerce_image_height',
					'title'      => esc_html__( 'Image Height', 'qode-compare-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
						'suffix'    => 'px',
					),
				)
			);

			$image_size_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_compare_for_woocommerce_image_hard_crop',
					'title'         => esc_html__( 'Hard Crop Image', 'qode-compare-for-woocommerce' ),
					'description'   => esc_html__( 'Crop thumbnail to exact dimensions (normally thumbnails are proportional)', 'qode-compare-for-woocommerce' ),
					'default_value' => 'yes',
				)
			);

			// Hook to include additional options after module options.
			do_action( 'qode_compare_for_woocommerce_action_after_compare_table_options_map', $page, $general_tab );
		}
	}
	add_action( 'qode_compare_for_woocommerce_action_core_options_init', 'qode_compare_for_woocommerce_add_comparison_table_options', 20 );
}
