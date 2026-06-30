<?php
require __DIR__ . '/wp-load.php';
global $wpdb;
$rows = $wpdb->get_results(
	"SELECT option_name, LENGTH(option_value) AS size FROM {$wpdb->options} WHERE autoload='yes' ORDER BY size DESC LIMIT 20"
);
$total = $wpdb->get_var( "SELECT SUM(LENGTH(option_value)) FROM {$wpdb->options} WHERE autoload='yes'" );
echo 'Autoload total: ' . number_format( (int) $total ) . " bytes\n\n";
foreach ( $rows as $r ) {
	echo number_format( (int) $r->size ) . "  {$r->option_name}\n";
}