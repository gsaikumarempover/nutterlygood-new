<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';
$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
foreach ( $terms as $t ) {
	if ( $t->count > 0 ) {
		echo $t->slug . ' parent=' . $t->parent . ' count=' . $t->count . PHP_EOL;
	}
}
echo PHP_EOL . 'dry-fruits children:' . PHP_EOL;
$df = get_term_by( 'slug', 'dry-fruits', 'product_cat' );
$children = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $df->term_id, 'hide_empty' => false ) );
foreach ( $children as $c ) {
	echo '  ' . $c->slug . ' count=' . $c->count . PHP_EOL;
}