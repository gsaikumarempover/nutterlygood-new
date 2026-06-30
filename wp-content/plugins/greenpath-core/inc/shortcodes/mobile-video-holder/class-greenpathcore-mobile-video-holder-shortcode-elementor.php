<?php

class GreenPathCore_Mobile_Video_Holder_Shortcode_Elementor extends GreenPathCore_Elementor_Widget_Base {
	function __construct( $data = array(), $args = null ) {
		$this->set_shortcode_slug( 'greenpath_core_mobile_video_holder' );

		parent::__construct( $data, $args );
	}
}

greenpath_core_register_new_elementor_widget( new GreenPathCore_Mobile_Video_Holder_Shortcode_Elementor() );
