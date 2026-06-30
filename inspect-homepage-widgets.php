<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';
$data = json_decode( get_post_meta( (int) get_option( 'page_on_front' ), '_elementor_data', true ), true );
$walk = function ( $items, $depth = 0 ) use ( &$walk ) {
	foreach ( $items as $el ) {
		$w = $el['widgetType'] ?? '';
		if ( $w ) {
			echo str_repeat( ' ', $depth * 2 ) . $w . ' id=' . ( $el['id'] ?? '' ) . PHP_EOL;
			$s = $el['settings'] ?? array();
			foreach ( array( 'title', 'behavior', 'tax_slug', 'use_alternate_image', 'taxonomy_slugs', 'enable_filter', 'enable_custom_filter', 'posts_per_page', 'columns', 'space', 'layout' ) as $k ) {
				if ( ! empty( $s[ $k ] ) ) {
					echo str_repeat( ' ', $depth * 2 ) . "  $k: " . ( is_array( $s[ $k ] ) ? json_encode( $s[ $k ] ) : $s[ $k ] ) . PHP_EOL;
				}
			}
		}
		if ( ! empty( $el['elements'] ) ) {
			$walk( $el['elements'], $depth + 1 );
		}
	}
};
$walk( $data );