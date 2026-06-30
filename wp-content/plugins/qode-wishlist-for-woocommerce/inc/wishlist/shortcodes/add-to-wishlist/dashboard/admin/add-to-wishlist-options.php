<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_add_wishlist_button_options' ) ) {
	/**
	 * Function that add new options for this module
	 */
	function qode_wishlist_for_woocommerce_add_wishlist_button_options() {
		$qode_framework = qode_wishlist_for_woocommerce_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => QODE_WISHLIST_FOR_WOOCOMMERCE_OPTIONS_NAME,
				'type'        => 'admin',
				'layout'      => 'tabbed',
				'slug'        => 'button',
				'title'       => esc_html__( 'Add to Wishlist', 'qode-wishlist-for-woocommerce' ),
				'description' => esc_html__( 'Add to Wishlist Options', 'qode-wishlist-for-woocommerce' ),
			)
		);

		if ( $page ) {

			$general_tab = $page->add_tab_element(
				array(
					'name'  => 'qode_wishlist_for_woocommerce_button_general_tab',
					'title' => esc_html__( 'General', 'qode-wishlist-for-woocommerce' ),
				)
			);

			$general_section = $general_tab->add_section_element(
				array(
					'name' => 'qode_wishlist_for_woocommerce_button_general_section',
				)
			);

			$general_section->add_field_element(
				array(
					'field_type'    => 'radio',
					'name'          => 'qode_wishlist_for_woocommerce_add_to_wishlist_behavior',
					'title'         => esc_html__( 'Behavior for Added Products', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Choose a behavior type for the "Add to Wishlist" button when the product has already been added to a Wishlist', 'qode-wishlist-for-woocommerce' ),
					'options'       => array(
						'add'    => esc_html__( 'Show "Add to Wishlist" button', 'qode-wishlist-for-woocommerce' ),
						'view'   => esc_html__( 'Show "Browse Wishlist" link', 'qode-wishlist-for-woocommerce' ),
						'remove' => esc_html__( 'Show "Remove from List" link', 'qode-wishlist-for-woocommerce' ),
					),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_behavior_default_value', 'add' ),
				)
			);

			// Hook to include additional options after specific module options.
			do_action( 'qode_wishlist_for_woocommerce_action_after_add_to_wishlist_general_options_map', $page, $general_section );

			$product_loop_section = $general_tab->add_section_element(
				array(
					'name'        => 'qode_wishlist_for_woocommerce_button_product_loop_section',
					'title'       => esc_html__( 'Product Loop Options', 'qode-wishlist-for-woocommerce' ),
					'description' => esc_html__( ' Loops represent product lists shown on shop pages, archive pages and all other places where the product lists are displayed', 'qode-wishlist-for-woocommerce' ),
				)
			);

			$product_loop_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_wishlist_for_woocommerce_show_button_in_loop',
					'title'         => esc_html__( 'Show "Add to Wishlist" in Loop', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Display the "Add to Wishlist" button inside WooCommerce loops', 'qode-wishlist-for-woocommerce' ),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_show_button_in_loop_default_value', 'yes' ),
				)
			);

			$product_loop_section_inner = $product_loop_section->add_section_element(
				array(
					'name'       => 'qode_wishlist_for_woocommerce_button_product_loop_section_inner',
					'dependency' => array(
						'show' => array(
							'qode_wishlist_for_woocommerce_show_button_in_loop' => array(
								'values'        => 'yes',
								'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_show_button_in_loop_default_value', 'yes' ),
							),
						),
					),
				)
			);

			$product_loop_section_inner->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qode_wishlist_for_woocommerce_add_to_wishlist_loop_type',
					'title'         => esc_html__( '"Add to Wishlist" Loop Type', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Choose the type of the "Add to Wishlist" button inside loops', 'qode-wishlist-for-woocommerce' ),
					'options'       => array(
						''                  => esc_html__( 'Default', 'qode-wishlist-for-woocommerce' ),
						'icon-with-text'    => esc_html__( 'Icon with Text', 'qode-wishlist-for-woocommerce' ),
						'icon'              => esc_html__( 'Only Icon', 'qode-wishlist-for-woocommerce' ),
						'icon-with-tooltip' => esc_html__( 'Icon with Tooltip', 'qode-wishlist-for-woocommerce' ),
						'text'              => esc_html__( 'Only Text', 'qode-wishlist-for-woocommerce' ),
					),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_loop_type_default_value', '' ),
				)
			);

			$product_loop_section_inner->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qode_wishlist_for_woocommerce_button_loop_position',
					'title'         => esc_html__( '"Add to Wishlist" Loop Position', 'qode-wishlist-for-woocommerce' ),
					'description'   => __( 'Choose a button position inside loops. If you select the "Use shortcode" position, copy this shortcode <code>[qode_wishlist_for_woocommerce_add_to_wishlist]</code> and paste it where you wish to place the button', 'qode-wishlist-for-woocommerce' ),
					'options'       => array(
						'after-add-to-cart'  => esc_html__( 'After "Add to cart"', 'qode-wishlist-for-woocommerce' ),
						'before-add-to-cart' => esc_html__( 'Before "Add to cart"', 'qode-wishlist-for-woocommerce' ),
						'on-thumbnail'       => esc_html__( 'On Thumbnail', 'qode-wishlist-for-woocommerce' ),
						'above-thumbnail'    => esc_html__( 'Above Thumbnail', 'qode-wishlist-for-woocommerce' ),
						'shortcode'          => esc_html__( 'Use shortcode', 'qode-wishlist-for-woocommerce' ),
					),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_button_loop_position_default_value', 'after-add-to-cart' ),
				)
			);

			$product_loop_section_inner->add_field_element(
				array(
					'field_type'    => 'radio',
					'name'          => 'qode_wishlist_for_woocommerce_add_to_wishlist_thumbnail_loop_position',
					'title'         => esc_html__( '"Add to Wishlist" Thumbnail Position', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Choose the position for the "Add to Wishlist" button in loops when placed over the thumbnail image', 'qode-wishlist-for-woocommerce' ),
					'options'       => array(
						'top-left'  => esc_html__( 'Top Left', 'qode-wishlist-for-woocommerce' ),
						'top-right' => esc_html__( 'Top Right', 'qode-wishlist-for-woocommerce' ),
					),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_thumbnail_loop_position_default_value', 'top-left' ),
					'dependency'    => array(
						'show' => array(
							'qode_wishlist_for_woocommerce_button_loop_position' => array(
								'values'        => 'on-thumbnail',
								'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_button_loop_position_default_value', 'after-add-to-cart' ),
							),
						),
					),
				)
			);

			$button_loop_position_style_row = $product_loop_section_inner->add_row_element(
				array(
					'name'        => 'qode_wishlist_for_woocommerce_button_loop_position_style_row',
					'title'       => esc_html__( '"Add to Wishlist" Thumbnail Position Offset', 'qode-wishlist-for-woocommerce' ),
					'description' => esc_html__( 'Adjust the "Add to Wishlist" button offset inside thumbnail. Valid measure units are px, %, em, rem, vh, vw & calc', 'qode-wishlist-for-woocommerce' ),
					'dependency'  => array(
						'show' => array(
							'qode_wishlist_for_woocommerce_button_loop_position' => array(
								'values'        => 'on-thumbnail',
								'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_button_loop_position_default_value', 'after-add-to-cart' ),
							),
						),
					),
				)
			);

			$button_loop_position_style_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_loop_thumb_top_offset',
					'title'      => esc_html__( 'Top Offset', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_loop_position_style_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_loop_thumb_side_offset',
					'title'      => esc_html__( 'Side Offset', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			// Hook to include additional options after specific module options.
			do_action( 'qode_wishlist_for_woocommerce_action_after_add_to_wishlist_product_loop_options_map', $general_tab, $product_loop_section_inner );

			$product_page_section = $general_tab->add_section_element(
				array(
					'name'  => 'qode_wishlist_for_woocommerce_button_product_page_section',
					'title' => esc_html__( 'Product Page Options', 'qode-wishlist-for-woocommerce' ),
				)
			);

			$product_page_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_wishlist_for_woocommerce_show_button_on_product_pages',
					'title'         => esc_html__( 'Show "Add to Wishlist" on Product Pages', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Display the "Add to Wishlist" button on product pages', 'qode-wishlist-for-woocommerce' ),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_show_button_on_product_pages_default_value', 'yes' ),
				)
			);

			$product_page_section_inner = $product_page_section->add_section_element(
				array(
					'name'       => 'qode_wishlist_for_woocommerce_button_product_loop_section_inner',
					'dependency' => array(
						'show' => array(
							'qode_wishlist_for_woocommerce_show_button_on_product_pages' => array(
								'values'        => 'yes',
								'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_show_button_on_product_pages_default_value', 'yes' ),
							),
						),
					),
				)
			);

			$product_page_section_inner->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qode_wishlist_for_woocommerce_add_to_wishlist_type',
					'title'         => esc_html__( '"Add to Wishlist" Type', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Choose the type of the "Add to Wishlist" button on product pages', 'qode-wishlist-for-woocommerce' ),
					'options'       => array(
						''                  => esc_html__( 'Default', 'qode-wishlist-for-woocommerce' ),
						'icon-with-text'    => esc_html__( 'Icon with Text', 'qode-wishlist-for-woocommerce' ),
						'icon'              => esc_html__( 'Only Icon', 'qode-wishlist-for-woocommerce' ),
						'icon-with-tooltip' => esc_html__( 'Icon with Tooltip', 'qode-wishlist-for-woocommerce' ),
						'text'              => esc_html__( 'Only Text', 'qode-wishlist-for-woocommerce' ),
					),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_type_default_value', 'icon-with-text' ),
				)
			);

			$product_page_section_inner->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qode_wishlist_for_woocommerce_button_single_position',
					'title'         => esc_html__( '"Add to Wishlist" Product Page Position', 'qode-wishlist-for-woocommerce' ),
					'description'   => __( 'Choose a button position on product pages. If you select the "Use shortcode" position, copy this shortcode <code>[qode_wishlist_for_woocommerce_add_to_wishlist]</code> and paste it where you wish to place the button', 'qode-wishlist-for-woocommerce' ),
					'options'       => array(
						'after-add-to-cart'      => esc_html__( 'After "Add to cart"', 'qode-wishlist-for-woocommerce' ),
						'before-add-to-cart'     => esc_html__( 'Before "Add to cart"', 'qode-wishlist-for-woocommerce' ),
						'after-add-to-cart-form' => esc_html__( 'After "Add to cart" form', 'qode-wishlist-for-woocommerce' ),
						'on-thumbnail'           => esc_html__( 'On Thumbnail', 'qode-wishlist-for-woocommerce' ),
						'after-summary'          => esc_html__( 'After Summary', 'qode-wishlist-for-woocommerce' ),
						'before-summary'         => esc_html__( 'Before Summary', 'qode-wishlist-for-woocommerce' ),
						'before-title'           => esc_html__( 'Before Title', 'qode-wishlist-for-woocommerce' ),
						'shortcode'              => esc_html__( 'Use shortcode', 'qode-wishlist-for-woocommerce' ),
					),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_button_single_position_default_value', 'after-add-to-cart' ),
				)
			);

			$product_page_section_inner->add_field_element(
				array(
					'field_type'    => 'radio',
					'name'          => 'qode_wishlist_for_woocommerce_add_to_wishlist_thumbnail_single_position',
					'title'         => esc_html__( '"Add to Wishlist" Thumbnail Position', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Choose the position for the "Add to Wishlist" button on product pages when placed on the thumbnail image', 'qode-wishlist-for-woocommerce' ),
					'options'       => array(
						'top-left'     => esc_html__( 'Top Left', 'qode-wishlist-for-woocommerce' ),
						'top-right'    => esc_html__( 'Top Right', 'qode-wishlist-for-woocommerce' ),
						'bottom-left'  => esc_html__( 'Bottom Left', 'qode-wishlist-for-woocommerce' ),
						'bottom-right' => esc_html__( 'Bottom Right', 'qode-wishlist-for-woocommerce' ),
					),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_thumbnail_single_position_default_value', 'top-left' ),
					'dependency'    => array(
						'show' => array(
							'qode_wishlist_for_woocommerce_button_single_position' => array(
								'values'        => 'on-thumbnail',
								'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_button_single_position_default_value', 'after-add-to-cart' ),
							),
						),
					),
				)
			);

			$button_single_position_style_row = $product_page_section_inner->add_row_element(
				array(
					'name'        => 'qode_wishlist_for_woocommerce_button_single_position_style_row',
					'title'       => esc_html__( '"Add to Wishlist" Thumbnail Position Offset', 'qode-wishlist-for-woocommerce' ),
					'description' => esc_html__( 'Adjust the "Add to Wishlist" button offset inside thumbnail. Valid measure units are px, %, em, rem, vh, vw & calc', 'qode-wishlist-for-woocommerce' ),
					'dependency'  => array(
						'show' => array(
							'qode_wishlist_for_woocommerce_button_single_position' => array(
								'values'        => 'on-thumbnail',
								'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_button_single_position_default_value', 'after-add-to-cart' ),
							),
						),
					),
				)
			);

			$button_single_position_style_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_single_thumb_top_offset',
					'title'      => esc_html__( 'Top Offset', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_single_position_style_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_single_thumb_bottom_offset',
					'title'      => esc_html__( 'Bottom Offset', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_single_position_style_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_single_thumb_side_offset',
					'title'      => esc_html__( 'Side Offset', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			// Hook to include additional options after specific module options.
			do_action( 'qode_wishlist_for_woocommerce_action_after_add_to_wishlist_product_page_options_map', $general_tab, $product_page_section_inner );

			$button_styles_tab = $page->add_tab_element(
				array(
					'name'  => 'qode_wishlist_for_woocommerce_button_styles_tab',
					'title' => esc_html__( 'Styles', 'qode-wishlist-for-woocommerce' ),
				)
			);

			$button_icon_section = $button_styles_tab->add_section_element(
				array(
					'name'        => 'qode_wishlist_for_woocommerce_button_icon_section',
					'title'       => esc_html__( '"Add to Wishlist" Icon', 'qode-wishlist-for-woocommerce' ),
					'description' => esc_html__( 'Make general adjustments to the "Add to Wishlist" icon appearance', 'qode-wishlist-for-woocommerce' ),
				)
			);

			$button_icon_section->add_field_element(
				array(
					'field_type'    => 'radio',
					'name'          => 'qode_wishlist_for_woocommerce_add_to_wishlist_icon',
					'title'         => esc_html__( 'Wishlist Icon', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Choose an icon type for the "Add to Wishlist" button', 'qode-wishlist-for-woocommerce' ),
					'options'       => array(
						'heart'       => esc_html__( 'Heart', 'qode-wishlist-for-woocommerce' ),
						'custom-icon' => esc_html__( 'Custom Icon', 'qode-wishlist-for-woocommerce' ),
					),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_icon_default_value', 'heart' ),
				)
			);

			$button_icon_section->add_field_element(
				array(
					'field_type'    => 'image',
					'name'          => 'qode_wishlist_for_woocommerce_add_to_wishlist_custom_icon',
					'title'         => esc_html__( 'Wishlist Custom Icon', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Set a custom icon for your "Add to Wishlist" button', 'qode-wishlist-for-woocommerce' ),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_custom_icon_default_value', '' ),
					'dependency'    => array(
						'show' => array(
							'qode_wishlist_for_woocommerce_add_to_wishlist_icon' => array(
								'values'        => 'custom-icon',
								'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_icon_default_value', 'heart' ),
							),
						),
					),
				)
			);

			$button_icon_section->add_field_element(
				array(
					'field_type'    => 'radio',
					'name'          => 'qode_wishlist_for_woocommerce_added_to_wishlist_icon',
					'title'         => esc_html__( 'Wishlist Added Icon', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Choose an icon type for the "Add to Wishlist" button when product is already added to a Wishlist', 'qode-wishlist-for-woocommerce' ),
					'options'       => array(
						'heart-o'     => esc_html__( 'Heart O', 'qode-wishlist-for-woocommerce' ),
						'custom-icon' => esc_html__( 'Custom Icon', 'qode-wishlist-for-woocommerce' ),
					),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_added_to_wishlist_icon_default_value', 'heart-o' ),
				)
			);

			$button_icon_section->add_field_element(
				array(
					'field_type'    => 'image',
					'name'          => 'qode_wishlist_for_woocommerce_added_to_wishlist_custom_icon',
					'title'         => esc_html__( 'Wishlist Added Custom Icon', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Set a custom icon for your "Add to Wishlist" button when product is already added to a Wishlist', 'qode-wishlist-for-woocommerce' ),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_added_to_wishlist_custom_icon_default_value', '' ),
					'dependency'    => array(
						'show' => array(
							'qode_wishlist_for_woocommerce_added_to_wishlist_icon' => array(
								'values'        => 'custom-icon',
								'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_added_to_wishlist_icon_default_value', 'heart-o' ),
							),
						),
					),
				)
			);

			$button_icon_section_row = $button_icon_section->add_row_element(
				array(
					'name' => 'qode_wishlist_for_woocommerce_button_icon_section_row',
				)
			);

			$button_icon_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_size',
					'title'      => esc_html__( 'Icon Size', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 3,
						'placeholder' => '17',
						'suffix'      => 'px',
					),
				)
			);

			$button_icon_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_color',
					'title'      => esc_html__( 'Color', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 3,
						'placeholder' => '#000000',
					),
				)
			);

			$button_icon_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_hover_color',
					'title'      => esc_html__( 'Hover', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 3,
						'placeholder' => '#ec274f',
					),
				)
			);

			$button_icon_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_active_color',
					'title'      => esc_html__( 'Active', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 3,
						'placeholder' => '#ec274f',
					),
				)
			);

			$button_icon_svg_section = $button_styles_tab->add_section_element(
				array(
					'name'        => 'qode_wishlist_for_woocommerce_button_icon_svg_section',
					'title'       => esc_html__( '"Add to Wishlist" SVG Icon Style', 'qode-wishlist-for-woocommerce' ),
					'description' => esc_html__( 'Make general adjustments to the "Add to Wishlist" SVG icon appearance', 'qode-wishlist-for-woocommerce' ),
				)
			);

			$button_icon_svg_section_row = $button_icon_svg_section->add_row_element(
				array(
					'name' => 'qode_wishlist_for_woocommerce_button_icon_svg_section_row',
				)
			);

			$button_icon_svg_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_fill_color',
					'title'      => esc_html__( 'Fill', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_icon_svg_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_fill_hover_color',
					'title'      => esc_html__( 'Fill Hover', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_icon_svg_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_fill_active_color',
					'title'      => esc_html__( 'Fill Active', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_icon_svg_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_stroke_color',
					'title'      => esc_html__( 'Stroke', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_icon_svg_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_stroke_hover_color',
					'title'      => esc_html__( 'Stroke Hover', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_icon_svg_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_icon_stroke_active_color',
					'title'      => esc_html__( 'Stroke Active', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_section = $button_styles_tab->add_section_element(
				array(
					'name'        => 'qode_wishlist_for_woocommerce_button_section',
					'title'       => esc_html__( '"Add to Wishlist" Button', 'qode-wishlist-for-woocommerce' ),
					'description' => esc_html__( 'Make general adjustments to the "Add to Wishlist" button appearance. Valid measure units are px, %, em, rem, vh, vw & calc', 'qode-wishlist-for-woocommerce' ),
				)
			);

			$button_section_row = $button_section->add_row_element(
				array(
					'name' => 'qode_wishlist_for_woocommerce_button_section_row',
				)
			);

			$button_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_padding',
					'title'      => esc_html__( 'Padding', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_background_color',
					'title'      => esc_html__( 'Background', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_background_hover_color',
					'title'      => esc_html__( 'Background Hover', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_background_active_color',
					'title'      => esc_html__( 'Background Active', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_border_width',
					'title'      => esc_html__( 'Border Width', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_section_row->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_border_style',
					'title'      => esc_html__( 'Border Style', 'qode-wishlist-for-woocommerce' ),
					'options'    => qode_wishlist_for_woocommerce_get_select_type_options_pool( 'border_style' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_border_radius',
					'title'      => esc_html__( 'Border Radius', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_border_color',
					'title'      => esc_html__( 'Border', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_border_hover_color',
					'title'      => esc_html__( 'Border Hover', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_section_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_border_active_color',
					'title'      => esc_html__( 'Border Active', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$button_text_section = $button_styles_tab->add_section_element(
				array(
					'name'        => 'qode_wishlist_for_woocommerce_button_text_section',
					'title'       => esc_html__( 'Text Customization', 'qode-wishlist-for-woocommerce' ),
					'description' => esc_html__( 'Provide custom labels for Wishlist elements', 'qode-wishlist-for-woocommerce' ),
				)
			);

			$button_text_section_row = $button_text_section->add_row_element(
				array(
					'name' => 'qode_wishlist_for_woocommerce_button_text_section_row',
				)
			);

			$button_text_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_add_to_wishlist_label',
					'title'      => esc_html__( '"Add to Wishlist" Text', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 6,
						'placeholder' => esc_html__( 'Add to wishlist', 'qode-wishlist-for-woocommerce' ),
					),
				)
			);

			$button_text_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_added_to_wishlist_label',
					'title'      => esc_html__( '"Added to Wishlist" Text', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 6,
						'placeholder' => esc_html__( 'Add to wishlist', 'qode-wishlist-for-woocommerce' ),
					),
				)
			);

			$button_text_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_remove_from_wishlist_label',
					'title'      => esc_html__( '"Remove from Wishlist" Text', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 6,
						'placeholder' => esc_html__( 'Remove from list', 'qode-wishlist-for-woocommerce' ),
					),
				)
			);

			$button_text_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_browse_wishlist_label',
					'title'      => esc_html__( '"Browse Wishlist" Text', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 6,
						'placeholder' => esc_html__( 'Browse wishlist', 'qode-wishlist-for-woocommerce' ),
					),
				)
			);

			$button_text_section_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_successfully_added_text',
					'title'      => esc_html__( '"Item Successfully Added" Text', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 6,
						'placeholder' => esc_html__( 'Item is added', 'qode-wishlist-for-woocommerce' ),
					),
					'dependency' => array(
						'hide' => array(
							'qode_wishlist_for_woocommerce_show_modal' => array(
								'values'        => 'no',
								'default_value' => 'yes',
							),
						),
					),
				)
			);

			// Hook to include additional options after specific module options.
			do_action( 'qode_wishlist_for_woocommerce_action_after_add_to_wishlist_options_button_text_section_map', $page, $button_text_section_row );

			// Hook to include additional options after module options.
			do_action( 'qode_wishlist_for_woocommerce_action_after_add_to_wishlist_options_map', $page );
		}
	}

	add_action( 'qode_wishlist_for_woocommerce_action_default_options_init', 'qode_wishlist_for_woocommerce_add_wishlist_button_options', 15 );
}
