<?php

if ( ! function_exists( 'greenpath_core_add_blog_list_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function greenpath_core_add_blog_list_widget( $widgets ) {
		$widgets[] = 'GreenPathCore_Blog_List_Widget';

		return $widgets;
	}

	add_filter( 'greenpath_core_filter_register_widgets', 'greenpath_core_add_blog_list_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathCore_Blog_List_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_widget_option(
				array(
					'field_type' => 'text',
					'name'       => 'widget_title',
					'title'      => esc_html__( 'Title', 'greenpath-core' ),
				)
			);
			$widget_mapped = $this->import_shortcode_options(
				array(
					'shortcode_base' => 'greenpath_core_blog_list',
				)
			);

			if ( $widget_mapped ) {
				$this->set_base( 'greenpath_core_blog_list' );
				$this->set_name( esc_html__( 'Nutterlygood Blog List', 'greenpath-core' ) );
				$this->set_description( esc_html__( 'Display a list of blog posts', 'greenpath-core' ) );
			}
		}

		public function render( $atts ) {
			$atts['is_widget_element'] = 'yes';

			echo GreenPathCore_Blog_List_Shortcode::call_shortcode( $atts ); // XSS OK
		}
	}
}
