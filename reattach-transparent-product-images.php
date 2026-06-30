<?php
/**
 * Re-upload transparent PNG product images and refresh WooCommerce galleries.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$dir    = WP_CONTENT_DIR . '/uploads/2026/06/ai-products/';
$subdir = '2026/06/ai-products';

function ng_transparent_attachment_by_basename( $basename, $subdir = '' ) {
	global $wpdb;
	$paths = array();
	if ( $subdir ) {
		$paths[] = trailingslashit( $subdir ) . $basename;
	}
	$paths[] = $basename;

	foreach ( $paths as $path ) {
		$aid = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s ORDER BY post_id DESC LIMIT 1",
				$path
			)
		);
		if ( $aid ) {
			return $aid;
		}
	}

	return 0;
}

function ng_transparent_ensure_png( $path, $title ) {
	global $subdir;

	if ( ! file_exists( $path ) ) {
		return 0;
	}

	$basename = basename( $path );
	$existing = ng_transparent_attachment_by_basename( $basename, $subdir );
	if ( $existing ) {
		return $existing;
	}

	$upload = wp_upload_bits( $basename, null, file_get_contents( $path ) ); // phpcs:ignore
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$attach_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/png',
			'post_title'     => sanitize_text_field( $title ),
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $attach_id ) ) {
		return 0;
	}

	wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $upload['file'] ) );
	return (int) $attach_id;
}

$manifests = array(
	__DIR__ . '/product-ai-manifest.json',
	__DIR__ . '/product-ai-manifest-remaining.json',
);

$updated = 0;
$skipped = 0;

foreach ( $manifests as $manifest_path ) {
	if ( ! file_exists( $manifest_path ) ) {
		continue;
	}

	$items = json_decode( file_get_contents( $manifest_path ), true );
	if ( ! is_array( $items ) ) {
		continue;
	}

	foreach ( $items as $item ) {
		$slug  = $item['slug'] ?? '';
		$file  = $item['file'] ?? '';
		$title = $item['title'] ?? $slug;

		if ( ! $slug || ! $file ) {
			++$skipped;
			continue;
		}

		$product_id = wc_get_product_id_by_sku( $slug );
		if ( ! $product_id ) {
			$post = get_page_by_path( $slug, OBJECT, 'product' );
			$product_id = $post ? (int) $post->ID : 0;
		}

		if ( ! $product_id ) {
			echo "SKIP not found: {$slug}\n";
			++$skipped;
			continue;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			++$skipped;
			continue;
		}

		$stem  = pathinfo( $file, PATHINFO_FILENAME );
		$names = array(
			$stem . '.png',
			$stem . '-angle-top.png',
			$stem . '-angle-side.png',
			$stem . '-angle-close.png',
		);

		$ids = array();
		foreach ( $names as $name ) {
			$path = $dir . $name;
			$aid  = ng_transparent_ensure_png( $path, $title . ' transparent' );
			if ( $aid ) {
				$ids[] = $aid;
			}
		}

		$ids = array_values( array_unique( $ids ) );
		if ( empty( $ids ) ) {
			echo "SKIP no PNGs: {$slug}\n";
			++$skipped;
			continue;
		}

		$product->set_image_id( $ids[0] );
		$product->set_gallery_image_ids( array_slice( $ids, 1 ) );
		$product->save();

		update_post_meta( $product_id, '_ng_ai_product_image', $subdir . '/' . $names[0] );
		echo "OK {$slug}: " . count( $ids ) . " PNG(s)\n";
		++$updated;
	}
}

echo "\nUpdated {$updated} products, skipped {$skipped}.\n";