<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_add_quick_view_button_options' ) ) {
	/**
	 * Function that add button options for this module
	 */
	function qode_quick_view_for_woocommerce_add_quick_view_button_options() {

		$qode_quick_view_for_woocommerce_framework = qode_quick_view_for_woocommerce_framework_get_framework_root();

		$page = $qode_quick_view_for_woocommerce_framework->add_options_page(
			array(
				'scope'       => QODE_QUICK_VIEW_FOR_WOOCOMMERCE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'quick-view-button',
				'icon'        => 'fa fa-indent',
				'title'       => esc_html__( 'Quick View Button', 'qode-quick-view-for-woocommerce' ),
				'description' => esc_html__( 'Quick View Button Options', 'qode-quick-view-for-woocommerce' ),
				'layout'      => 'tabbed',
			)
		);

		if ( $page ) {

			$general_tab = $page->add_tab_element(
				array(
					'name'  => 'qode_quick_view_for_woocommerce_button_general_tab',
					'title' => esc_html__( 'General', 'qode-quick-view-for-woocommerce' ),
				)
			);

			$button_type        = apply_filters( 'qode_quick_view_for_woocommerce_filter_button_type_option', array( 'icon-with-text' => esc_html__( 'Icon With Text', 'qode-quick-view-for-woocommerce' ) ) );
			$button_type_option = apply_filters( 'qode_quick_view_for_woocommerce_filter_button_type_default_option_value', 'icon-with-text' );

			$general_tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qode_quick_view_for_woocommerce_button_type',
					'title'         => esc_html__( 'Type', 'qode-quick-view-for-woocommerce' ),
					'description'   => esc_html__( 'Choose a type of the "Quick View" button you wish to use', 'qode-quick-view-for-woocommerce' ),
					'options'       => $button_type,
					'default_value' => $button_type_option,
					'args'          => array(
						'custom_class' => ( count( $button_type ) === 1 ) ? 'qodef-hidden' : '',
					),
				)
			);

			$button_position        = apply_filters( 'qode_quick_view_for_woocommerce_filter_button_loop_position_option', array( 'after-add-to-cart' => esc_html__( 'After Add To Cart', 'qode-quick-view-for-woocommerce' ) ) );
			$button_position_option = apply_filters( 'qode_quick_view_for_woocommerce_filter_button_loop_position_default_value', 'after-add-to-cart' );

			$general_tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qode_quick_view_for_woocommerce_button_loop_position',
					'title'         => esc_html__( 'Position', 'qode-quick-view-for-woocommerce' ),
					'description'   => __( 'Choose where you wish to position the "Quick View" button inside WooCommerce Product Loops. In the event that you are using the shortcode, you can copy the following code and paste it where you wish to place it on your page, along with the corresponding product ID: <code>[qode_quick_view_for_woocommerce_button item_id="product_id"]</code>', 'qode-quick-view-for-woocommerce' ),
					'options'       => $button_position,
					'default_value' => $button_position_option,
					'args'          => array(
						'custom_class' => ( count( $button_position ) === 1 ) ? 'qodef-hidden' : '',
					),
				)
			);

			$general_tab->add_field_element(
				array(
					'field_type'    => 'text',
					'name'          => 'qode_quick_view_for_woocommerce_button_label',
					'title'         => esc_html__( 'Label', 'qode-quick-view-for-woocommerce' ),
					'description'   => esc_html__( 'Input text for the "Quick View" button label in WooCommerce Product Loops', 'qode-quick-view-for-woocommerce' ),
					'default_value' => esc_html__( 'Quick View', 'qode-quick-view-for-woocommerce' ),
					'args'          => array(
						'custom_class' => 'qodef-no-indent',
					),
					'dependency'    => array(
						'hide' => array(
							'qode_quick_view_for_woocommerce_button_type' => array(
								'values'        => 'icon',
								'default_value' => 'icon-with-text',
							),
						),
					),
				)
			);

			// Hook to include additional options after module options.
			do_action( 'qode_quick_view_for_woocommerce_action_after_quick_view_button_options_map', $page, $general_tab );
		}
	}

	add_action( 'qode_quick_view_for_woocommerce_action_default_options_init', 'qode_quick_view_for_woocommerce_add_quick_view_button_options' );
}
