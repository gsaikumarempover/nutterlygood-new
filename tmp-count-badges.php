<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$q = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => -1, 'fields' => 'ids' ) );
$with = 0;
foreach ( $q->posts as $id ) {
	$product = wc_get_product( $id );
	if ( ! $product ) {
		continue;
	}
	$d = nuttergood_farmley_get_badge_discount_percent( $product );
	if ( $d > 0 ) {
		++$with;
		echo $id . ' | ' . $product->get_name() . ' | ' . $d . "%\n";
	}
}
echo "Total with badges: $with\n";