<?php
$raw = file_get_contents( __DIR__ . '/export/home-elementor-36.json' );
$elements = json_decode( $raw, true );
$walk = function ( $nodes ) use ( &$walk ) {
	foreach ( $nodes as $el ) {
		if ( ( $el['id'] ?? '' ) === '1ea2b9b' ) {
			echo json_encode( $el['settings'] ?? array(), JSON_PRETTY_PRINT );
			return;
		}
		if ( ! empty( $el['elements'] ) ) {
			$walk( $el['elements'] );
		}
	}
};
$walk( $elements );