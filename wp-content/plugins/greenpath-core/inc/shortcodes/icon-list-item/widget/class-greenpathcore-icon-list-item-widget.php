<?php

if ( ! function_exists( 'greenpath_core_add_icon_list_item_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function greenpath_core_add_icon_list_item_widget( $widgets ) {
		$widgets[] = 'GreenPathCore_Icon_List_Item_Widget';

		return $widgets;
	}

	add_filter( 'greenpath_core_filter_register_widgets', 'greenpath_core_add_icon_list_item_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathCore_Icon_List_Item_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$widget_mapped = $this->import_shortcode_options(
				array(
					'shortcode_base' => 'greenpath_core_icon_list_item',
				)
			);
			if ( $widget_mapped ) {
				$this->set_base( 'greenpath_core_icon_list_item' );
				$this->set_name( esc_html__( 'Nutterlygood Icon List Item', 'greenpath-core' ) );
				$this->set_description( esc_html__( 'Add a icon list item element into widget areas', 'greenpath-core' ) );
			}
		}

		public function render( $atts ) {
			echo GreenPathCore_Icon_List_Item_Shortcode::call_shortcode( $atts ); // XSS OK
		}
	}
}
