<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';
$data = json_decode( get_post_meta( (int) get_option( 'page_on_front' ), '_elementor_data', true ), true );
$walk = function ( $items ) use ( &$walk ) {
	foreach ( $items as $el ) {
		if ( ( $el['id'] ?? '' ) === '3a8d545' ) {
			echo wp_json_encode( $el['settings'], JSON_PRETTY_PRINT ) . PHP_EOL;
		}
		if ( ! empty( $el['elements'] ) ) {
			$walk( $el['elements'] );
		}
	}
};
$walk( $data );

echo PHP_EOL . 'mixes child terms:' . PHP_EOL;
$mixes = get_term_by( 'slug', 'mixes', 'product_cat' );
$children = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $mixes->term_id, 'hide_empty' => false ) );
foreach ( $children as $t ) {
	echo $t->slug . ' | ' . $t->name . ' | count=' . $t->count . PHP_EOL;
}
echo 'top-level cats:' . PHP_EOL;
$tops = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => false ) );
foreach ( $tops as $t ) {
	echo $t->slug . ' | ' . $t->name . ' | count=' . $t->count . PHP_EOL;
}