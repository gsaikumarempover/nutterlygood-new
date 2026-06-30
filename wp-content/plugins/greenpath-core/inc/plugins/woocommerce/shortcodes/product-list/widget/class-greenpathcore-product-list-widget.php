<?php

if ( ! function_exists( 'greenpath_core_add_product_list_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function greenpath_core_add_product_list_widget( $widgets ) {
		$widgets[] = 'GreenPathCore_Product_List_Widget';

		return $widgets;
	}

	add_filter( 'greenpath_core_filter_register_widgets', 'greenpath_core_add_product_list_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathCore_Product_List_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$widget_mapped = $this->import_shortcode_options(
				array(
					'shortcode_base' => 'greenpath_core_product_list',
					'exclude'        => array(
						'enable_custom_filter',
						'filter_type',
						'advanced_filter_type',
						'enable_grid_filter',
						'enable_ordering_filter',
						'first_attribute_filter',
						'second_attribute_filter',
					),
				)
			);

			if ( $widget_mapped ) {
				$this->set_base( 'greenpath_core_product_list' );
				$this->set_name( esc_html__( 'Nutterlygood Product List', 'greenpath-core' ) );
				$this->set_description( esc_html__( 'Display a list of products', 'greenpath-core' ) );
			}
		}

		public function render( $atts ) {
			$atts['is_widget_element'] = 'yes';

			echo GreenPathCore_Product_List_Shortcode::call_shortcode( $atts ); // XSS OK
		}
	}
}
