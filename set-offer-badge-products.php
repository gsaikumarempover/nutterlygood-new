<?php
/**
 * Mark a small set of products to show offer badges on list cards.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

if ( ! function_exists( 'nuttergood_farmley_default_offer_badge_product_ids' ) ) {
	echo "Run from site with nuttergood theme active.\n";
	exit( 1 );
}

$ids = nuttergood_farmley_default_offer_badge_product_ids();

foreach ( $ids as $id ) {
	$id = (int) $id;
	$p  = get_post( $id );
	if ( ! $p || 'product' !== $p->post_type ) {
		echo "Skip missing product: $id\n";
		continue;
	}
	update_post_meta( $id, '_ng_show_offer_badge', 'yes' );
	echo "Badge enabled: $id — {$p->post_title}\n";
}

echo "Done. " . count( $ids ) . " products flagged.\n";