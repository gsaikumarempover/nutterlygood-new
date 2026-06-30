<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

require_once QODE_WISHLIST_FOR_WOOCOMMERCE_ADMIN_PATH . '/inc/shortcodes/class-qode-wishlist-for-woocommerce-framework-shortcodes.php';
require_once QODE_WISHLIST_FOR_WOOCOMMERCE_ADMIN_PATH . '/inc/shortcodes/class-qode-wishlist-for-woocommerce-framework-shortcode.php';

foreach ( glob( QODE_WISHLIST_FOR_WOOCOMMERCE_ADMIN_PATH . '/inc/shortcodes/translators/*/*-translator.php' ) as $translator ) {
	require_once $translator;
}
