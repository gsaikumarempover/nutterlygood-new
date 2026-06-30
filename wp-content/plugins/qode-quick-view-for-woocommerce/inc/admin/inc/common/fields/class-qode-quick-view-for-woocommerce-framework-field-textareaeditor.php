<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

class Qode_Quick_View_For_WooCommerce_Framework_Field_Textareaeditor extends Qode_Quick_View_For_WooCommerce_Framework_Field_Type {

	public function render_field() {

		$settings = array(
			'media_buttons'  => false,
			'textarea_rows'  => 2,
			'editor_class'   => 'qode-quick-view-for-woocommerce-textarea-editor',
			'default_editor' => 'tinymce',
		);
		wp_editor( $this->params['value'], $this->name, $settings );
	}
}
