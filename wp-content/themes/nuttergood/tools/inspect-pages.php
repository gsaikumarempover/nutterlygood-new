<?php
require dirname( __DIR__, 4 ) . '/wp-load.php';
foreach ( array( 3431, 3437 ) as $id ) {
	$p = get_post( $id );
	echo "=== {$p->post_title} ({$id}) ===\n";
	echo 'template: ' . get_page_template_slug( $id ) . "\n";
	echo 'elementor: ' . get_post_meta( $id, '_elementor_edit_mode', true ) . "\n";
	echo 'title meta bg: ' . get_post_meta( $id, 'qodef_page_title_background_image', true ) . "\n";
	echo substr( strip_tags( $p->post_content ), 0, 400 ) . "\n\n";
}