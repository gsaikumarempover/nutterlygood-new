<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

$plugins = array(
	'qode-compare-for-woocommerce/class-qode-compare-for-woocommerce.php',
	'qode-wishlist-for-woocommerce/class-qode-wishlist-for-woocommerce.php',
);

foreach ( $plugins as $file ) {
	if ( ! file_exists( WP_PLUGIN_DIR . '/' . $file ) ) {
		echo "MISSING: $file\n";
		continue;
	}
	if ( is_plugin_active( $file ) ) {
		echo "Already active: $file\n";
	} else {
		$r = activate_plugin( $file );
		echo is_wp_error( $r ) ? "ERROR $file: " . $r->get_error_message() . "\n" : "Activated: $file\n";
	}
}

wp_cache_flush();
echo "Done.\n";