<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_elementor_instance' ) ) {
	/**
	 * Function that return page builder module instance
	 */
	function qode_wishlist_for_woocommerce_get_elementor_instance() {
		return \Elementor\Plugin::instance();
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_elementor_widgets_manager' ) ) {
	/**
	 * Function that return page builder widget module instance
	 */
	function qode_wishlist_for_woocommerce_get_elementor_widgets_manager() {
		return qode_wishlist_for_woocommerce_get_elementor_instance()->widgets_manager;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_load_elementor_widgets' ) ) {
	/**
	 * Function that include modules into page builder
	 */
	function qode_wishlist_for_woocommerce_load_elementor_widgets() {
		include_once QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/plugins/elementor/class-qode-wishlist-for-woocommerce-elementor-widget-base.php';

		$shortcodes = array();

		foreach ( glob( QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/*/shortcodes/*', GLOB_ONLYDIR ) as $shortcode ) {
			foreach ( glob( $shortcode . '/*-elementor.php' ) as $shortcode_load ) {
				$shortcodes[ basename( $shortcode_load ) ] = $shortcode_load;
			}
		}

		$additional_shortcodes = apply_filters( 'qode_wishlist_for_woocommerce_filter_include_elementor_shortcodes', array() );

		$shortcodes = array_merge( $shortcodes, $additional_shortcodes );

		if ( ! empty( $shortcodes ) ) {
			ksort( $shortcodes );

			foreach ( $shortcodes as $shortcode ) {
				include_once $shortcode;
			}
		}
	}

	if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '>' ) ) {
		add_action( 'elementor/widgets/register', 'qode_wishlist_for_woocommerce_load_elementor_widgets' );
	} else {
		add_action( 'elementor/widgets/widgets_registered', 'qode_wishlist_for_woocommerce_load_elementor_widgets' );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_register_new_elementor_widget' ) ) {
	/**
	 * Function that register a new widget type.
	 *
	 * @param \Elementor\Widget_Base $widget_instance Elementor Widget.
	 */
	function qode_wishlist_for_woocommerce_register_new_elementor_widget( $widget_instance ) {

		if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '>' ) ) {
			qode_wishlist_for_woocommerce_get_elementor_widgets_manager()->register( $widget_instance );
		} else {
			qode_wishlist_for_woocommerce_get_elementor_widgets_manager()->register_widget_type( $widget_instance );
		}
	}
}
