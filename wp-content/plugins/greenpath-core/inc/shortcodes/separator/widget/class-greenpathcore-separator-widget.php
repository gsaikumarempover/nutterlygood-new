<?php

if ( ! function_exists( 'greenpath_core_add_separator_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function greenpath_core_add_separator_widget( $widgets ) {
		$widgets[] = 'GreenPathCore_Separator_Widget';

		return $widgets;
	}

	add_filter( 'greenpath_core_filter_register_widgets', 'greenpath_core_add_separator_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathCore_Separator_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$widget_mapped = $this->import_shortcode_options(
				array(
					'shortcode_base' => 'greenpath_core_separator',
				)
			);

			if ( $widget_mapped ) {
				$this->set_base( 'greenpath_core_separator' );
				$this->set_name( esc_html__( 'Nutterlygood Separator', 'greenpath-core' ) );
				$this->set_description( esc_html__( 'Add a separator element into widget areas', 'greenpath-core' ) );
			}
		}

		public function render( $atts ) {
			echo GreenPathCore_Separator_Shortcode::call_shortcode( $atts ); // XSS OK
		}
	}
}
