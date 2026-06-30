<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$page_id = (int) get_option( 'page_on_front' );
$raw     = get_post_meta( $page_id, '_elementor_data', true );
$data    = json_decode( $raw, true );

function ng_find_element( $els, $id ) {
	foreach ( $els as $el ) {
		if ( ( $el['id'] ?? '' ) === $id ) {
			return $el;
		}
		if ( ! empty( $el['elements'] ) ) {
			$found = ng_find_element( $el['elements'], $id );
			if ( $found ) {
				return $found;
			}
		}
	}
	return null;
}

$widget = ng_find_element( $data, 'bf23057' );
echo "Widget settings:\n";
echo json_encode( $widget['settings'] ?? array(), JSON_PRETTY_PRINT ) . "\n\n";

$slugs = array( 'dry-fruits', 'chips', 'mixes', 'brittles', 'mouth-fresheners' );
foreach ( $slugs as $slug ) {
	$t = get_term_by( 'slug', $slug, 'product_cat' );
	echo $slug . ': ' . ( $t ? $t->name . ' (id ' . $t->term_id . ', parent ' . $t->parent . ')' : 'MISSING' ) . "\n";
}