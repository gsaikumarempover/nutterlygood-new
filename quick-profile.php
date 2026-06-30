<?php
define( 'SAVEQUERIES', true );
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/nutterlyGood/';
$_SERVER['REQUEST_METHOD'] = 'GET';

$t0 = microtime( true );
require __DIR__ . '/wp-load.php';
$t1 = microtime( true );

$front_id = (int) get_option( 'page_on_front' );
$t2 = microtime( true );

if ( $front_id ) {
	$data = get_post_meta( $front_id, '_elementor_data', true );
	$el_size = is_string( $data ) ? strlen( $data ) : 0;
} else {
	$el_size = 0;
}
$t3 = microtime( true );

global $wpdb;
echo 'wp-load: ' . round( $t1 - $t0, 2 ) . "s\n";
echo 'front meta: ' . round( $t3 - $t2, 2 ) . "s (elementor json: {$el_size} bytes)\n";
echo 'queries after load: ' . count( $wpdb->queries ?? array() ) . "\n";
echo 'active plugins: ' . count( get_option( 'active_plugins', array() ) ) . "\n";