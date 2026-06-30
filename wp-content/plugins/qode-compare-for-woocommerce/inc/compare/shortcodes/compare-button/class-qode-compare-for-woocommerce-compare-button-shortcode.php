<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_compare_for_woocommerce_add_compare_button_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function qode_compare_for_woocommerce_add_compare_button_shortcode( $shortcodes ) {
		$shortcodes[] = 'Qode_Compare_For_WooCommerce_Compare_Button_Shortcode';

		return $shortcodes;
	}

	add_filter( 'qode_compare_for_woocommerce_filter_register_shortcodes', 'qode_compare_for_woocommerce_add_compare_button_shortcode' );
}

if ( class_exists( 'Qode_Compare_For_WooCommerce_Shortcode' ) ) {

	class Qode_Compare_For_WooCommerce_Compare_Button_Shortcode extends Qode_Compare_For_WooCommerce_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( QODE_COMPARE_FOR_WOOCOMMERCE_INC_URL_PATH . '/compare/shortcodes/compare-button' );
			$this->set_base( 'qode_compare_for_woocommerce_button' );
			$this->set_name( esc_html__( 'Compare Button', 'qode-compare-for-woocommerce' ) );
			$this->set_description( esc_html__( 'Shortcode that displays compare button', 'qode-compare-for-woocommerce' ) );
			$this->set_category( esc_html__( 'QODE Compare for WooCommerce', 'qode-compare-for-woocommerce' ) );
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'qode-compare-for-woocommerce' ),
				)
			);
		}

		public static function call_shortcode( $params = array() ) {
			$html = qode_compare_for_woocommerce_call_shortcode( 'qode_compare_for_woocommerce_button', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();
			$atts = array_merge( $atts, $options );
			global $product;
			$item_id = ! empty( $atts['item_id'] ) ? intval( $atts['item_id'] ) : 0;

			if ( empty( $item_id ) && $product instanceof WC_Product ) {
				$item_id = $product->get_id();
			} else {
				$product = wc_get_product( $item_id );
			}
			$atts['item_id'] = $item_id;

			if ( ! apply_filters( 'qode_compare_for_woocommerce_filter_show_compare_button', true, $item_id ) ) {
				return '';
			}

			$atts['is_single'] = qode_compare_for_woocommerce_is_single_product_page();

			$button_type_option  = qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_button_type' );
			$atts['button_type'] = ! empty( $atts['button_type'] ) ? $atts['button_type'] : apply_filters( 'qode_compare_for_woocommerce_filter_sc_button_type', $button_type_option, $atts );

			$button_text         = apply_filters( 'qode_compare_for_woocommerce_filter_sc_button_text', qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_button_text' ), $atts );
			$atts['button_text'] = ! empty( $button_text ) ? $button_text : esc_html__( 'Add to Compare', 'qode-compare-for-woocommerce' );

			if ( qode_compare_for_woocommerce_check_is_comparison_item_added( $atts['item_id'] ) ) {
				$atts['button_text'] = apply_filters( 'qode_compare_for_woocommerce_filter_sc_button_added_text', esc_html__( 'Added to Compare', 'qode-compare-for-woocommerce' ), $atts );
			}

			$atts['holder_classes'] = $this->get_holder_classes( $atts );
			$atts['holder_data']    = $this->get_holder_data( $atts );

			return apply_filters( 'qode_compare_for_woocommerce_filter_compare_button', qode_compare_for_woocommerce_get_template_part( 'compare/shortcodes/compare-button', 'templates/compare-button', '', $atts ), $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qcfw-button';

			$holder_classes[] = 'qcfw-spinner-item';

			$holder_classes[] = $atts['is_single'] ? 'qcfw--single' : 'qcfw--loop';

			if ( ! empty( $atts['button_type'] ) ) {
				$holder_classes[] = 'qcfw-type--' . esc_attr( $atts['button_type'] );
			}

			if ( qode_compare_for_woocommerce_check_is_comparison_item_added( $atts['item_id'] ) ) {
				$holder_classes[] = 'qcfw--added';
			}

			return implode( ' ', apply_filters( 'qode_compare_for_woocommerce_filter_compare_button_holder_classes', $holder_classes, $atts ) );
		}

		private function get_holder_data( $atts ) {
			$data = array();

			$data['data-item-id']          = esc_attr( $atts['item_id'] );
			$data['data-original-item-id'] = esc_attr( $atts['item_id'] );

			return $data;
		}
	}
}
