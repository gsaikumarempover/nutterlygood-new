<?php
$raw = file_get_contents( __DIR__ . '/export/home-elementor-36.json' );
$elements = json_decode( $raw, true );
$walk = function ( $nodes ) use ( &$walk ) {
	foreach ( $nodes as $el ) {
		if ( ( $el['id'] ?? '' ) === 'ca3823c' ) {
			echo json_encode( $el, JSON_PRETTY_PRINT );
			return;
		}
		if ( ! empty( $el['elements'] ) ) {
			$walk( $el['elements'] );
		}
	}
};
$walk( $elements );