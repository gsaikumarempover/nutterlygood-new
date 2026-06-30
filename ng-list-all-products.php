<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$q = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'ID', 'order' => 'DESC' ) );
while ( $q->have_posts() ) {
	$q->the_post();
	$p = wc_get_product( get_the_ID() );
	$meta = function_exists( 'nuttergood_farmley_get_product_meta' ) ? nuttergood_farmley_get_product_meta( $p ) : array();
	$weight = '';
	if ( ! empty( $meta['sizes'][0]['weight'] ) ) {
		$weight = $meta['sizes'][0]['weight'];
	} elseif ( ! empty( $meta['sizes'][0]['label'] ) ) {
		$weight = $meta['sizes'][0]['label'];
	}
	echo get_the_ID() . '|' . get_the_title() . '|' . $p->get_slug() . '|weight=' . $weight . '|thumb=' . $p->get_image_id() . '|gallery=' . implode( ',', $p->get_gallery_image_ids() ) . PHP_EOL;
}
echo 'TOTAL=' . (int) $q->post_count . PHP_EOL;