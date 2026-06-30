<?php

if ( ! function_exists( 'greenpath_core_add_icon_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function greenpath_core_add_icon_widget( $widgets ) {
		$widgets[] = 'GreenPathCore_Icon_Widget';

		return $widgets;
	}

	add_filter( 'greenpath_core_filter_register_widgets', 'greenpath_core_add_icon_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathCore_Icon_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$widget_mapped = $this->import_shortcode_options(
				array(
					'shortcode_base' => 'greenpath_core_icon',
				)
			);

			if ( $widget_mapped ) {
				$this->set_base( 'greenpath_core_icon' );
				$this->set_name( esc_html__( 'Nutterlygood Icon', 'greenpath-core' ) );
				$this->set_description( esc_html__( 'Add a icon element into widget areas', 'greenpath-core' ) );
			}
		}

		public function render( $atts ) {
			echo GreenPathCore_Icon_Shortcode::call_shortcode( $atts ); // XSS OK
		}
	}
}
