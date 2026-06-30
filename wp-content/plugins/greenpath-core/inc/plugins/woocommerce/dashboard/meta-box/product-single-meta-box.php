<?php

if ( ! function_exists( 'greenpath_core_add_product_single_product_meta_box' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function greenpath_core_add_product_single_product_meta_box() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'  => array( 'product' ),
				'type'   => 'meta',
				'slug'   => 'product-single',
				'title'  => esc_html__( 'Product Single', 'greenpath-core' ),
				'layout' => 'tabbed',
			)
		);

		if ( $page ) {

			$general_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-general',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'General Settings', 'greenpath-core' ),
					'description' => esc_html__( 'General product settings', 'greenpath-core' ),
				)
			);

			$single_layouts = apply_filters(
				'greenpath_core_filter_woo_single_product_layouts',
				array(
					''         => esc_html__( 'Default', 'greenpath-core' ),
					'standard' => esc_html__( 'Standard', 'greenpath-core' ),
				)
			);

			if ( count( $single_layouts ) > 1 ) {
				$general_tab->add_field_element(
					array(
						'field_type'  => 'select',
						'name'        => 'qodef_woo_single_layout',
						'title'       => esc_html__( 'Product layout', 'greenpath-core' ),
						'description' => esc_html__( 'Choose a default layout for single product page', 'greenpath-core' ),
						'options'     => $single_layouts,
					)
				);
			}

			$media_section = $general_tab->add_section_element(
				array(
					'name'  => 'qodef_woo_single_media_section',
					'title' => esc_html__( 'Media', 'greenpath-core' ),
					'dependency'    => array(
						'show' => array(
							'qodef_woo_single_layout' => array(
								'values'        => array( 'standard' ),
								'default_value' => '',
							),
						),
					),
				)
			);

			$media_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_woo_single_media_layout',
					'title'         => esc_html__( 'Media Layout', 'greenpath-core' ),
					'description'   => esc_html__( 'Choose media display layout on single product pages (only for Standard layout)', 'greenpath-core' ),
					'options'       => array(
						'slider'  => esc_html__( 'Slider', 'greenpath-core' ),
						'gallery' => esc_html__( 'Gallery', 'greenpath-core' ),
						'combo'   => esc_html__( 'Combo', 'greenpath-core' ),
					),
					'default_value' => 'combo',
				)
			);

			$media_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_woo_single_enable_image_lightbox',
					'title'         => esc_html__( 'Enable Image Lightbox', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will set lightbox functionality for images on single product page', 'greenpath-core' ),
					'options'       => array(
						''               => esc_html__( 'None', 'greenpath-core' ),
						'photo-swipe'    => esc_html__( 'Photo Swipe', 'greenpath-core' ),
						'magnific-popup' => esc_html__( 'Magnific Popup', 'greenpath-core' ),
					),
					'default_value' => 'magnific-popup',
					'dependency'    => array(
						'show' => array(
							'qodef_woo_single_media_layout' => array(
								'values'        => array( 'gallery', 'combo' ),
								'default_value' => 'combo',
							),
						),
					),
				)
			);

			$media_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_woo_single_enable_image_zoom',
					'title'         => esc_html__( 'Enable Zoom Magnifier', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will show magnifier image on hover on single product page', 'greenpath-core' ),
					'default_value' => 'yes',
					'dependency'    => array(
						'show' => array(
							'qodef_woo_single_media_layout' => array(
								'values'        => array( 'gallery', 'combo' ),
								'default_value' => 'combo',
							),
						),
					),
				)
			);

			$media_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_woo_single_thumb_images_position',
					'title'         => esc_html__( 'Set Thumbnail Images Position', 'greenpath-core' ),
					'description'   => esc_html__( 'Choose position of the thumbnail images on single product page relative to featured image', 'greenpath-core' ),
					'options'       => array(
						''      => esc_html__( 'Default', 'greenpath-core' ),
						'below' => esc_html__( 'Below', 'greenpath-core' ),
						'left'  => esc_html__( 'Left', 'greenpath-core' ),
					),
					'default_value' => 'below',
					'dependency'    => array(
						'show' => array(
							'qodef_woo_single_media_layout' => array(
								'values'        => array( 'gallery', 'combo' ),
								'default_value' => 'combo',
							),
						),
					),
				)
			);

			$media_section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_woo_single_thumbnail_images_columns',
					'title'       => esc_html__( 'Number of Thumbnail Image Columns', 'greenpath-core' ),
					'description' => esc_html__( 'Set a number of columns for thumbnail images on single product pages', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'columns_number', true ),
					'dependency'  => array(
						'show' => array(
							'qodef_woo_single_media_layout' => array(
								'values'        => array( 'gallery', 'combo' ),
								'default_value' => 'combo',
							),
						),
					),
				)
			);

			$general_tab->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_woo_single_enable_cart_showcase',
					'title'         => esc_html__( 'Enable Cart Showcase', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will display the Product Cart Showcase shortcode with Cross-Sell Products', 'greenpath-core' ),
					'default_value' => 'no',
				)
			);

			$general_tab->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_woo_single_enable_info_panels',
					'title'         => esc_html__( 'Enable Info Panels', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will show information panels on the Product Single page', 'greenpath-core' ),
					'default_value' => 'no',
					'dependency'    => array(
						'hide' => array(
							'qodef_woo_single_layout' => array(
								'values'        => 'big_gallery',
								'default_value' => ''
							)
						)
					)
				)
			);

			$product_details_section = $general_tab->add_section_element(
				array(
					'name'        => 'qodef_woo_single_info_panels_section',
					'title'       => esc_html__( 'Product Info Panels', 'greenpath-core' ),
					'dependency'  => array(
						'show' => array(
							'qodef_woo_single_enable_info_panels' => array(
								'values'        => 'yes',
								'default_value' => '',
							),
						),
					),
				)
			);

			$extra_repeater = $product_details_section->add_repeater_element(
				array(
					'name'        => 'qodef_woo_single_info_panels',
					'title'       => esc_html__( 'Info Panel Items', 'greenpath-core' ),
					'description' => esc_html__( 'Add custom icons with text that will be displayed on the Single page', 'greenpath-core' ),
					'button_text' => esc_html__( 'Add New Product Detail', 'greenpath-core' ),
				)
			);

			$extra_repeater->add_field_element(
				array(
					'field_type'  => 'textarea',
					'name'        => 'qodef_woo_single_info_panel_icon_svg',
					'title'       => esc_html__( 'Info Panel Icon SVG', 'greenpath-core' ),
				)
			);

			$extra_repeater->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_woo_single_info_panel_bold_text',
					'title'      => esc_html__( 'Info Panel Bold Text', 'greenpath-core' ),
				)
			);

			$extra_repeater->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_woo_single_info_panel_text',
					'title'      => esc_html__( 'Info Panel Text', 'greenpath-core' ),
				)
			);

			// Hook to include additional options after module options
			do_action( 'greenpath_core_action_after_product_single_meta_box_map', $page, $general_tab );
		}
	}

	add_action( 'greenpath_core_action_default_meta_boxes_init', 'greenpath_core_add_product_single_product_meta_box' );
}
