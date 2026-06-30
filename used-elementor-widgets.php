<?php
require __DIR__ . '/wp-load.php';
$front_id = (int) get_option( 'page_on_front' );
$data     = get_post_meta( $front_id, '_elementor_data', true );
$json     = json_decode( $data, true );
$widgets  = array();

$walk = static function ( $nodes ) use ( &$walk, &$widgets ) {
	if ( ! is_array( $nodes ) ) {
		return;
	}
	foreach ( $nodes as $node ) {
		if ( ! empty( $node['widgetType'] ) ) {
			$widgets[ $node['widgetType'] ] = ( $widgets[ $node['widgetType'] ] ?? 0 ) + 1;
		}
		if ( ! empty( $node['elements'] ) ) {
			$walk( $node['elements'] );
		}
	}
};
$walk( $json );
arsort( $widgets );
echo "Homepage Elementor widgets (" . count( $widgets ) . " types):\n";
foreach ( $widgets as $w => $c ) {
	echo "  $w x$c\n";
}