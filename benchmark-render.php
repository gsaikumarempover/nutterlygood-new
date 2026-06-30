<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/nutterlyGood/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SERVER_NAME'] = 'localhost';

$t0 = microtime( true );
ob_start();
require __DIR__ . '/wp-blog-header.php';
$html = ob_get_clean();
echo round( microtime( true ) - $t0, 2 ) . 's ' . number_format( strlen( $html ) ) . " bytes\n";