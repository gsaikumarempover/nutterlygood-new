<?php
require dirname( __DIR__, 4 ) . '/wp-load.php';
delete_option( 'ng_farmley_header_sync_v1' );
if ( function_exists( 'nuttergood_farmley_sync_header_meta_all_pages' ) ) {
	nuttergood_farmley_sync_header_meta_all_pages();
}
require __DIR__ . '/compare-header.php';