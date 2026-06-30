<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

// Set additional hook for 3rd party elements.
do_action( 'qode_wishlist_for_woocommerce_action_before_add_to_wishlist_content', $params );

qode_wishlist_for_woocommerce_template_part( 'wishlist/shortcodes/add-to-wishlist', 'templates/parts/spinner', '', $params );

if ( 'text' !== $button_type ) {
	qode_wishlist_for_woocommerce_template_part( 'wishlist/shortcodes/add-to-wishlist', 'templates/parts/icon', '', $params );
}

if ( in_array( $button_type, array( 'text', 'icon-with-text' ), true ) ) {
	qode_wishlist_for_woocommerce_template_part( 'wishlist/shortcodes/add-to-wishlist', 'templates/parts/label', '', $params );
}

// Set additional hook for 3rd party elements.
do_action( 'qode_wishlist_for_woocommerce_action_after_add_to_wishlist_content', $params );
