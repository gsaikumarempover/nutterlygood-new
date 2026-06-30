<?php

class GreenPathCore_Image_Marquee_Shortcode_Elementor extends GreenPathCore_Elementor_Widget_Base {

	public function __construct( array $data = [], $args = null ) {
		$this->set_shortcode_slug( 'greenpath_core_image_marquee' );

		parent::__construct( $data, $args );
	}
}

greenpath_core_register_new_elementor_widget( new GreenPathCore_Image_Marquee_Shortcode_Elementor() );
