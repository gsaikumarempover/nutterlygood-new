<?php
require __DIR__ . '/wp-load.php';

$shop_id = (int) get_option( 'woocommerce_shop_page_id' );
echo "shop_id={$shop_id}\n";
echo 'coming_soon=' . get_option( 'woocommerce_coming_soon', '(missing)' ) . "\n";
echo 'store_pages_only=' . get_option( 'woocommerce_store_pages_only', '(missing)' ) . "\n";

$post = get_post( $shop_id );
if ( $post ) {
	echo 'post_content=' . substr( $post->post_content, 0, 500 ) . "\n";
	echo 'elementor=' . get_post_meta( $shop_id, '_elementor_data', true ) ? 'yes' : 'no';
	echo "\n";
}

$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
echo "categories:\n";
foreach ( $terms as $t ) {
	echo "- {$t->slug} ({$t->name})\n";
}