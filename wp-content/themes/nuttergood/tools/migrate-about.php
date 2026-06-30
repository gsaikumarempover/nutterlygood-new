<?php
require dirname( __DIR__, 4 ) . '/wp-load.php';
delete_option( 'ng_farmley_about_layout_v1' );
if ( function_exists( 'nuttergood_farmley_setup_about_template' ) ) {
	nuttergood_farmley_setup_about_template();
}
$page = get_page_by_path( 'about-us' );
if ( $page ) {
	echo 'template: ' . get_page_template_slug( $page->ID ) . "\n";
	echo 'elementor: ' . get_post_meta( $page->ID, '_elementor_edit_mode', true ) . "\n";
	echo 'title enabled: ' . get_post_meta( $page->ID, 'qodef_enable_page_title', true ) . "\n";
}