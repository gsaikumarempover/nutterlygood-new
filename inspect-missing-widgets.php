<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$compare = get_option( 'widget_qode_compare_for_woocommerce_compare_counter' );
echo "compare widget 8:\n";
print_r( $compare[8] ?? 'MISSING' );

$wishlist = get_option( 'widget_greenpath_core_qode_wishlist' );
echo "\nwishlist widget 2:\n";
print_r( $wishlist[2] ?? 'MISSING' );

echo "\ncompare plugin active: " . ( is_plugin_active( 'qode-compare-for-woocommerce/qode-compare-for-woocommerce.php' ) ? 'yes' : 'no' ) . PHP_EOL;
echo "wishlist plugin active: " . ( is_plugin_active( 'greenpath-membership/greenpath-membership.php' ) ? 'yes' : 'no' ) . PHP_EOL;

// Try rendering widgets manually
echo "\n--- Render test ---\n";
ob_start();
the_widget( 'Qode_Compare_For_Woocommerce_Compare_Counter_Widget', $compare[8] ?? array(), array( 'widget_id' => 'qode_compare_for_woocommerce_compare_counter-8' ) );
echo "compare output: " . ( ob_get_clean() ? 'yes' : 'empty' ) . PHP_EOL;

ob_start();
the_widget( 'Greenpath_Core_Qode_Wishlist_Widget', $wishlist[2] ?? array(), array( 'widget_id' => 'greenpath_core_qode_wishlist-2' ) );
$w = ob_get_clean();
echo "wishlist output length: " . strlen( $w ) . PHP_EOL;

$account_id = wc_get_page_id( 'myaccount' );
echo "\nmyaccount page id: $account_id\n";
echo "myaccount url: " . get_permalink( $account_id ) . PHP_EOL;