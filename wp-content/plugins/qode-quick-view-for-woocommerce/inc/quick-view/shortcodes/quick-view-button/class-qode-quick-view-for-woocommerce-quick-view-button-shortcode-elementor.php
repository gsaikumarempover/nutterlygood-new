<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
class Qode_Quick_View_For_WooCommerce_Quick_View_Button_Shortcode_Elementor extends Qode_Quick_View_For_WooCommerce_Elementor_Widget_Base {

	public function __construct( array $data = array(), $args = null ) {
		$this->set_shortcode_slug( 'qode_quick_view_for_woocommerce_button' );

		parent::__construct( $data, $args );
	}
}

qode_quick_view_for_woocommerce_register_new_elementor_widget( new Qode_Quick_View_For_WooCommerce_Quick_View_Button_Shortcode_Elementor() );
