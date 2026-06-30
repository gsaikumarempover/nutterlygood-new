<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}

if ( ! function_exists( 'qode_compare_for_woocommerce_add_compare_button_options' ) ) {
	/**
	 * Function that add button options for this module
	 */
	function qode_compare_for_woocommerce_add_compare_button_options() {
		$qode_framework = qode_compare_for_woocommerce_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => QODE_COMPARE_FOR_WOOCOMMERCE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'compare-button',
				'icon'        => 'fa fa-indent',
				'title'       => esc_html__( 'Compare Button', 'qode-compare-for-woocommerce' ),
				'description' => esc_html__( 'Compare Button Options', 'qode-compare-for-woocommerce' ),
				'layout'      => apply_filters( 'qode_compare_for_woocommerce_filter_compare_button_options_page_layout', '' ),
			)
		);

		if ( $page ) {

			$general_tab = $page->add_tab_element(
				array(
					'name'  => 'qode_compare_for_woocommerce_button_general_tab',
					'title' => esc_html__( 'General', 'qode-compare-for-woocommerce' ),
				)
			);

			$button_general_section = $general_tab->add_section_element(
				array(
					'name' => 'qode_compare_for_woocommerce_button_general_section',
				)
			);

			do_action( 'qode_compare_for_woocommerce_action_general_button_options', $button_general_section, $general_tab, $page );
		}
	}
	add_action( 'qode_compare_for_woocommerce_action_core_options_init', 'qode_compare_for_woocommerce_add_compare_button_options', 30 );
}

if ( ! function_exists( 'qode_compare_for_woocommerce_add_button_options' ) ) {
	/**
	 * Function that add button options
	 */
	function qode_compare_for_woocommerce_add_button_options( $section ) {
		if ( $section ) {
			$section->add_field_element(
				array(
					'field_type'    => 'radio',
					'name'          => 'qode_compare_for_woocommerce_button_type',
					'title'         => esc_html__( 'Button Type', 'qode-compare-for-woocommerce' ),
					'description'   => esc_html__( 'Select a type of the button you wish to use', 'qode-compare-for-woocommerce' ),
					'options'       => array(
						'textual' => esc_html__( 'Textual', 'qode-compare-for-woocommerce' ),
						'solid'   => esc_html__( 'Solid', 'qode-compare-for-woocommerce' ),
					),
					'default_value' => apply_filters( 'qode_compare_for_woocommerce_filter_button_type_default_value', 'textual' ),
				)
			);

			do_action( 'qode_compare_for_woocommerce_action_after_button_type_option', $section );

			$section->add_field_element(
				array(
					'field_type'    => 'text',
					'name'          => 'qode_compare_for_woocommerce_button_text',
					'title'         => esc_html__( 'Button Label', 'qode-compare-for-woocommerce' ),
					'default_value' => esc_html__( 'Add to Compare', 'qode-compare-for-woocommerce' ),
					'dependency'    => apply_filters( 'qode_compare_for_woocommerce_button_label_option_dependency', array() ),
				)
			);

			do_action( 'qode_compare_for_woocommerce_action_after_button_text_option', $section );
		}
	}
	add_action( 'qode_compare_for_woocommerce_action_general_button_options', 'qode_compare_for_woocommerce_add_button_options' );
}

if ( ! function_exists( 'qode_compare_for_woocommerce_add_auto_popup_option' ) ) {
	/**
	 * Function that add auto popup option
	 */
	function qode_compare_for_woocommerce_add_auto_popup_option( $section ) {
		if ( $section ) {
			$section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_compare_for_woocommerce_auto_popup',
					'title'         => esc_html__( 'Open Lightbox Automatically', 'qode-compare-for-woocommerce' ),
					'description'   => esc_html__( 'Display the lightbox automatically after "Compare" button is clicked', 'qode-compare-for-woocommerce' ),
					'default_value' => apply_filters( 'qode_compare_for_woocommerce_filter_auto_popup_default_value', 'yes' ),
					'dependency'    => apply_filters( 'qode_compare_for_woocommerce_filter_auto_lightbox_option_dependency', array() ),
				)
			);
		}
	}
	add_action( 'qode_compare_for_woocommerce_action_general_button_options', 'qode_compare_for_woocommerce_add_auto_popup_option', 20 );
}

if ( ! function_exists( 'qode_compare_for_woocommerce_add_loop_option' ) ) {
	/**
	 * Function that add loop option
	 */
	function qode_compare_for_woocommerce_add_loop_option( $section ) {
		if ( $section ) {
			$section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_compare_for_woocommerce_show_in_loop',
					'title'         => esc_html__( 'Show "Compare" Button in Loops', 'qode-compare-for-woocommerce' ),
					'description'   => esc_html__( 'Display "Compare" button in WooCommerce loops (shop pages, archive pages and all other places where the WooCommerce product loop is shown)', 'qode-compare-for-woocommerce' ),
					'default_value' => apply_filters( 'qode_compare_for_woocommerce_filter_show_in_loop_default_value', 'yes' ),
				)
			);

			do_action( 'qode_compare_for_woocommerce_action_after_show_in_loop_option', $section );
		}
	}
	add_action( 'qode_compare_for_woocommerce_action_general_button_options', 'qode_compare_for_woocommerce_add_loop_option', 30 );
}

if ( ! function_exists( 'qode_compare_for_woocommerce_add_page_option' ) ) {
	/**
	 * Function that add single page option
	 */
	function qode_compare_for_woocommerce_add_page_option( $section ) {
		if ( $section ) {
			$section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_compare_for_woocommerce_show_on_single_product',
					'title'         => esc_html__( 'Show "Compare" Button on Product Pages', 'qode-compare-for-woocommerce' ),
					'description'   => esc_html__( 'Display the "Compare" button on single product pages', 'qode-compare-for-woocommerce' ),
					'default_value' => apply_filters( 'qode_compare_for_woocommerce_filter_show_on_single_product_default_value', 'no' ),
				)
			);

			do_action( 'qode_compare_for_woocommerce_action_after_show_on_single_product_option', $section );
		}
	}
	add_action( 'qode_compare_for_woocommerce_action_general_button_options', 'qode_compare_for_woocommerce_add_page_option', 40 );
}
