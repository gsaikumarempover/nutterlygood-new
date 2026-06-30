<?php

if ( ! function_exists( 'greenpath_core_add_banner_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function greenpath_core_add_banner_widget( $widgets ) {
		$widgets[] = 'GreenPathCore_Banner_Widget';

		return $widgets;
	}

	add_filter( 'greenpath_core_filter_register_widgets', 'greenpath_core_add_banner_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathCore_Banner_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$widget_mapped = $this->import_shortcode_options(
				array(
					'shortcode_base' => 'greenpath_core_banner',
				)
			);
			if ( $widget_mapped ) {
				$this->set_base( 'greenpath_core_banner' );
				$this->set_name( esc_html__( 'Nutterlygood Banner', 'greenpath-core' ) );
				$this->set_description( esc_html__( 'Add a banner element into widget areas', 'greenpath-core' ) );
			}
		}

		public function render( $atts ) {
			echo GreenPathCore_Banner_Shortcode::call_shortcode( $atts ); // XSS OK
		}
	}
}
