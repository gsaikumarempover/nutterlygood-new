<?php
/**
 * Mix legacy webp packshots with HD AI transparent PNGs on product cards.
 * ~half primary HD + hover legacy, ~half primary legacy + hover HD.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

function ng_mix_log( $msg ) {
	echo $msg . PHP_EOL;
}

function ng_mix_attachment_by_file( $rel ) {
	global $wpdb;
	$rel = ltrim( $rel, '/' );
	$id  = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value=%s ORDER BY post_id DESC LIMIT 1",
			$rel
		)
	);
	return $id;
}

function ng_mix_ensure_attachment( $rel, $title = '' ) {
	$existing = ng_mix_attachment_by_file( $rel );
	if ( $existing ) {
		return $existing;
	}

	$path = trailingslashit( wp_upload_dir()['basedir'] ) . ltrim( $rel, '/' );
	if ( ! file_exists( $path ) ) {
		return 0;
	}

	$filetype = wp_check_filetype( basename( $path ), null );
	$attach   = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'] ?: 'image/webp',
			'post_title'     => $title ? $title : sanitize_file_name( pathinfo( $path, PATHINFO_FILENAME ) ),
			'post_status'    => 'inherit',
		),
		$path
	);

	if ( is_wp_error( $attach ) ) {
		return 0;
	}

	wp_update_attachment_metadata( $attach, wp_generate_attachment_metadata( $attach, $path ) );
	return (int) $attach;
}

function ng_mix_legacy_webp_for_product( WC_Product $product ) {
	$title = $product->get_name();
	$guess = str_replace( ' ', '-', $title ) . '.webp';
	$path  = trailingslashit( wp_upload_dir()['basedir'] ) . '2026/06/' . $guess;
	if ( file_exists( $path ) ) {
		return '2026/06/' . $guess;
	}

	$dir   = trailingslashit( wp_upload_dir()['basedir'] ) . '2026/06/';
	$webps = glob( $dir . '*.webp' );
	foreach ( $webps as $file ) {
		$name = basename( $file );
		if ( preg_match( '/-1\.webp$/', $name ) ) {
			continue;
		}
		$stem = pathinfo( $name, PATHINFO_FILENAME );
		if ( 0 === strcasecmp( $stem, str_replace( ' ', '-', $title ) ) ) {
			return '2026/06/' . $name;
		}
	}

	return '';
}

function ng_mix_hd_png_for_slug( $slug, $manifest_files ) {
	$stem = null;
	foreach ( $manifest_files as $row ) {
		if ( ( $row['slug'] ?? '' ) === $slug ) {
			$stem = pathinfo( $row['file'] ?? '', PATHINFO_FILENAME );
			break;
		}
	}
	if ( ! $stem ) {
		$stem = $slug;
	}

	$candidates = array(
		"2026/06/ai-products/{$stem}.png",
		"2026/06/{$stem}.png",
	);
	foreach ( $candidates as $rel ) {
		if ( file_exists( trailingslashit( wp_upload_dir()['basedir'] ) . $rel ) ) {
			return $rel;
		}
	}

	return '';
}

$manifest_files = array();
foreach ( array( 'product-ai-manifest.json', 'product-ai-manifest-remaining.json' ) as $mf ) {
	$path = __DIR__ . '/' . $mf;
	if ( file_exists( $path ) ) {
		$decoded = json_decode( file_get_contents( $path ), true );
		if ( is_array( $decoded ) ) {
			$manifest_files = array_merge( $manifest_files, $decoded );
		}
	}
}

$products = wc_get_products( array( 'limit' => -1, 'status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ) );
$hd_count = 0;
$lg_count = 0;
$skipped  = 0;

foreach ( $products as $idx => $product ) {
	$id   = $product->get_id();
	$slug = $product->get_slug();

	$hd_rel = ng_mix_hd_png_for_slug( $slug, $manifest_files );
	$lg_rel = ng_mix_legacy_webp_for_product( $product );

	if ( ! $hd_rel && ! $lg_rel ) {
		ng_mix_log( "SKIP {$slug}: no images" );
		++$skipped;
		continue;
	}

	$hd_id = $hd_rel ? ng_mix_ensure_attachment( $hd_rel, $product->get_name() . ' HD' ) : 0;
	$lg_id = $lg_rel ? ng_mix_ensure_attachment( $lg_rel, $product->get_name() . ' Legacy' ) : 0;

	$use_hd_primary = ( $idx % 2 === 0 );
	if ( $use_hd_primary && ! $hd_id && $lg_id ) {
		$use_hd_primary = false;
	}
	if ( ! $use_hd_primary && ! $lg_id && $hd_id ) {
		$use_hd_primary = true;
	}

	$primary_id = $use_hd_primary ? $hd_id : $lg_id;
	$hover_id   = $use_hd_primary ? $lg_id : $hd_id;
	$mode       = $use_hd_primary ? 'hd' : 'legacy';

	if ( ! $primary_id ) {
		ng_mix_log( "SKIP {$slug}: no primary attachment" );
		++$skipped;
		continue;
	}

	$gallery = array();
	if ( $hover_id && $hover_id !== $primary_id ) {
		$gallery[] = $hover_id;
	}

	// Extra HD angles for gallery when HD pack exists.
	if ( $hd_id ) {
		$stem = pathinfo( $hd_rel, PATHINFO_FILENAME );
		foreach ( array( 'angle-top', 'angle-side', 'angle-close' ) as $suffix ) {
			$angle_rel = dirname( $hd_rel ) . '/' . $stem . '-' . $suffix . '.png';
			$angle_id  = ng_mix_ensure_attachment( $angle_rel, $product->get_name() . ' ' . $suffix );
			if ( $angle_id && $angle_id !== $primary_id && ! in_array( $angle_id, $gallery, true ) ) {
				$gallery[] = $angle_id;
			}
		}
	}

	$product->set_image_id( $primary_id );
	$product->set_gallery_image_ids( $gallery );
	$product->save();

	update_post_meta( $id, '_ng_card_image_mode', $mode );
	update_post_meta( $id, '_ng_hd_image_id', $hd_id );
	update_post_meta( $id, '_ng_legacy_image_id', $lg_id );
	delete_post_meta( $id, 'qodef_product_list_image' );

	$sizes_raw = get_post_meta( $id, '_ng_farmley_sizes', true );
	if ( ! empty( $sizes_raw ) ) {
		$sizes = json_decode( $sizes_raw, true );
		if ( is_array( $sizes ) && isset( $sizes[0] ) && is_array( $sizes[0] ) ) {
			$sizes[0]['image_id'] = $primary_id;
			update_post_meta( $id, '_ng_farmley_sizes', wp_json_encode( $sizes ) );
		}
	}

	if ( $use_hd_primary ) {
		++$hd_count;
	} else {
		++$lg_count;
	}

	ng_mix_log( "OK {$slug} mode={$mode} primary={$primary_id} hover=" . ( $hover_id ?: 'none' ) );
}

delete_option( 'greenpath_core_dynamic_styles' );
wp_cache_flush();

ng_mix_log( "=== Done: HD primary={$hd_count}, legacy primary={$lg_count}, skipped={$skipped} ===" );