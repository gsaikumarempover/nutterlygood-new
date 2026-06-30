<?php
/**
 * Regenerate WordPress thumbnails after AI product image file updates.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/image.php';

global $wpdb;

$rows = $wpdb->get_results(
	"SELECT post_id, meta_value FROM {$wpdb->postmeta}
	 WHERE meta_key = '_wp_attached_file'
	 AND meta_value LIKE '2026/06/ai-products/%.jpg'"
);

$done = 0;
foreach ( $rows as $row ) {
	$path = trailingslashit( wp_upload_dir()['basedir'] ) . $row->meta_value;
	if ( ! file_exists( $path ) ) {
		continue;
	}
	$meta = wp_generate_attachment_metadata( (int) $row->post_id, $path );
	wp_update_attachment_metadata( (int) $row->post_id, $meta );
	echo "Regenerated: {$row->meta_value}" . PHP_EOL;
	$done++;
}

delete_option( 'greenpath_core_dynamic_styles' );
wp_cache_flush();
echo "=== Regenerated {$done} attachment sizes ===" . PHP_EOL;