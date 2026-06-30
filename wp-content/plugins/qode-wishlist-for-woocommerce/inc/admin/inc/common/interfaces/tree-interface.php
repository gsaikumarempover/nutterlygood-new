<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

interface Qode_Wishlist_For_WooCommerce_Framework_Tree_Interface {
	public function has_children();

	public function get_children();

	public function get_child( $key );

	public function add_child( Qode_Wishlist_For_WooCommerce_Framework_Child_Interface $field );
}
