<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$ids = array( 7515, 7517, 7519, 7521, 7523, 7525, 7527, 7529, 7537, 7547, 7551 );
foreach ( $ids as $id ) {
	$p = get_post( $id );
	echo $id . ' | ' . ( $p ? $p->post_title : 'missing' ) . PHP_EOL;
}