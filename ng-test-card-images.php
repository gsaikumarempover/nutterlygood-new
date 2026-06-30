<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

$errors = array();
set_error_handler(
	function ( $errno, $errstr, $errfile, $errline ) use ( &$errors ) {
		if ( str_contains( $errfile, 'media.php' ) ) {
			$errors[] = "$errstr in $errfile:$errline";
		}
		return false;
	}
);

$q = new WP_Query(
	array(
		'post_type'      => 'product',
		'posts_per_page' => 12,
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => 'dry-fruits',
			),
		),
	)
);

$params = array(
	'image_dimension' => array(
		'size'  => 'full',
		'class' => 'qodef-list-image',
	),
);

while ( $q->have_posts() ) {
	$q->the_post();
	$p = wc_get_product( get_the_ID() );
	ob_start();
	nuttergood_farmley_render_product_card_media( $params );
	ob_end_clean();
}

wp_reset_postdata();
restore_error_handler();

if ( empty( $errors ) ) {
	echo "OK: no media.php warnings for " . (int) $q->post_count . " products\n";
} else {
	echo implode( "\n", $errors ) . "\n";
}