<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_add_quick_view_content_options' ) ) {
	/**
	 * Function that add product options for this module
	 */
	function qode_quick_view_for_woocommerce_add_quick_view_content_options() {

		$qode_quick_view_for_woocommerce_framework = qode_quick_view_for_woocommerce_framework_get_framework_root();

		$page = $qode_quick_view_for_woocommerce_framework->add_options_page(
			array(
				'scope'       => QODE_QUICK_VIEW_FOR_WOOCOMMERCE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'content',
				'icon'        => 'fa fa-indent',
				'title'       => esc_html__( 'Content', 'qode-quick-view-for-woocommerce' ),
				'description' => esc_html__( 'Content Options', 'qode-quick-view-for-woocommerce' ),
				'layout'      => 'tabbed',
			)
		);

		if ( $page ) {

			// Hook to include additional options after module options.
			do_action( 'qode_quick_view_for_woocommerce_action_after_content_options_map', $page );

			$content_style_tab = $page->add_tab_element(
				array(
					'name'  => 'qode_quick_view_for_woocommerce_content_style_tab',
					'title' => esc_html__( 'Style', 'qode-quick-view-for-woocommerce' ),
				)
			);

			$content_style_general_section = $content_style_tab->add_section_element(
				array(
					'name'        => 'qode_quick_view_for_woocommerce_content_style_general_section',
					'title'       => esc_html__( 'Quick View Content', 'qode-quick-view-for-woocommerce' ),
					'description' => esc_html__( 'Make general stylistic adjustments to the content inside the Quick View', 'qode-quick-view-for-woocommerce' ),
				)
			);

			$content_style_general_row = $content_style_general_section->add_row_element(
				array(
					'name' => 'qode_quick_view_for_woocommerce_content_style_general_row',
				)
			);

			$content_style_general_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_quick_view_for_woocommerce_background_color',
					'title'      => esc_html__( 'Background', 'qode-quick-view-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			// Hook to include additional options after module options.
			do_action( 'qode_quick_view_for_woocommerce_action_after_content_style_general_row_options_map', $content_style_general_row );

			$close_icon_style_section = $content_style_tab->add_section_element(
				array(
					'name'        => 'qode_quick_view_for_woocommerce_content_close_icon_style_section',
					'title'       => esc_html__( 'Close Icon', 'qode-quick-view-for-woocommerce' ),
					'description' => esc_html__( 'Make general stylistic adjustments to the "Close" icon appearance', 'qode-quick-view-for-woocommerce' ),
				)
			);

			$close_icon_style_row = $close_icon_style_section->add_row_element(
				array(
					'name' => 'qode_quick_view_for_woocommerce_content_close_icon_style_row',
				)
			);

			$close_icon_style_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_quick_view_for_woocommerce_close_icon_color',
					'title'      => esc_html__( 'Close Icon', 'qode-quick-view-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			$close_icon_style_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_quick_view_for_woocommerce_close_icon_hover_color',
					'title'      => esc_html__( 'Close Icon Hover', 'qode-quick-view-for-woocommerce' ),
					'args'       => array(
						'col_width' => 3,
					),
				)
			);

			// Hook to include additional options after module options.
			do_action( 'qode_quick_view_for_woocommerce_action_after_content_close_icon_style_options_map', $close_icon_style_section );
			// Hook to include additional options after module options.
			do_action( 'qode_quick_view_for_woocommerce_action_after_content_style_options_map', $page, $content_style_tab );
		}
	}

	add_action( 'qode_quick_view_for_woocommerce_action_default_options_init', 'qode_quick_view_for_woocommerce_add_quick_view_content_options' );
}
