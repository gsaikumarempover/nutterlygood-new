<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';
$active = get_option( 'active_plugins', array() );
echo "Active plugins:\n";
foreach ( $active as $p ) {
	echo "  - $p\n";
}
echo "\nAll plugin dirs:\n";
foreach ( glob( WP_PLUGIN_DIR . '/*', GLOB_ONLYDIR ) as $dir ) {
	echo '  - ' . basename( $dir ) . PHP_EOL;
}