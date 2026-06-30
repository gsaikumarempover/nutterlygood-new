<?php
require dirname( __DIR__, 4 ) . '/wp-load.php';
foreach ( get_post_meta( 3431 ) as $k => $v ) {
	if ( strpos( $k, 'title' ) !== false || strpos( $k, 'qodef_page' ) !== false ) {
		echo $k . ' => ' . ( is_array( $v ) ? $v[0] : $v ) . "\n";
	}
}