<?php
/**
 * Map AI-generated HD product images to homepage carousel products.
 * Does NOT use legacy webp packshots.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

function ng_pclog( $msg ) {
	echo $msg . PHP_EOL;
}

function ng_pc_upload_path( $rel ) {
	return trailingslashit( wp_upload_dir()['basedir'] ) . ltrim( $rel, '/' );
}

function ng_pc_get_attachment_by_file( $rel ) {
	global $wpdb;
	$id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value=%s LIMIT 1",
			ltrim( $rel, '/' )
		)
	);
	return $id ? (int) $id : 0;
}

function ng_pc_ensure_media( $rel, $title = '' ) {
	$rel = ltrim( $rel, '/' );
	$existing = ng_pc_get_attachment_by_file( $rel );
	if ( $existing ) {
		return $existing;
	}
	$path = ng_pc_upload_path( $rel );
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$filetype  = wp_check_filetype( basename( $path ), null );
	$attach_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => $title ? $title : sanitize_file_name( pathinfo( $path, PATHINFO_FILENAME ) ),
			'post_status'    => 'inherit',
		),
		$path
	);
	if ( ! is_wp_error( $attach_id ) ) {
		wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $path ) );
	}
	return is_wp_error( $attach_id ) ? 0 : (int) $attach_id;
}

$manifest_path = __DIR__ . '/product-ai-manifest.json';
if ( ! file_exists( $manifest_path ) ) {
	ng_pclog( 'Missing product-ai-manifest.json' );
	exit( 1 );
}

$manifest = json_decode( file_get_contents( $manifest_path ), true );
if ( ! is_array( $manifest ) ) {
	ng_pclog( 'Invalid manifest JSON' );
	exit( 1 );
}

$updated = 0;
$skipped = 0;

foreach ( $manifest as $item ) {
	$slug = $item['slug'] ?? '';
	$file = $item['file'] ?? '';
	$title = $item['title'] ?? $slug;
	if ( ! $slug || ! $file ) {
		$skipped++;
		continue;
	}
	$product = get_page_by_path( $slug, OBJECT, 'product' );
	if ( ! $product ) {
		ng_pclog( "Skip (product not found): $slug" );
		$skipped++;
		continue;
	}
	$rel = '2026/06/ai-products/' . $file;
	if ( ! file_exists( ng_pc_upload_path( $rel ) ) ) {
		ng_pclog( "Skip (AI file missing): $rel" );
		$skipped++;
		continue;
	}
	$attach_id = ng_pc_ensure_media( $rel, $title . ' AI Hero' );
	if ( ! $attach_id ) {
		ng_pclog( "Skip (attach failed): $slug" );
		$skipped++;
		continue;
	}
	set_post_thumbnail( $product->ID, $attach_id );
	update_post_meta( $product->ID, '_ng_ai_product_image', $rel );
	ng_pclog( "Mapped: $title (#{$product->ID}) -> $rel" );
	$updated++;
}

delete_option( 'greenpath_core_dynamic_styles' );
wp_cache_flush();
ng_pclog( "=== AI product images mapped: $updated updated, $skipped skipped ===" );