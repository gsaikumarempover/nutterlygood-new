<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_add_quick_view_button_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function qode_quick_view_for_woocommerce_add_quick_view_button_shortcode( $shortcodes ) {
		$shortcodes[] = 'Qode_Quick_View_For_WooCommerce_Quick_View_Button_Shortcode';

		return $shortcodes;
	}

	add_filter( 'qode_quick_view_for_woocommerce_filter_register_shortcodes', 'qode_quick_view_for_woocommerce_add_quick_view_button_shortcode' );
}

if ( class_exists( 'Qode_Quick_View_For_WooCommerce_Shortcode' ) ) {
	class Qode_Quick_View_For_WooCommerce_Quick_View_Button_Shortcode extends Qode_Quick_View_For_WooCommerce_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_URL_PATH . '/quick-view/shortcodes/quick-view-button' );
			$this->set_base( 'qode_quick_view_for_woocommerce_button' );
			$this->set_name( esc_html__( 'Quick View Button', 'qode-quick-view-for-woocommerce' ) );
			$this->set_description( esc_html__( 'Shortcode that displays quick view button', 'qode-quick-view-for-woocommerce' ) );
			$this->set_category( esc_html__( 'QODE Quick View for WooCommerce', 'qode-quick-view-for-woocommerce' ) );
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'qode-quick-view-for-woocommerce' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'item_id',
					'title'         => esc_html__( 'Product ID', 'qode-quick-view-for-woocommerce' ),
					'options'       => qode_quick_view_for_woocommerce_get_cpt_items(),
					'default_value' => '',
				)
			);
		}

		public static function call_shortcode( $params = array() ) {
			$html = qode_quick_view_for_woocommerce_call_shortcode( 'qode_quick_view_for_woocommerce_button', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			global $product;
			$item_id = ! empty( $atts['item_id'] ) ? absint( $atts['item_id'] ) : 0;

			if ( empty( $item_id ) && $product instanceof WC_Product ) {
				$item_id = $product->get_id();

				$atts['item_id'] = $item_id;
			} else {
				$product = wc_get_product( $item_id );
			}

			if ( ! apply_filters( 'qode_quick_view_for_woocommerce_filter_show_quick_view_button', true, $item_id ) ) {
				return '';
			}

			// Set shortcode attributes.
			$atts['button_type']     = apply_filters( 'qode_quick_view_for_woocommerce_filter_button_type_default_option_value', 'icon-with-text' );
			$atts['wrapper_classes'] = $this->get_wrapper_classes();
			$atts['holder_classes']  = $this->get_holder_classes( $atts );
			$atts['holder_data']     = $this->get_holder_data( $atts );

			return apply_filters( 'qode_quick_view_for_woocommerce_filter_quick_view_button', qode_quick_view_for_woocommerce_get_template_part( 'quick-view/shortcodes/quick-view-button', 'layouts/' . $atts['button_type'] . '/templates/' . $atts['button_type'], '', $atts ), $atts );
		}

		private function get_wrapper_classes() {
			$wrapper_classes   = array( 'qqvfw-quick-view-button-wrapper' );
			$wrapper_classes[] = 'qqvfw-position--' . apply_filters( 'qode_quick_view_for_woocommerce_filter_button_loop_position_default_value', 'after-add-to-cart' );

			return apply_filters( 'qode_quick_view_for_woocommerce_filter_quick_view_button_wrapper_classes', $wrapper_classes );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qqvfw-quick-view-button';
			$holder_classes[] = 'button';

			if ( ! empty( $atts['button_type'] ) ) {
				$holder_classes[] = 'qqvfw-type--' . esc_attr( $atts['button_type'] );
			}

			return apply_filters( 'qode_quick_view_for_woocommerce_filter_quick_view_button_holder_classes', $holder_classes );
		}

		private function get_holder_data( $atts ) {
			$data = array();

			$data['data-item-id']                = esc_attr( $atts['item_id'] );
			$data['data-quick-view-type']        = esc_attr( apply_filters( 'qode_quick_view_for_woocommerce_filter_set_quick_view_type', 'pop-up' ) );
			$data['data-quick-view-type-mobile'] = esc_attr( apply_filters( 'qode_quick_view_for_woocommerce_filter_set_quick_view_type_mobile', 'pop-up' ) );
			$data['aria-label']                  = esc_attr( qode_quick_view_for_woocommerce_get_quick_view_button_label( $atts ) );
			$data['href']                        = esc_url( $this->get_button_link( $atts ) );

			// Specific data which we used in ajax request.
			$data['data-shortcode-atts'] = wp_json_encode(
				stripslashes_deep(
					array(
						'button_type' => apply_filters( 'qode_quick_view_for_woocommerce_filter_button_type_default_option_value', $atts['button_type'] ),
					)
				)
			);

			$data['rel'] = 'noopener noreferrer';

			return $data;
		}

		private function get_button_link( $atts ) {
			return '?quick_view_button=' . absint( $atts['item_id'] );
		}
	}
}
