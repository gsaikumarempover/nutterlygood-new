<?php

class GreenPathCore_Product_Cart_Showcase_Shortcode_Elementor extends GreenPathCore_Elementor_Widget_Base {

	public function __construct( array $data = [], $args = null ) {
		$this->set_shortcode_slug( 'greenpath_core_product_cart_showcase' );

		parent::__construct( $data, $args );
	}
}

if ( qode_framework_is_installed( 'woocommerce' ) ) {
	greenpath_core_register_new_elementor_widget( new GreenPathCore_Product_Cart_Showcase_Shortcode_Elementor() );
}
