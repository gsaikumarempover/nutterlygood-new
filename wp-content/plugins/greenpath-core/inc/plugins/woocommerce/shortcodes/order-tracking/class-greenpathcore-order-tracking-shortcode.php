<?php

if ( ! function_exists( 'greenpath_core_add_order_tracking_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function greenpath_core_add_order_tracking_shortcode( $shortcodes ) {
		$shortcodes[] = 'GreenPathCore_Order_Tracking_Shortcode';

		return $shortcodes;
	}

	add_filter( 'greenpath_core_filter_register_shortcodes', 'greenpath_core_add_order_tracking_shortcode', 8 );
}

if ( class_exists( 'GreenPathCore_Shortcode' ) ) {
	class GreenPathCore_Order_Tracking_Shortcode extends GreenPathCore_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( GREENPATH_CORE_PLUGINS_URL_PATH . '/woocommerce/shortcodes/order-tracking' );
			$this->set_base( 'greenpath_core_order_tracking' );
			$this->set_name( esc_html__( 'Order Tracking', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Shortcode that shows the order tracking form', 'greenpath-core' ) );
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'greenpath-core' ),
				)
			);
		}

		public static function call_shortcode( $params ) {
			$html = qode_framework_call_shortcode( 'greenpath_core_order_tracking', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['holder_classes'] = $this->get_holder_classes( $atts );

			return greenpath_core_get_template_part( 'plugins/woocommerce/shortcodes/order-tracking', 'templates/order-tracking', '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-order-tracking';

			return implode( ' ', $holder_classes );
		}
	}
}
