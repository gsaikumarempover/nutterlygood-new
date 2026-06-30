<?php
/**
 * Attach Nutterly Good branded packet images (main + 3 angles) to all products.
 * Expects files in wp-content/uploads/2026/06/ng-branded-packets/
 * Run: php attach-ng-branded-packets.php
 */
require __DIR__ . '/wp-load.php';

if ( ! class_exists( 'WooCommerce' ) ) {
	die( "WooCommerce not loaded\n" );
}

$manifest_path = __DIR__ . '/ng-branded-packets-manifest.json';
$packets_dir   = WP_CONTENT_DIR . '/uploads/2026/06/ng-branded-packets/';
$subdir        = '2026/06/ng-branded-packets';

if ( ! file_exists( $manifest_path ) ) {
	die( "Manifest not found\n" );
}

$items = json_decode( file_get_contents( $manifest_path ), true );
if ( ! is_array( $items ) ) {
	die( "Invalid manifest\n" );
}

function ng_branded_attachment_by_filename( $filename, $subdir = '' ) {
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

function ng_branded_ensure_attachment( $path, $title, $subdir = '' ) {
	if ( ! file_exists( $path ) ) {
		return 0;
	}

	$basename = basename( $path );
	$existing = ng_branded_attachment_by_filename( $basename, $subdir );
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
			'post_mime_type' => $filetype['type'] ?: 'image/jpeg',
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

$updated = 0;
$skipped = 0;
$missing = 0;

foreach ( $items as $item ) {
	$id    = (int) ( $item['id'] ?? 0 );
	$slug  = $item['slug'] ?? '';
	$title = $item['title'] ?? $slug;

	if ( ! $id || ! $slug ) {
		$skipped++;
		continue;
	}

	$product = wc_get_product( $id );
	if ( ! $product ) {
		echo "SKIP missing product #{$id}\n";
		$skipped++;
		continue;
	}

	$angles = array( '', '-angle-top', '-angle-side', '-angle-close' );
	$ids    = array();

	foreach ( $angles as $suffix ) {
		foreach ( array( 'jpg', 'png', 'webp' ) as $ext ) {
			$file = $slug . $suffix . '.' . $ext;
			$path = $packets_dir . $file;
			if ( ! file_exists( $path ) ) {
				continue;
			}
			$label = $title . ( $suffix ? ' ' . trim( $suffix, '-' ) : ' front' );
			$aid   = ng_branded_ensure_attachment( $path, $label, $subdir );
			if ( $aid ) {
				$ids[] = $aid;
			}
			break;
		}
	}

	$ids = array_values( array_unique( $ids ) );
	if ( count( $ids ) < 1 ) {
		echo "MISSING images: {$slug}\n";
		$missing++;
		continue;
	}

	$product->set_image_id( $ids[0] );
	$product->set_gallery_image_ids( array_slice( $ids, 1 ) );
	$product->save();

	$sizes_raw = get_post_meta( $id, '_ng_farmley_sizes', true );
	if ( ! empty( $sizes_raw ) ) {
		$sizes = json_decode( $sizes_raw, true );
		if ( is_array( $sizes ) && isset( $sizes[0] ) && is_array( $sizes[0] ) ) {
			$sizes[0]['image_id'] = (int) $ids[0];
			update_post_meta( $id, '_ng_farmley_sizes', wp_json_encode( $sizes ) );
		}
	}

	echo 'OK ' . $slug . ' featured=' . $ids[0] . ' gallery=' . implode( ',', array_slice( $ids, 1 ) ) . ' (' . count( $ids ) . " imgs)\n";
	$updated++;
}

echo "\nDone: updated={$updated}, missing={$missing}, skipped={$skipped}\n";