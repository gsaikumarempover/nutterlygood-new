<?php
error_reporting( E_ALL );
ini_set( 'display_errors', '1' );
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/nutterlyGood/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['HTTPS'] = 'off';
$_SERVER['SERVER_PORT'] = '80';

define( 'WP_USE_THEMES', true );
$t0 = microtime( true );
ob_start();
require __DIR__ . '/wp-blog-header.php';
$html = ob_get_clean();
$headers = headers_list();
echo 'Time: ' . round( microtime( true ) - $t0, 2 ) . "s\n";
echo 'Bytes: ' . strlen( $html ) . "\n";
echo "Headers:\n" . implode( "\n", $headers ) . "\n";
if ( strlen( $html ) < 500 ) {
	echo "Body:\n$html\n";
}