<?php

if ( ! function_exists( 'greenpath_core_add_mobile_video_holder_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function greenpath_core_add_mobile_video_holder_shortcode( $shortcodes ) {
		$shortcodes[] = 'GreenPathCore_Mobile_Video_Holder_Shortcode';

		return $shortcodes;
	}

	add_filter( 'greenpath_core_filter_register_shortcodes', 'greenpath_core_add_mobile_video_holder_shortcode' );
}

if ( class_exists( 'GreenPathCore_Shortcode' ) ) {
	class GreenPathCore_Mobile_Video_Holder_Shortcode extends GreenPathCore_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( GREENPATH_CORE_SHORTCODES_URL_PATH . '/mobile-video-holder' );
			$this->set_base( 'greenpath_core_mobile_video_holder' );
			$this->set_name( esc_html__( 'Mobile Video Holder', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Shortcode that adds mobile video holder element', 'greenpath-core' ) );
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'video_source',
					'title'      => esc_html__( 'Link to Self Hosted Video', 'greenpath-core' ),
				)
			);
		}

		public static function call_shortcode( $params ) {
			$html = qode_framework_call_shortcode( 'greenpath_core_mobile_video_holder', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['holder_classes']     = $this->get_holder_classes( $atts );

			return greenpath_core_get_template_part( 'shortcodes/mobile-video-holder', 'templates/mobile-video-holder', '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-mobile-video-holder';

			return implode( ' ', $holder_classes );
		}
	}
}
