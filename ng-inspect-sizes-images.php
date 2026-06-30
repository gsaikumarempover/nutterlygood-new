<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$products = wc_get_products( array( 'limit' => -1, 'status' => 'publish' ) );

$webp_in_sizes = 0;
$webp_gallery  = 0;

foreach ( $products as $p ) {
	$id = $p->get_id();
	$meta_key = '_ng_farmley_product_meta';
	if ( ! metadata_exists( 'post', $id, $meta_key ) ) {
		$meta_key = 'ng_farmley_product_meta';
	}
	$raw = get_post_meta( $id, $meta_key, true );
	if ( is_string( $raw ) ) {
		$meta = maybe_unserialize( $raw );
	} else {
		$meta = $raw;
	}
	if ( ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
		foreach ( $meta['sizes'] as $row ) {
			$img_id = (int) ( $row['image_id'] ?? 0 );
			if ( ! $img_id ) {
				continue;
			}
			$file = get_post_meta( $img_id, '_wp_attached_file', true );
			if ( str_contains( (string) $file, '.webp' ) ) {
				++$webp_in_sizes;
				echo "SIZE webp: {$p->get_name()} -> {$file}" . PHP_EOL;
			}
		}
	}

	foreach ( $p->get_gallery_image_ids() as $gid ) {
		$file = get_post_meta( $gid, '_wp_attached_file', true );
		if ( str_contains( (string) $file, '.webp' ) ) {
			++$webp_gallery;
			echo "GALLERY webp: {$p->get_name()} -> {$file}" . PHP_EOL;
		}
	}
}

echo "webp in sizes: {$webp_in_sizes}, webp in gallery: {$webp_gallery}" . PHP_EOL;