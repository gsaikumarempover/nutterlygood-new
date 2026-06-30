<?php
$data = json_decode( file_get_contents( __DIR__ . '/export/revslider/main-home-extracted/slider_export.txt' ), true );
$slide = $data['slides'][0];
echo "Slider height: " . ( $data['params']['size']['height']['d'] ?? '' ) . PHP_EOL;
foreach ( $slide['layers'] as $id => $layer ) {
	$type = $layer['type'] ?? ( $layer['subtype'] ?? 'text' );
	$text = isset( $layer['text'] ) ? substr( $layer['text'], 0, 40 ) : '';
	$y    = $layer['position']['y']['d']['v'] ?? '';
	$x    = $layer['position']['x']['d']['v'] ?? '';
	$vh   = $layer['position']['vertical']['d']['v'] ?? '';
	$hh   = $layer['position']['horizontal']['d']['v'] ?? '';
	echo "Layer $id ($type): text='$text' x=$x y=$y h=$hh v=$vh" . PHP_EOL;
}