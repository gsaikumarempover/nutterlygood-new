<?php
/**
 * Map AI-generated HD images to ALL products + category carousel cards.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

function ng_ailog( $msg ) {
	echo $msg . PHP_EOL;
}

function ng_ai_upload_path( $rel ) {
	return trailingslashit( wp_upload_dir()['basedir'] ) . ltrim( $rel, '/' );
}

function ng_ai_get_attachment_by_file( $rel ) {
	global $wpdb;
	$id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value=%s LIMIT 1",
			ltrim( $rel, '/' )
		)
	);
	return $id ? (int) $id : 0;
}

function ng_ai_ensure_media( $rel, $title = '' ) {
	$rel = ltrim( $rel, '/' );
	$existing = ng_ai_get_attachment_by_file( $rel );
	if ( $existing ) {
		return $existing;
	}
	$path = ng_ai_upload_path( $rel );
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

function ng_ai_map_products( $manifest_path, $folder ) {
	if ( ! file_exists( $manifest_path ) ) {
		return array( 0, 0 );
	}
	$manifest = json_decode( file_get_contents( $manifest_path ), true );
	$updated  = 0;
	$skipped  = 0;
	foreach ( $manifest as $item ) {
		$slug  = $item['slug'] ?? '';
		$file  = $item['file'] ?? '';
		$title = $item['title'] ?? $slug;
		$product = get_page_by_path( $slug, OBJECT, 'product' );
		if ( ! $product ) {
			ng_ailog( "Product skip (not found): $slug" );
			$skipped++;
			continue;
		}
		$png_file = preg_replace( '/\.jpe?g$/i', '.png', $file );
		$rel      = "2026/06/$folder/" . ( file_exists( ng_ai_upload_path( "2026/06/$folder/$png_file" ) ) ? $png_file : $file );
		if ( ! file_exists( ng_ai_upload_path( $rel ) ) ) {
			ng_ailog( "Product skip (file missing): $rel" );
			$skipped++;
			continue;
		}
		$attach_id = ng_ai_ensure_media( $rel, $title . ' AI Hero' );
		if ( ! $attach_id ) {
			$skipped++;
			continue;
		}
		set_post_thumbnail( $product->ID, $attach_id );
		update_post_meta( $product->ID, '_ng_ai_product_image', $rel );
		ng_ailog( "Product: $title -> $rel" );
		$updated++;
	}
	return array( $updated, $skipped );
}

function ng_ai_map_categories( $manifest_path ) {
	if ( ! file_exists( $manifest_path ) ) {
		return array( 0, 0 );
	}
	$manifest = json_decode( file_get_contents( $manifest_path ), true );
	$updated  = 0;
	$skipped  = 0;
	foreach ( $manifest as $item ) {
		$slug  = $item['slug'] ?? '';
		$file  = $item['file'] ?? '';
		$title = $item['title'] ?? $slug;
		$term  = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term ) {
			ng_ailog( "Category skip (not found): $slug" );
			$skipped++;
			continue;
		}
		$rel = "2026/06/ai-categories/$file";
		if ( ! file_exists( ng_ai_upload_path( $rel ) ) ) {
			ng_ailog( "Category skip (file missing): $rel" );
			$skipped++;
			continue;
		}
		$attach_id = ng_ai_ensure_media( $rel, $title . ' AI Category' );
		if ( ! $attach_id ) {
			$skipped++;
			continue;
		}
		update_term_meta( $term->term_id, 'thumbnail_id', $attach_id );
		delete_term_meta( $term->term_id, 'qodef_product_category_alternate_svg' );
		ng_ailog( "Category: $title -> $rel" );
		$updated++;
	}
	return array( $updated, $skipped );
}

$root = __DIR__;
list( $p1, $s1 ) = ng_ai_map_products( $root . '/product-ai-manifest.json', 'ai-products' );
list( $p2, $s2 ) = ng_ai_map_products( $root . '/product-ai-manifest-remaining.json', 'ai-products' );
list( $c1, $cs1 ) = ng_ai_map_categories( $root . '/category-ai-manifest.json' );

delete_option( 'greenpath_core_dynamic_styles' );
wp_cache_flush();

$pt = $p1 + $p2;
$ps = $s1 + $s2;
ng_ailog( "=== Done: products $pt mapped ($ps skipped), categories $c1 mapped ($cs1 skipped) ===" );