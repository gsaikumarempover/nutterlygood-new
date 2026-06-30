<?php
/**
 * Upload generated packet images and assign to sample dry-fruits products.
 * Run: php set-product-packet-images.php
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$image_dir = 'C:/Users/G Sai Kumar/.grok/sessions/C%3A%5CUsers%5CG%20Sai%20Kumar/019ed9ba-80f9-78a1-9fda-a791f0155a73/images';

$map = array(
	7585 => array( 'file' => '64.jpg', 'title' => 'Peri Peri Almonds Packet' ),
	7583 => array( 'file' => '65.jpg', 'title' => 'Premium Classic Almonds Packet' ),
	7523 => array( 'file' => '66.jpg', 'title' => 'Premium Classic Cashews Packet' ),
	7525 => array( 'file' => '67.jpg', 'title' => 'Pizza Cashews Packet' ),
);

foreach ( $map as $product_id => $info ) {
	$source = $image_dir . '/' . $info['file'];
	if ( ! file_exists( $source ) ) {
		echo "MISSING: {$source}\n";
		continue;
	}

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		echo "NO PRODUCT: {$product_id}\n";
		continue;
	}

	$upload_dir = wp_upload_dir();
	$dest_name  = 'ng-packet-' . $product_id . '-' . $info['file'];
	$dest_path  = $upload_dir['path'] . '/' . $dest_name;

	if ( ! copy( $source, $dest_path ) ) {
		echo "COPY FAIL: {$product_id}\n";
		continue;
	}

	$filetype = wp_check_filetype( $dest_name, null );
	$attachment = array(
		'post_mime_type' => $filetype['type'] ? $filetype['type'] : 'image/jpeg',
		'post_title'     => sanitize_text_field( $info['title'] ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	$attach_id = wp_insert_attachment( $attachment, $dest_path, $product_id );
	if ( is_wp_error( $attach_id ) ) {
		echo "ATTACH FAIL {$product_id}: " . $attach_id->get_error_message() . "\n";
		continue;
	}

	$attach_data = wp_generate_attachment_metadata( $attach_id, $dest_path );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	set_post_thumbnail( $product_id, $attach_id );

	$sizes_raw = get_post_meta( $product_id, '_ng_farmley_sizes', true );
	if ( ! empty( $sizes_raw ) ) {
		$sizes = json_decode( $sizes_raw, true );
		if ( is_array( $sizes ) && isset( $sizes[0] ) ) {
			$sizes[0]['image_id'] = (int) $attach_id;
			update_post_meta( $product_id, '_ng_farmley_sizes', wp_json_encode( $sizes ) );
		}
	}

	echo "OK {$product_id} " . $product->get_name() . " => attachment {$attach_id}\n";
}