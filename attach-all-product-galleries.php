<?php
/**
 * Attach main + 3 unique angle gallery images for every manifest product.
 * Run: php attach-all-product-galleries.php
 */
require __DIR__ . '/wp-load.php';

if ( ! class_exists( 'WooCommerce' ) ) {
	die( "WooCommerce not loaded\n" );
}

function ng_batch_attachment_by_filename( $filename, $subdir = '' ) {
	global $wpdb;
	$basename = basename( $filename );
	$paths    = array();
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

function ng_batch_resolve_product_file( $products_dir, $filename ) {
	$candidates = array();
	$png        = preg_replace( '/\.jpe?g$/i', '.png', $filename );
	if ( $png !== $filename ) {
		$candidates[] = $png;
	}
	$candidates[] = $filename;

	foreach ( $candidates as $name ) {
		$path = $products_dir . $name;
		if ( file_exists( $path ) ) {
			return $path;
		}
	}

	return '';
}

function ng_batch_ensure_attachment( $path, $title, $subdir = '' ) {
	if ( ! file_exists( $path ) ) {
		return 0;
	}

	$basename = basename( $path );
	$existing = ng_batch_attachment_by_filename( $basename, $subdir );
	if ( $existing ) {
		return $existing;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$upload = wp_upload_bits( $basename, null, file_get_contents( $path ) ); // phpcs:ignore
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$filetype  = wp_check_filetype( $basename );
	$attach_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => sanitize_text_field( $title ),
			'post_content'   => '',
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

$products_dir = WP_CONTENT_DIR . '/uploads/2026/06/ai-products/';
$subdir       = '2026/06/ai-products';
$updated      = 0;
$skipped      = 0;

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
			$skipped++;
			continue;
		}

		$product_id = wc_get_product_id_by_sku( $slug );
		if ( ! $product_id ) {
			$post = get_page_by_path( $slug, OBJECT, 'product' );
			$product_id = $post ? (int) $post->ID : 0;
		}

		if ( ! $product_id ) {
			echo "SKIP not found: {$slug}\n";
			$skipped++;
			continue;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			$skipped++;
			continue;
		}

		$stem   = pathinfo( $file, PATHINFO_FILENAME );
		$files  = array(
			$file,
			"{$stem}-angle-top.jpg",
			"{$stem}-angle-side.jpg",
			"{$stem}-angle-close.jpg",
		);
		$ids    = array();

		foreach ( $files as $gallery_file ) {
			$path = ng_batch_resolve_product_file( $products_dir, $gallery_file );
			if ( ! $path ) {
				continue;
			}
			$aid  = ng_batch_ensure_attachment( $path, $title . ' gallery', $subdir );
			if ( $aid ) {
				$ids[] = $aid;
			}
		}

		$ids = array_values( array_unique( $ids ) );
		if ( count( $ids ) < 2 ) {
			echo "SKIP gallery incomplete ({$slug}): " . count( $ids ) . " images\n";
			$skipped++;
			continue;
		}

		$product->set_image_id( $ids[0] );
		$product->set_gallery_image_ids( array_slice( $ids, 1 ) );
		$product->save();

		echo "OK {$slug}: featured={$ids[0]}, gallery=" . implode( ',', array_slice( $ids, 1 ) ) . "\n";
		$updated++;
	}
}

echo "\nUpdated {$updated} products, skipped {$skipped}.\n";