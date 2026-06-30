<?php

class GreenPathCore_Blog_List_Shortcode_Elementor extends GreenPathCore_Elementor_Widget_Base {

	public function __construct( array $data = [], $args = null ) {
		$this->set_shortcode_slug( 'greenpath_core_blog_list' );

		parent::__construct( $data, $args );
	}
}

greenpath_core_register_new_elementor_widget( new GreenPathCore_Blog_List_Shortcode_Elementor() );
