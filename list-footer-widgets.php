<?php
require __DIR__ . '/wp-load.php';

$sidebars = wp_get_sidebars_widgets();
$targets  = array(
	'qodef-footer-top-area-column-1',
	'qodef-footer-top-area-column-2',
	'qodef-footer-top-area-column-3',
	'qodef-footer-top-area-column-4',
	'qodef-footer-bottom-area-column-1',
	'qodef-footer-bottom-area-column-2',
);

foreach ( $targets as $sb ) {
	echo "=== {$sb} ===\n";
	if ( ! empty( $sidebars[ $sb ] ) ) {
		foreach ( $sidebars[ $sb ] as $w ) {
			echo $w . "\n";
		}
	} else {
		echo "(none)\n";
	}
}