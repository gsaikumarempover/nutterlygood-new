<?php

class GreenPathCore_Instagram_List_Shortcode_Elementor extends GreenPathCore_Elementor_Widget_Base {

	public function __construct( array $data = [], $args = null ) {
		$this->set_shortcode_slug( 'greenpath_core_instagram_list' );

		parent::__construct( $data, $args );
	}
}

if ( qode_framework_is_installed( 'instagram' ) ) {
	greenpath_core_register_new_elementor_widget( new GreenPathCore_Instagram_List_Shortcode_Elementor() );
}
