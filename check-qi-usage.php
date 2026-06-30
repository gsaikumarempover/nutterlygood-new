<?php
require __DIR__ . '/wp-load.php';
global $wpdb;
$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key='_elementor_data' AND meta_value LIKE '%\"widgetType\":\"qi_%'" );
echo $count > 0 ? "Qi widgets used on $count pages\n" : "No Qi widgets in Elementor pages\n";
$blocks = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key='_elementor_data' AND meta_value LIKE '%qi-blocks%'" );
echo "Qi blocks refs: $blocks\n";