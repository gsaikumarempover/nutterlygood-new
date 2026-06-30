<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_add_wishlist_page_template_options' ) ) {
	/**
	 * Function that add new options for this module
	 */
	function qode_wishlist_for_woocommerce_add_wishlist_page_template_options() {
		$qode_framework = qode_wishlist_for_woocommerce_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => QODE_WISHLIST_FOR_WOOCOMMERCE_OPTIONS_NAME,
				'type'        => 'admin',
				'layout'      => 'tabbed',
				'slug'        => 'wishlist-page',
				'title'       => esc_html__( 'Wishlist Page', 'qode-wishlist-for-woocommerce' ),
				'description' => esc_html__( 'Wishlist Page Options', 'qode-wishlist-for-woocommerce' ),
			)
		);

		if ( $page ) {
			$get_page_id = get_option( QODE_WISHLIST_FOR_WOOCOMMERCE_PAGE_TEMPLATE );

			$general_tab = $page->add_tab_element(
				array(
					'name'  => 'qode_wishlist_for_woocommerce_wishlist_page_general_tab',
					'title' => esc_html__( 'General', 'qode-wishlist-for-woocommerce' ),
				)
			);

			$general_section = $general_tab->add_section_element(
				array(
					'name' => 'qode_wishlist_for_woocommerce_wishlist_page_general_section',
				)
			);

			$general_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qode_wishlist_for_woocommerce_page_template',
					'title'         => esc_html__( 'Wishlist Page', 'qode-wishlist-for-woocommerce' ),
					'description'   => __( 'Select a main Wishlist page. Make sure you add the <code>[qode_wishlist_for_woocommerce_table]</code> shortcode to that page\'s content', 'qode-wishlist-for-woocommerce' ),
					'options'       => qode_wishlist_for_woocommerce_get_pages( true ),
					'default_value' => ! empty( $get_page_id ) ? $get_page_id : '',
				)
			);

			// Hook to include additional options after specific module options.
			do_action( 'qode_wishlist_for_woocommerce_action_after_wishlist_page_general_options_map', $page, $general_section );

			$wishlist_page_section = $general_tab->add_section_element(
				array(
					'name'  => 'qode_wishlist_for_woocommerce_wishlist_page_section',
					'title' => esc_html__( 'Single Wishlist Page Layout', 'qode-wishlist-for-woocommerce' ),
				)
			);

			// Hook to include additional options before specific module options.
			do_action( 'qode_wishlist_for_woocommerce_action_before_wishlist_page_options_map', $page, $wishlist_page_section );

			$wishlist_page_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_wishlist_for_woocommerce_show_table_title',
					'title'         => esc_html__( 'Show Wishlist Page Title', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Display title on the Wishlist page', 'qode-wishlist-for-woocommerce' ),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_show_table_title_default_value', 'yes' ),
				)
			);

			$wishlist_page_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qode_wishlist_for_woocommerce_table_title_tag',
					'title'         => esc_html__( 'Wishlist Page Title Tag', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Set a heading tag for Wishlist page title text', 'qode-wishlist-for-woocommerce' ),
					'options'       => qode_wishlist_for_woocommerce_get_select_type_options_pool( 'title_tag' ),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_table_title_tag_default_value', 'h3' ),
					'dependency'    => array(
						'show' => array(
							'qode_wishlist_for_woocommerce_show_table_title' => array(
								'values'        => 'yes',
								'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_show_table_title_default_value', 'yes' ),
							),
						),
					),
				)
			);

			$wishlist_page_section->add_field_element(
				array(
					'field_type'    => 'checkbox',
					'name'          => 'qode_wishlist_for_woocommerce_table_items',
					'title'         => esc_html__( 'Show Product Features', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Select product features you wish to show on the Wishlist page', 'qode-wishlist-for-woocommerce' ),
					'options'       => apply_filters(
						'qode_wishlist_for_woocommerce_filter_available_table_items',
						array(
							'price'        => esc_html__( 'Product Price', 'qode-wishlist-for-woocommerce' ),
							'variations'   => esc_html__( 'Product Variations (selected by user)', 'qode-wishlist-for-woocommerce' ),
							'date-added'   => esc_html__( 'Added Wishlist Item Date', 'qode-wishlist-for-woocommerce' ),
							'stock-status' => esc_html__( 'Product Stock Status', 'qode-wishlist-for-woocommerce' ),
							'category'     => esc_html__( 'Product Category', 'qode-wishlist-for-woocommerce' ),
							'add-to-cart'  => esc_html__( 'Add to Cart Button', 'qode-wishlist-for-woocommerce' ),
							'remove'       => esc_html__( 'Remove Item Icon', 'qode-wishlist-for-woocommerce' ),
						)
					),
					'default_value' => apply_filters(
						'qode_wishlist_for_woocommerce_filter_available_table_items_default_values',
						array(
							'price',
							'stock-status',
							'category',
							'add-to-cart',
							'remove',
						)
					),
				)
			);

			// Hook to include additional options after specific module options.
			do_action( 'qode_wishlist_for_woocommerce_action_after_wishlist_page_options_map', $page, $wishlist_page_section );

			$wishlist_page_features_section = $general_tab->add_section_element(
				array(
					'name'  => 'qode_wishlist_for_woocommerce_wishlist_page_features_section',
					'title' => esc_html__( 'Wishlist Page Features', 'qode-wishlist-for-woocommerce' ),
				)
			);

			$wishlist_page_features_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_wishlist_for_woocommerce_table_items_redirect_to_cart',
					'title'         => esc_html__( 'Redirect to Cart', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Redirect users to Cart page after adding a product to Cart from the Wishlist page', 'qode-wishlist-for-woocommerce' ),
					'default_value' => 'no',
				)
			);

			$wishlist_page_features_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_wishlist_for_woocommerce_table_items_remove_from_wishlist',
					'title'         => esc_html__( 'Remove If Added to Cart', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Remove the product from the Wishlist after it has been added to Cart', 'qode-wishlist-for-woocommerce' ),
					'default_value' => 'no',
				)
			);

			// Hook to include additional options after specific module options.
			do_action( 'qode_wishlist_for_woocommerce_action_after_wishlist_page_features_options_map', $page, $wishlist_page_features_section );

			$wishlist_page_styles_tab = $page->add_tab_element(
				array(
					'name'  => 'qode_wishlist_for_woocommerce_wishlist_table_wishlist_page_styles_tab',
					'title' => esc_html__( 'Styles', 'qode-wishlist-for-woocommerce' ),
				)
			);

			// Hook to include additional options after specific module options.
			do_action( 'qode_wishlist_for_woocommerce_action_after_wishlist_page_style_options_map', $page, $wishlist_page_styles_tab );

			// Hook to include additional options after module options.
			do_action( 'qode_wishlist_for_woocommerce_action_after_wishlist_page_options_map', $page );
		}
	}

	add_action( 'qode_wishlist_for_woocommerce_action_default_options_init', 'qode_wishlist_for_woocommerce_add_wishlist_page_template_options', 20 );
}
