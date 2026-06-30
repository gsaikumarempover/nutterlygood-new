<?php
require __DIR__ . '/wp-load.php';

global $wp_registered_sidebars;
echo "Sidebars:\n";
foreach ( $wp_registered_sidebars as $id => $sb ) {
	echo "- {$id}: {$sb['name']}\n";
}

$widget_areas = array(
	'sidebar-1',
	'qodef-product-list-sidebar-widget-area',
	'qodef-product-list-side-area-widget-area',
);

foreach ( $widget_areas as $area ) {
	echo "\nWidgets in {$area}:\n";
	$widgets = wp_get_sidebars_widgets();
	if ( empty( $widgets[ $area ] ) ) {
		echo "(empty)\n";
		continue;
	}
	foreach ( $widgets[ $area ] as $w ) {
		echo "- {$w}\n";
	}
}

$menu = wp_get_nav_menu_items( 'Footer Menu 1' );
echo "\nChecking main menu for Shop link...\n";
$menus = wp_get_nav_menus();
foreach ( $menus as $m ) {
	$items = wp_get_nav_menu_items( $m->term_id );
	if ( ! $items ) {
		continue;
	}
	foreach ( $items as $item ) {
		if ( stripos( $item->title, 'shop' ) !== false ) {
			echo "{$m->name}: {$item->title} -> {$item->url}\n";
		}
	}
}