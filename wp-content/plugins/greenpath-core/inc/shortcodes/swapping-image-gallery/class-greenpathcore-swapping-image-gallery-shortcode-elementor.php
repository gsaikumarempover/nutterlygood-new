<?php

class GreenPathCore_Swapping_Image_Gallery_Shortcode_Elementor extends GreenPathCore_Elementor_Widget_Base {

	public function __construct( array $data = [], $args = null ) {
		$this->set_shortcode_slug( 'greenpath_core_swapping_image_gallery' );

		parent::__construct( $data, $args );
	}
}

greenpath_core_register_new_elementor_widget( new GreenPathCore_Swapping_Image_Gallery_Shortcode_Elementor() );
