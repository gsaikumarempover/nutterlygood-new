<?php

if ( ! function_exists( 'greenpath_core_add_woocommerce_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function greenpath_core_add_woocommerce_options() {
		$qode_framework = qode_framework_get_framework_root();

		$list_item_layouts = apply_filters( 'greenpath_core_filter_product_list_layouts', array() );
		$options_map       = greenpath_core_get_variations_options_map( $list_item_layouts );

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => GREENPATH_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'woocommerce',
				'icon'        => 'fa fa-book',
				'title'       => esc_html__( 'WooCommerce', 'greenpath-core' ),
				'description' => esc_html__( 'Global WooCommerce Options', 'greenpath-core' ),
				'layout'      => 'tabbed',
			)
		);

		if ( $page ) {

			$list_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-list',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'Product List', 'greenpath-core' ),
					'description' => esc_html__( 'Settings related to product list', 'greenpath-core' ),
				)
			);

			if ( $options_map['visibility'] ) {
				$list_tab->add_field_element(
					array(
						'field_type'    => 'select',
						'name'          => 'qodef_product_list_item_layout',
						'title'         => esc_html__( 'Item Layout', 'greenpath-core' ),
						'description'   => esc_html__( 'Choose layout for list item on shop lists', 'greenpath-core' ),
						'options'       => $list_item_layouts,
						'default_value' => $options_map['default_value'],
					)
				);
			}

			$list_tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_woo_product_list_columns',
					'title'       => esc_html__( 'Number of Columns', 'greenpath-core' ),
					'description' => esc_html__( 'Choose number of columns for product list on shop pages', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'columns_number' ),
				)
			);

			$list_tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_woo_product_list_columns_space',
					'title'       => esc_html__( 'Items Horizontal Spacing', 'greenpath-core' ),
					'description' => esc_html__( 'Choose horizontal space between items for product list on shop pages', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'items_space' ),
				)
			);

			$woo_product_list_columns_space_row = $list_tab->add_row_element(
				array(
					'name'       => 'qodef_woo_product_list_columns_space_row',
					'dependency' => array(
						'show' => array(
							'qodef_woo_product_list_columns_space' => array(
								'values'        => 'custom',
								'default_value' => '',
							),
						),
					),
				)
			);

			$woo_product_list_columns_space_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_product_list_columns_space_custom',
					'title'       => esc_html__( 'Custom Horizontal Spacing', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$woo_product_list_columns_space_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_product_list_columns_space_custom_1512',
					'title'       => esc_html__( 'Custom Horizontal Spacing - 1512', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 1512px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$woo_product_list_columns_space_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_product_list_columns_space_custom_1200',
					'title'       => esc_html__( 'Custom Horizontal Spacing - 1200', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 1200px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$woo_product_list_columns_space_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_product_list_columns_space_custom_880',
					'title'       => esc_html__( 'Custom Horizontal Spacing - 880', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 880px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$list_tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_woo_product_list_columns_vertical_space',
					'title'       => esc_html__( 'Items Vertical Spacing', 'greenpath-core' ),
					'description' => esc_html__( 'Choose vertical space between items for product list on shop pages', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'items_space' ),
				)
			);

			$woo_product_list_columns_vertical_space_row = $list_tab->add_row_element(
				array(
					'name'       => 'qodef_woo_product_list_columns_vertical_space_row',
					'dependency' => array(
						'show' => array(
							'qodef_woo_product_list_columns_vertical_space' => array(
								'values'        => 'custom',
								'default_value' => '',
							),
						),
					),
				)
			);

			$woo_product_list_columns_vertical_space_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_product_list_columns_vertical_space_custom',
					'title'       => esc_html__( 'Custom Vertical Spacing', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$woo_product_list_columns_vertical_space_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_product_list_columns_vertical_space_custom_1512',
					'title'       => esc_html__( 'Custom Vertical Spacing - 1512', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 1512px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$woo_product_list_columns_vertical_space_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_product_list_columns_vertical_space_custom_1200',
					'title'       => esc_html__( 'Custom Vertical Spacing - 1200', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 1200px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$woo_product_list_columns_vertical_space_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_product_list_columns_vertical_space_custom_880',
					'title'       => esc_html__( 'Custom Vertical Spacing - 880', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 880px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$list_tab->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_product_list_products_per_page',
					'title'       => esc_html__( 'Products per Page', 'greenpath-core' ),
					'description' => esc_html__( 'Set number of products on shop pages. Default value is 12', 'greenpath-core' ),
				)
			);

			$list_tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_woo_product_list_title_tag',
					'title'       => esc_html__( 'Title Tag', 'greenpath-core' ),
					'description' => esc_html__( 'Choose title tag for product list item on shop pages', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'title_tag' ),
				)
			);

			$list_tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_woo_product_list_sidebar_layout',
					'title'         => esc_html__( 'Sidebar Layout', 'greenpath-core' ),
					'description'   => esc_html__( 'Choose default sidebar layout for shop pages', 'greenpath-core' ),
					'default_value' => 'no-sidebar',
					'options'       => greenpath_core_get_select_type_options_pool( 'sidebar_layouts', false ),
				)
			);

			$custom_sidebars = greenpath_core_get_custom_sidebars();
			if ( ! empty( $custom_sidebars ) && count( $custom_sidebars ) > 1 ) {
				$list_tab->add_field_element(
					array(
						'field_type'  => 'select',
						'name'        => 'qodef_woo_product_list_custom_sidebar',
						'title'       => esc_html__( 'Custom Sidebar', 'greenpath-core' ),
						'description' => esc_html__( 'Choose a custom sidebar to display on shop pages', 'greenpath-core' ),
						'options'     => $custom_sidebars,
					)
				);
			}

			$list_tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_woo_product_list_sidebar_grid_gutter',
					'title'       => esc_html__( 'Set Grid Gutter', 'greenpath-core' ),
					'description' => esc_html__( 'Choose grid gutter size to set space between content and sidebar', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'items_space' ),
				)
			);

			$woo_product_list_sidebar_grid_gutter_row = $list_tab->add_row_element(
				array(
					'name'       => 'qodef_woo_product_list_sidebar_grid_gutter_row',
					'dependency' => array(
						'show' => array(
							'qodef_woo_product_list_sidebar_grid_gutter' => array(
								'values'        => 'custom',
								'default_value' => '',
							),
						),
					),
				)
			);

			$woo_product_list_sidebar_grid_gutter_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_product_list_sidebar_grid_gutter_custom',
					'title'       => esc_html__( 'Custom Grid Gutter', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$woo_product_list_sidebar_grid_gutter_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_product_list_sidebar_grid_gutter_custom_1512',
					'title'       => esc_html__( 'Custom Grid Gutter - 1512', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 1512px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$woo_product_list_sidebar_grid_gutter_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_product_list_sidebar_grid_gutter_custom_1200',
					'title'       => esc_html__( 'Custom Grid Gutter - 1200', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 1200px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$woo_product_list_sidebar_grid_gutter_row->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_product_list_sidebar_grid_gutter_custom_880',
					'title'       => esc_html__( 'Custom Grid Gutter - 880', 'greenpath-core' ),
					'description' => esc_html__( 'Enter grid gutter size in pixels for screen size below 880px', 'greenpath-core' ),
					'args'        => array(
						'col_width' => 3,
					),
				)
			);

			$list_tab->add_field_element(
				array(
					'field_type'    => 'yesno',
					'default_value' => 'no',
					'name'          => 'qodef_woo_enable_percent_sign_value',
					'title'         => esc_html__( 'Enable Percent Sign', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will show percent value mark instead of sale label on products', 'greenpath-core' ),
				)
			);

			// Hook to include additional options after section module options
			do_action( 'greenpath_core_action_after_woo_product_list_options_map', $list_tab );

			$single_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-single',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'Product Single', 'greenpath-core' ),
					'description' => esc_html__( 'Settings related to product single', 'greenpath-core' ),
				)
			);

			$single_layouts = apply_filters(
				'greenpath_core_filter_woo_single_product_layouts',
				array(
					'standard' => esc_html__( 'Standard', 'greenpath-core' ),
				)
			);

			if ( count( $single_layouts ) > 1 ) {
				$single_tab->add_field_element(
					array(
						'field_type'    => 'select',
						'name'          => 'qodef_woo_single_layout',
						'title'         => esc_html__( 'Product layout', 'greenpath-core' ),
						'description'   => esc_html__( 'Choose a default layout for single product page', 'greenpath-core' ),
						'options'       => $single_layouts,
						'default_value' => 'standard',
					)
				);
			}

			$single_tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_woo_single_enable_page_title',
					'title'       => esc_html__( 'Enable Page Title', 'greenpath-core' ),
					'description' => esc_html__( 'Use this option to enable/disable page title on single product page', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'no_yes' ),
				)
			);

			$single_tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_woo_single_title_tag',
					'title'       => esc_html__( 'Title Tag', 'greenpath-core' ),
					'description' => esc_html__( 'Choose title tag for product on single product page', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'title_tag' ),
				)
			);

			$media_section = $single_tab->add_section_element(
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
					'options'     => greenpath_core_get_select_type_options_pool( 'columns_number' ),
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

			$single_tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_woo_single_related_product_list_columns',
					'title'       => esc_html__( 'Number of Related Product Columns', 'greenpath-core' ),
					'description' => esc_html__( 'Set a number of columns for related products on single product pages', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'columns_number' ),
				)
			);

			$single_tab->add_field_element(
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

			$product_details_section = $single_tab->add_section_element(
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
					'button_text' => esc_html__( 'Add New Info Panel', 'greenpath-core' ),
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

			$single_tab->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_woo_single_enable_fake_live_viewing',
					'title'         => esc_html__( 'Enable Fake Live Viewing', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will show live viewing message on single product pages', 'greenpath-core' ),
					'default_value' => 'yes',
				)
			);

			$flw_section = $single_tab->add_section_element(
				array(
					'name'        => 'qodef_woo_single_fake_live_viewing_section',
					'title'       => esc_html__( 'Fake Live Viewing', 'greenpath-core' ),
					'description' => esc_html__( 'Fake Live Viewing settings', 'greenpath-core' ),
					'dependency'  => array(
						'show' => array(
							'qodef_woo_single_enable_fake_live_viewing' => array(
								'values'        => 'yes',
								'default_value' => 'yes',
							),
						),
					),
				)
			);

			$flw_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_single_fake_live_viewing_min',
					'title'       => esc_html__( 'Min Viewers Count', 'greenpath-core' ),
					'description' => esc_html__( 'Set minimum count for number of fake live viewers.', 'greenpath-core' ),
				)
			);

			$flw_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_single_fake_live_viewing_max',
					'title'       => esc_html__( 'Max Viewers Count', 'greenpath-core' ),
					'description' => esc_html__( 'Set maximum count for number of fake live viewers.', 'greenpath-core' ),
				)
			);

			$single_tab->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_woo_single_enable_sales_count',
					'title'         => esc_html__( 'Enable Sales Count', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will show number of sales on single product pages', 'greenpath-core' ),
					'default_value' => 'yes',
				)
			);

			$single_tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_woo_single_sales_count_type',
					'title'         => esc_html__( 'Sales Type', 'greenpath-core' ),
					'description'   => esc_html__( 'Choose time period for fake sales count.', 'greenpath-core' ),
					'options'       => array(
						'fake'  => esc_html__( 'Fake', 'greenpath-core' ),
						'total' => esc_html__( 'Total', 'greenpath-core' ),
					),
					'default_value' => 'fake',
					'dependency'    => array(
						'show' => array(
							'qodef_woo_single_enable_sales_count' => array(
								'values'        => 'yes',
								'default_value' => 'yes',
							),
						),
					),
				)
			);

			$fsc_section = $single_tab->add_section_element(
				array(
					'name'        => 'qodef_woo_single_fake_sales_count_section',
					'title'       => esc_html__( 'Fake Sales Count', 'greenpath-core' ),
					'description' => esc_html__( 'Fake Sales Count settings', 'greenpath-core' ),
					'dependency'  => array(
						'show' => array(
							'qodef_woo_single_sales_count_type' => array(
								'values'        => 'fake',
								'default_value' => 'fake',
							),
						),
					),
				)
			);

			$fsc_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_single_fake_sales_count_min',
					'title'       => esc_html__( 'Min Sales Count', 'greenpath-core' ),
					'description' => esc_html__( 'Set minimum count for number fake sales.', 'greenpath-core' ),
				)
			);

			$fsc_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_single_fake_sales_count_max',
					'title'       => esc_html__( 'Max Sales Count', 'greenpath-core' ),
					'description' => esc_html__( 'Set maximum count for number of fake sales.', 'greenpath-core' ),
				)
			);

			$fsc_section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_woo_single_fake_sales_time_period',
					'title'       => esc_html__( 'Time Period', 'greenpath-core' ),
					'description' => esc_html__( 'Choose time period for fake sales count.', 'greenpath-core' ),
					'options'     => array(
						''       => esc_html__( 'Default', 'greenpath-core' ),
						'minute' => esc_html__( 'Minutes', 'greenpath-core' ),
						'hour'   => esc_html__( 'Hours', 'greenpath-core' ),
						'day'    => esc_html__( 'Days', 'greenpath-core' ),
						'week'   => esc_html__( 'Weeks', 'greenpath-core' ),
					),
				)
			);

			$fsc_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_woo_single_fake_sales_time_frame',
					'title'       => esc_html__( 'Time Frame', 'greenpath-core' ),
					'description' => esc_html__( 'Enter custom time frame value.', 'greenpath-core' ),
				)
			);

			// Hook to include additional options after section module options
			do_action( 'greenpath_core_action_after_woo_product_single_options_map', $single_tab );

			// Hook to include additional options after module options
			do_action( 'greenpath_core_action_after_woo_options_map', $page );
		}
	}

	add_action( 'greenpath_core_action_default_options_init', 'greenpath_core_add_woocommerce_options', greenpath_core_get_admin_options_map_position( 'woocommerce' ) );
}
