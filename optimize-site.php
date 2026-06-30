<?php
/**
 * One-time site optimization.
 */
require __DIR__ . '/wp-load.php';

// Deactivate unused Qi Blocks plugin (not referenced in Elementor data).
$active = get_option( 'active_plugins', array() );
$remove = 'qi-blocks/class-qi-blocks.php';
if ( in_array( $remove, $active, true ) ) {
	$active = array_values( array_diff( $active, array( $remove ) ) );
	update_option( 'active_plugins', $active );
	echo "Deactivated qi-blocks plugin.\n";
} else {
	echo "qi-blocks already inactive.\n";
}

// Disable all Qi Addons widgets except those used on site.
global $wpdb;
$rows  = $wpdb->get_col( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key='_elementor_data' AND meta_value LIKE '%\"widgetType\":\"qi_%'" );
$used  = array();
$walk  = static function ( $nodes ) use ( &$walk, &$used ) {
	foreach ( $nodes as $node ) {
		if ( ! empty( $node['widgetType'] ) && 0 === strpos( $node['widgetType'], 'qi_' ) ) {
			$used[ $node['widgetType'] ] = true;
		}
		if ( ! empty( $node['elements'] ) ) {
			$walk( $node['elements'] );
		}
	}
};
foreach ( $rows as $json ) {
	$data = json_decode( $json, true );
	if ( is_array( $data ) ) {
		$walk( $data );
	}
}

$disabled = array();
if ( defined( 'QI_ADDONS_FOR_ELEMENTOR_SHORTCODES_PATH' ) ) {
	foreach ( glob( QI_ADDONS_FOR_ELEMENTOR_SHORTCODES_PATH . '/*', GLOB_ONLYDIR ) as $dir ) {
		$folder = basename( $dir );
		$slug   = 'qi_' . str_replace( '-', '_', $folder );
		$found  = false;
		foreach ( array_keys( $used ) as $widget ) {
			if ( 0 === strpos( $widget, $slug ) ) {
				$found = true;
				break;
			}
		}
		if ( ! $found ) {
			$disabled[] = $folder;
		}
	}
}

if ( defined( 'QI_ADDONS_FOR_ELEMENTOR_DISABLED_WIDGETS' ) ) {
	update_option( QI_ADDONS_FOR_ELEMENTOR_DISABLED_WIDGETS, array_values( array_unique( $disabled ) ) );
	echo 'Disabled ' . count( $disabled ) . " unused Qi Addons widget packs.\n";
}

wp_cache_flush();
echo "Done. Restart Apache in XAMPP to apply OPcache.\n";