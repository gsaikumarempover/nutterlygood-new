<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';
print_r( get_nav_menu_locations() );
foreach ( wp_get_nav_menus() as $m ) {
	echo $m->term_id . ' => ' . $m->name . PHP_EOL;
}