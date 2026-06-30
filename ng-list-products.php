<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$q = new WP_Query(
	array(
		'post_type'      => 'product',
		'posts_per_page' => 8,
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => 'dry-fruits',
			),
		),
	)
);

while ( $q->have_posts() ) {
	$q->the_post();
	$p   = wc_get_product( get_the_ID() );
	$meta = function_exists( 'nuttergood_farmley_get_product_meta' ) ? nuttergood_farmley_get_product_meta( $p ) : array();
	echo get_the_ID() . '|' . get_the_title() . '|weight=' . $p->get_weight() . '|sizes=' . wp_json_encode( $meta['sizes'] ?? array() ) . '|img=' . $p->get_image_id() . PHP_EOL;
}