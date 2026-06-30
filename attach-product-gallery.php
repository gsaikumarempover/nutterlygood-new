<?php
/**
 * Attach multi-angle gallery images to a WooCommerce product.
 * Usage: php attach-product-gallery.php <product-slug> [image1.jpg image2.jpg ...]
 */
require __DIR__ . '/wp-load.php';

if ( ! class_exists( 'WooCommerce' ) ) {
	die( "WooCommerce not loaded\n" );
}

$slug = $argv[1] ?? 'df-ppa-250-peri-peri-almonds-5';
$files = array_slice( $argv, 2 );

if ( empty( $files ) ) {
	die( "Usage: php attach-product-gallery.php <slug> file1.jpg file2.jpg ...\n" );
}

function ng_gallery_attachment_by_filename( $filename, $subdir = '' ) {
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

function ng_gallery_ensure_attachment( $path, $title, $subdir = '' ) {
	if ( ! file_exists( $path ) ) {
		echo "Missing file: {$path}\n";
		return 0;
	}

	$basename = basename( $path );
	$existing = ng_gallery_attachment_by_filename( $basename, $subdir );
	if ( $existing ) {
		echo "Reuse attachment {$existing} for {$basename}\n";
		return $existing;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$upload = wp_upload_bits( $basename, null, file_get_contents( $path ) ); // phpcs:ignore
	if ( ! empty( $upload['error'] ) ) {
		echo "Upload error for {$basename}: {$upload['error']}\n";
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
		echo "Insert error for {$basename}\n";
		return 0;
	}

	wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $upload['file'] ) );
	echo "Created attachment {$attach_id} for {$basename}\n";
	return (int) $attach_id;
}

$product_id = wc_get_product_id_by_sku( $slug );
if ( ! $product_id ) {
	$post = get_page_by_path( $slug, OBJECT, 'product' );
	$product_id = $post ? (int) $post->ID : 0;
}

if ( ! $product_id ) {
	die( "Product not found for slug: {$slug}\n" );
}

$product = wc_get_product( $product_id );
if ( ! $product ) {
	die( "Invalid product ID {$product_id}\n" );
}

$products_dir = WP_CONTENT_DIR . '/uploads/2026/06/ai-products/';
$subdir       = '2026/06/ai-products';
$gallery_ids  = array();

foreach ( $files as $file ) {
	$path = str_contains( $file, ':\\' ) || str_starts_with( $file, '/' ) ? $file : $products_dir . $file;
	$aid  = ng_gallery_ensure_attachment( $path, $product->get_name() . ' gallery', $subdir );
	if ( $aid ) {
		$gallery_ids[] = $aid;
	}
}

$gallery_ids = array_values( array_unique( $gallery_ids ) );
if ( empty( $gallery_ids ) ) {
	die( "No gallery images attached.\n" );
}

$product->set_image_id( $gallery_ids[0] );
$product->set_gallery_image_ids( array_slice( $gallery_ids, 1 ) );
$product->save();

echo "Product {$product_id} ({$product->get_name()}): featured={$gallery_ids[0]}, gallery=" . implode( ',', array_slice( $gallery_ids, 1 ) ) . "\n";