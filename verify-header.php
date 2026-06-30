<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$s = get_option( 'sidebars_widgets' );
echo "top-left: " . implode( ', ', $s['qodef-top-area-left'] ?? array() ) . PHP_EOL;
echo "top-center: " . implode( ', ', $s['qodef-top-area-center'] ?? array() ) . PHP_EOL;
echo "top-right: " . implode( ', ', $s['qodef-top-area-right'] ?? array() ) . PHP_EOL;
echo "ext-one: " . implode( ', ', $s['extended-header-one'] ?? array() ) . PHP_EOL;
echo "ext-two: " . implode( ', ', $s['extended-header-two'] ?? array() ) . PHP_EOL;

$b = get_option( 'widget_block' );
echo "block-13: " . ( ! empty( $b[13] ) ? 'yes' : 'no' ) . PHP_EOL;
echo "block-25: " . ( ! empty( $b[25] ) ? 'yes' : 'no' ) . PHP_EOL;

$svg = get_option( 'widget_greenpath_core_svg_icon' );
echo "svg-14: " . ( ! empty( $svg[14] ) ? 'yes' : 'no' ) . PHP_EOL;
echo "svg-25: " . ( ! empty( $svg[25] ) ? 'yes' : 'no' ) . PHP_EOL;

$compare = get_option( 'widget_qode_compare_for_woocommerce_compare_counter' );
echo "compare-8: " . ( ! empty( $compare[8] ) ? 'yes' : 'no' ) . PHP_EOL;

$wishlist = get_option( 'widget_greenpath_core_qode_wishlist' );
echo "wishlist-2: " . ( ! empty( $wishlist[2] ) ? 'yes' : 'no' ) . PHP_EOL;

$items = wp_get_nav_menu_items( 70 );
echo "cat menu items: " . ( $items ? count( $items ) : 0 ) . PHP_EOL;