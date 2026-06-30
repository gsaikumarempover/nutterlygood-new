<?php

if ( ! function_exists( 'greenpath_core_add_simple_blog_list_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function greenpath_core_add_simple_blog_list_widget( $widgets ) {
		$widgets[] = 'GreenPathCore_Simple_Blog_List_Widget';

		return $widgets;
	}

	add_filter( 'greenpath_core_filter_register_widgets', 'greenpath_core_add_simple_blog_list_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathCore_Simple_Blog_List_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_widget_option(
				array(
					'field_type' => 'text',
					'name'       => 'widget_title',
					'title'      => esc_html__( 'Title', 'greenpath-core' ),
				)
			);

			$this->set_widget_option(
				array(
					'field_type' => 'select',
					'name'       => 'layout',
					'title'      => esc_html__( 'Item Layout', 'greenpath-core' ),
					'options'    => apply_filters( 'greenpath_core_filter_simple_blog_list_widget_layouts', array() ),
				)
			);

			$widget_mapped = $this->import_shortcode_options(
				array(
					'shortcode_base' => 'greenpath_core_blog_list',
					'exclude'        => array(
						'custom_class',
						'behavior',
						'space',
						'vertical_space',
						'masonry_images_proportion',
						'images_proportion',
						'custom_image_width',
						'custom_image_height',
						'columns',
						'columns_responsive',
						'columns_1512',
						'columns_1368',
						'columns_1200',
						'columns_1024',
						'columns_880',
						'columns_680',
						'slider_loop',
						'slider_autoplay',
						'slider_speed',
						'slider_speed_animation',
						'slider_navigation',
						'slider_pagination',
						'layout',
						'excerpt_length',
						'enable_filter',
						'pagination_type',
						'pagination_top_margin',
					),
				)
			);

			if ( $widget_mapped ) {
				$this->set_base( 'greenpath_core_simple_blog_list' );
				$this->set_name( esc_html__( 'Nutterlygood Simple Blog List', 'greenpath-core' ) );
				$this->set_description( esc_html__( 'Display a list of blog posts', 'greenpath-core' ) );
			}
		}

		public function render( $atts ) {
			$atts['is_widget_element'] = 'yes';

			// force atts
			$atts['behavior']            = 'columns';
			$atts['space']               = 'no'; // spacing inherited from widgets map
			$atts['vertical_space']      = 'no'; // spacing inherited from widgets map
			$atts['images_proportion']   = 'custom';
			$atts['custom_image_width']  = 111;
			$atts['custom_image_height'] = 77;
			$atts['columns']             = 1;
			$atts['columns_responsive']  = 'predefined';

			echo GreenPathCore_Blog_List_Shortcode::call_shortcode( $atts ); // XSS OK
		}
	}
}
