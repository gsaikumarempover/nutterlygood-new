<?php

if ( ! function_exists( 'greenpath_core_add_twitter_list_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function greenpath_core_add_twitter_list_widget( $widgets ) {
		if ( qode_framework_is_installed( 'twitter' ) ) {
			$widgets[] = 'GreenPathCore_Twitter_List_Widget';
		}

		return $widgets;
	}

	add_filter( 'greenpath_core_filter_register_widgets', 'greenpath_core_add_twitter_list_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathCore_Twitter_List_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_widget_option(
				array(
					'name'       => 'widget_title',
					'field_type' => 'text',
					'title'      => esc_html__( 'Title', 'greenpath-core' ),
				)
			);
			$widget_mapped = $this->import_shortcode_options(
				array(
					'shortcode_base' => 'greenpath_core_twitter_list',
				)
			);
			if ( $widget_mapped ) {
				$this->set_base( 'greenpath_core_twitter_list' );
				$this->set_name( esc_html__( 'Nutterlygood Twitter List', 'greenpath-core' ) );
				$this->set_description( esc_html__( 'Add a twitter list element into widget areas', 'greenpath-core' ) );
			}
		}

		public function render( $atts ) {
			echo GreenPathCore_Twitter_List_Shortcode::call_shortcode( $atts ); // XSS OK
		}
	}
}
