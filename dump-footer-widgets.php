<?php
require __DIR__ . '/wp-load.php';

$widgets = get_option( 'widget_greenpath_core_single_image' );
echo "=== single_image ===\n";
print_r( $widgets );

$widgets = get_option( 'widget_greenpath_core_icon' );
echo "=== icon ===\n";
print_r( $widgets );

$widgets = get_option( 'widget_greenpath_core_svg_icon' );
echo "=== svg_icon ===\n";
print_r( $widgets );

$widgets = get_option( 'widget_block' );
echo "=== block ===\n";
print_r( $widgets );