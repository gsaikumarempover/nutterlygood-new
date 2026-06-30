<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$export = json_decode( file_get_contents( __DIR__ . '/export/home-elementor-36.json' ), true );
$live   = json_decode( get_post_meta( 36, '_elementor_data', true ), true );

function find_el( $items, $id ) {
	foreach ( $items as $el ) {
		if ( ( $el['id'] ?? '' ) === $id ) {
			return $el;
		}
		if ( ! empty( $el['elements'] ) ) {
			$f = find_el( $el['elements'], $id );
			if ( $f ) {
				return $f;
			}
		}
	}
	return null;
}

foreach ( array( '47bd4b6', '76f78af', 'abca240', 'a463981', 'bf23057' ) as $id ) {
	echo "=== $id EXPORT ===\n";
	$el = find_el( $export, $id );
	echo json_encode( $el ? ( $el['settings'] ?? array() ) : 'NOT FOUND', JSON_PRETTY_PRINT ) . "\n\n";
	echo "=== $id LIVE ===\n";
	$el = find_el( $live, $id );
	echo json_encode( $el ? ( $el['settings'] ?? array() ) : 'NOT FOUND', JSON_PRETTY_PRINT ) . "\n\n";
}