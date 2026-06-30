<?php
/**
 * Sync local WooCommerce catalog with live nutterlygood.com shop (pages 1–2).
 * Run: php sync-live-shop.php
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$admin = get_user_by( 'login', 'nutterlygood' );
if ( $admin ) {
	wp_set_current_user( $admin->ID );
}

if ( ! class_exists( 'WooCommerce' ) ) {
	die( "WooCommerce not loaded\n" );
}

define( 'NG_MIGRATION_LIB_ONLY', true );
require __DIR__ . '/migrate-nutterlygood.php';

function ng_sync_log( $message ) {
	echo $message . PHP_EOL;
}

function ng_sync_fetch_shop_pages( $max_pages = 2 ) {
	$paths = array();
	for ( $p = 1; $p <= $max_pages; $p++ ) {
		$url = 1 === $p ? 'https://www.nutterlygood.com/shop' : 'https://www.nutterlygood.com/shop/page/' . $p;
		ng_sync_log( 'Fetching ' . $url );
		$response = wp_remote_get(
			$url,
			array(
				'timeout'   => 45,
				'sslverify' => false,
				'headers'   => array(
					'User-Agent' => 'NutterlyGoodLocalSync/1.0',
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			ng_sync_log( 'Fetch failed page ' . $p . ': ' . $response->get_error_message() );
			continue;
		}
		$body = wp_remote_retrieve_body( $response );
		if ( ! $body ) {
			continue;
		}
		$local = __DIR__ . '/live-shop-sync-p' . $p . '.html';
		file_put_contents( $local, $body );
		$paths[] = $local;
	}
	return $paths;
}

function ng_sync_parse_listing( $file ) {
	$items = ng_parse_products_from_html( $file );
	foreach ( $items as &$item ) {
		if ( ! empty( $item['image'] ) && 0 !== strpos( $item['image'], 'http' ) ) {
			$item['image'] = 'https://www.nutterlygood.com' . $item['image'];
		}
	}
	unset( $item );
	return $items;
}

function ng_sync_fetch_product_detail( $slug ) {
	$url      = 'https://www.nutterlygood.com/shop/' . rawurlencode( $slug );
	$response = wp_remote_get(
		$url,
		array(
			'timeout'   => 45,
			'sslverify' => false,
			'headers'   => array(
				'User-Agent' => 'NutterlyGoodLocalSync/1.0',
			),
		)
	);
	if ( is_wp_error( $response ) ) {
		return array();
	}
	$html = wp_remote_retrieve_body( $response );
	if ( ! $html ) {
		return array();
	}

	$detail = array(
		'description' => '',
		'weight'      => '',
		'price'       => 0,
		'images'      => array(),
	);

	if ( preg_match( '/oe_currency_value">([\d.]+)/', $html, $pr ) ) {
		$detail['price'] = (float) $pr[1];
	}

	if ( preg_match( '/data-value-name="([^"]+)"[^>]*data-attribute-name="Weight"/', $html, $w ) ) {
		$detail['weight'] = trim( $w[1] );
	} elseif ( preg_match( '/<span>Weight<\/span>:\s*<span>([^<]+)<\/span>/', $html, $w ) ) {
		$detail['weight'] = trim( $w[1] );
	}

	if ( preg_match( '/id="product_full_description"[^>]*>(.*?)<div class="oe_structure oe_empty oe_structure_not_nearest/s', $html, $desc ) ) {
		$detail['description'] = trim( $desc[1] );
	}

	preg_match_all( '/src="(\/web\/image\/(?:product\.product|product\.image)\/[^"]+)"/', $html, $imgs );
	$seen = array();
	foreach ( $imgs[1] as $path ) {
		$full = 'https://www.nutterlygood.com' . html_entity_decode( $path, ENT_QUOTES, 'UTF-8' );
		if ( ! isset( $seen[ $full ] ) ) {
			$seen[ $full ] = true;
			$detail['images'][] = $full;
		}
	}

	return $detail;
}

function ng_sync_attachment_by_filename( $filename, $subdir = '' ) {
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
		$like = '%/' . $wpdb->esc_like( $basename );
		$aid  = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s ORDER BY post_id DESC LIMIT 1",
				$like
			)
		);
		if ( $aid ) {
			return $aid;
		}
	}
	return 0;
}

function ng_sync_ensure_local_file_attachment( $path, $title, $subdir = '' ) {
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$basename = basename( $path );
	$existing = ng_sync_attachment_by_filename( $basename, $subdir );
	if ( $existing ) {
		return $existing;
	}

	$upload = wp_upload_bits( $basename, null, file_get_contents( $path ) ); // phpcs:ignore
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$filetype  = wp_check_filetype( $basename );
	$attach    = array(
		'post_mime_type' => $filetype['type'],
		'post_title'     => sanitize_text_field( $title ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);
	$attach_id = wp_insert_attachment( $attach, $upload['file'] );
	if ( ! is_wp_error( $attach_id ) ) {
		wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $upload['file'] ) );
		return (int) $attach_id;
	}
	return 0;
}

function ng_sync_generate_transparent_png( $title, $slug ) {
	if ( ! function_exists( 'imagecreatetruecolor' ) ) {
		return 0;
	}

	$dir = WP_CONTENT_DIR . '/uploads/2026/06/ai-products/generated/';
	wp_mkdir_p( $dir );
	$file = $dir . sanitize_file_name( $slug ) . '.png';
	if ( file_exists( $file ) ) {
		return ng_sync_ensure_local_file_attachment( $file, $title, '2026/06/ai-products/generated' );
	}

	$size = 800;
	$img  = imagecreatetruecolor( $size, $size );
	imagealphablending( $img, false );
	imagesavealpha( $img, true );
	$transparent = imagecolorallocatealpha( $img, 0, 0, 0, 127 );
	imagefill( $img, 0, 0, $transparent );

	$green = imagecolorallocate( $img, 12, 83, 61 );
	$gold  = imagecolorallocate( $img, 185, 149, 49 );
	$cream = imagecolorallocate( $img, 252, 244, 235 );

	imagefilledellipse( $img, 400, 360, 520, 520, $cream );
	imagefilledellipse( $img, 400, 360, 480, 480, $green );

	$words = preg_split( '/\s+/', trim( $title ) );
	$line1 = implode( ' ', array_slice( $words, 0, 3 ) );
	$line2 = implode( ' ', array_slice( $words, 3 ) );
	imagestring( $img, 5, 280, 320, substr( $line1, 0, 28 ), $gold );
	if ( $line2 ) {
		imagestring( $img, 4, 300, 350, substr( $line2, 0, 24 ), $gold );
	}

	imagepng( $img, $file );
	imagedestroy( $img );

	return ng_sync_ensure_local_file_attachment( $file, $title, '2026/06/ai-products/generated' );
}

function ng_sync_product_images( $item, $detail ) {
	$slug         = $item['slug'];
	$title        = $item['name'];
	$products_dir = WP_CONTENT_DIR . '/uploads/2026/06/ai-products/';
	$subdir       = '2026/06/ai-products';
	$gallery_ids  = array();

	$candidates = array(
		$products_dir . $slug . '.jpg',
		$products_dir . $slug . '.png',
		$products_dir . $slug . '.webp',
	);

	foreach ( $candidates as $path ) {
		if ( file_exists( $path ) ) {
			$aid = ng_sync_ensure_local_file_attachment( $path, $title . ' pack', $subdir );
			if ( $aid ) {
				$gallery_ids[] = $aid;
				break;
			}
		}
	}

	if ( empty( $gallery_ids ) && ! empty( $detail['images'] ) ) {
		foreach ( array_slice( $detail['images'], 0, 4 ) as $idx => $url ) {
			$aid = ng_download_image_to_media( $url, $title . ' image ' . ( $idx + 1 ) );
			if ( $aid ) {
				$gallery_ids[] = $aid;
			}
		}
	}

	if ( empty( $gallery_ids ) && ! empty( $item['image'] ) ) {
		$aid = ng_download_image_to_media( $item['image'], $title );
		if ( $aid ) {
			$gallery_ids[] = $aid;
		}
	}

	if ( empty( $gallery_ids ) ) {
		$aid = ng_sync_generate_transparent_png( $title, $slug );
		if ( $aid ) {
			$gallery_ids[] = $aid;
		}
	}

	return array_values( array_unique( array_filter( $gallery_ids ) ) );
}

function ng_sync_weight_to_kg( $weight_label ) {
	if ( preg_match( '/(\d+(?:\.\d+)?)\s*(kg|g|gm|gram)/i', $weight_label, $m ) ) {
		$val = (float) $m[1];
		return stripos( $m[2], 'kg' ) !== false ? $val : $val / 1000;
	}
	if ( preg_match( '/(\d{2,4})/', $weight_label, $m ) ) {
		return (float) $m[1] / 1000;
	}
	return 0;
}

function ng_sync_weight_display( $weight_label, $slug, $title ) {
	if ( $weight_label ) {
		return $weight_label;
	}
	if ( preg_match( '/(\d{2,4})\s*g/i', $slug . ' ' . $title, $m ) ) {
		return $m[1] . ' g';
	}
	if ( preg_match( '/-(\d{2,4})-/', $slug, $m ) ) {
		return $m[1] . ' g';
	}
	return '';
}

function ng_sync_find_product_by_slug( $slug ) {
	$posts = get_posts(
		array(
			'post_type'      => 'product',
			'name'           => $slug,
			'posts_per_page' => 1,
			'post_status'    => array( 'publish', 'draft', 'private' ),
		)
	);
	return ! empty( $posts[0] ) ? wc_get_product( $posts[0]->ID ) : null;
}

function ng_sync_strip_description( $html ) {
	$text = wp_strip_all_tags( $html );
	$text = preg_replace( '/\s+/', ' ', $text );
	return trim( $text );
}

function ng_sync_build_categories() {
	$dry_fruits = ng_get_or_create_category( 'Dry Fruits', 'dry-fruits' );
	$almonds    = ng_get_or_create_category( 'Almonds', 'almonds', $dry_fruits );
	$cashews    = ng_get_or_create_category( 'Cashews', 'cashews', $dry_fruits );
	$khishmish  = ng_get_or_create_category( 'Khishmish', 'khishmish', $dry_fruits );
	$cranberry  = ng_get_or_create_category( 'Cranberry', 'cranberry', $dry_fruits );
	$walnuts    = ng_get_or_create_category( 'Walnuts', 'walnuts', $dry_fruits );
	$chips      = ng_get_or_create_category( 'Chips', 'chips' );
	$mixes      = ng_get_or_create_category( 'Mixes', 'mixes' );
	$brittles   = ng_get_or_create_category( 'Brittles', 'brittles' );
	$mouth      = ng_get_or_create_category( 'Mouth Freshners', 'mouth-fresheners' );

	return compact( 'dry_fruits', 'almonds', 'cashews', 'khishmish', 'cranberry', 'walnuts', 'chips', 'mixes', 'brittles', 'mouth' );
}

function ng_sync_guess_ingredients( $name ) {
	$map = array(
		'trail'     => 'Black Currant, Pumpkin Seeds, Sunflower Seeds, Cranberry, Blueberry, Cashew, Almond',
		'seed'      => 'Pumpkin Seeds, Sunflower Seeds, Flax Seeds, Chia Seeds, Watermelon Seeds',
		'protein'   => 'Almonds, Cashews, Peanuts, Seeds, Soy Nuggets',
		'museli'    => 'Oats, Almonds, Raisins, Seeds, Berries',
		'mexican'   => 'Corn, Peanuts, Spices, Edible Oil, Salt',
		'fruit mix' => 'Dried Fruits, Berries, Spices, Sugar',
		'almond'    => 'Almonds, Spices, Edible Oil, Salt',
		'cashew'    => 'Cashews, Spices, Edible Oil, Salt',
		'cranberry' => 'Cranberry, Spices, Sugar, Sunflower Oil',
		'mix'       => 'Almonds, Cashews, Seeds, Berries, Raisins',
		'chips'     => 'Potato, Corn, Spices, Edible Oil, Salt',
		'paan'      => 'Betel Leaves, Saunf, Gulkand, Natural Flavours',
		'shot'      => 'Fruit Pulp, Sugar, Spices, Citric Acid',
		'papad'     => 'Mango Pulp, Spices, Salt, Edible Oil',
		'walnut'    => 'Walnuts, Salt',
		'brittle'   => 'Nuts, Sugar, Cocoa, Edible Oil',
	);
	$lower = strtolower( $name );
	foreach ( $map as $key => $val ) {
		if ( false !== strpos( $lower, $key ) ) {
			return $val;
		}
	}
	return 'Premium natural ingredients, spices, and edible oil';
}

// --- Run sync ---
ng_sync_log( '=== Live Shop Sync Start ===' );

$cats        = ng_sync_build_categories();
$pages       = ng_sync_fetch_shop_pages( 2 );
$live_map    = array();

foreach ( $pages as $file ) {
	$live_map = array_merge( $live_map, ng_sync_parse_listing( $file ) );
}

$live_map = array_values( $live_map );
ng_sync_log( 'Live products found: ' . count( $live_map ) );

$live_slugs   = array();
$synced       = 0;
$packed_by    = 'Nutterly Good, CS-09, Etna Block, Rajapushpa Atria, Golden Mile Road, Kokapet, Hyderabad, Telangana 500075';

foreach ( $live_map as $item ) {
	$slug = trim( $item['slug'] );
	$name = trim( $item['name'] );
	if ( ! $slug || ! $name ) {
		continue;
	}

	$live_slugs[] = $slug;
	ng_sync_log( "Syncing {$name} ({$slug})..." );

	$detail = ng_sync_fetch_product_detail( $slug );
	$price  = $detail['price'] > 0 ? $detail['price'] : (float) $item['price'];
	$weight = ng_sync_weight_display( $detail['weight'] ?? '', $slug, $name );
	$weight_kg = ng_sync_weight_to_kg( $weight );

	$description = $detail['description'];
	if ( ! $description ) {
		$description = '<p>Premium quality ' . esc_html( $name ) . ' from Nutterly Good — handcrafted with care for everyday wellness.</p>';
	}

	$short = ng_sync_strip_description( $description );
	if ( strlen( $short ) > 220 ) {
		$short = substr( $short, 0, 217 ) . '...';
	}

	$gallery_ids = ng_sync_product_images( $item, $detail );

	$product = ng_sync_find_product_by_slug( $slug );
	if ( ! $product ) {
		$product = new WC_Product_Simple();
	}

	$product->set_name( $name );
	$product->set_slug( $slug );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_regular_price( (string) $price );
	$product->set_sale_price( '' );
	$product->set_price( (string) $price );
	$product->set_description( $description );
	$product->set_short_description( $short );

	if ( $weight_kg > 0 ) {
		$product->set_weight( (string) $weight_kg );
	}

	if ( ! empty( $gallery_ids ) ) {
		$product->set_image_id( $gallery_ids[0] );
		$product->set_gallery_image_ids( array_slice( $gallery_ids, 1 ) );
	}

	$product_id = $product->save();
	wp_set_object_terms( $product_id, ng_assign_categories( $name, $slug, $cats ), 'product_cat', false );

	$size_label = $weight ? $weight : 'Standard';
	$size_options = array(
		array(
			'label'         => $size_label,
			'image_id'      => (int) ( $gallery_ids[0] ?? 0 ),
			'price'         => (string) $price,
			'regular_price' => (string) $price,
			'mrp'           => (string) $price,
			'weight'        => $size_label,
		),
	);

	update_post_meta( $product_id, '_ng_subtitle', $short );
	update_post_meta( $product_id, '_ng_country_origin', 'India' );
	update_post_meta( $product_id, '_ng_mrp', (string) $price );
	update_post_meta( $product_id, '_ng_offer_price', (string) $price );
	update_post_meta( $product_id, '_ng_shelf_life', '9 Months' );
	update_post_meta( $product_id, '_ng_ingredients', ng_sync_guess_ingredients( $name ) );
	update_post_meta( $product_id, '_ng_packed_by', $packed_by );
	update_post_meta( $product_id, '_ng_farmley_sizes', wp_json_encode( $size_options ) );

	++$synced;
}

$deleted = 0;
foreach ( wc_get_products( array( 'limit' => -1, 'status' => array( 'publish', 'draft', 'private' ) ) ) as $product ) {
	if ( ! in_array( $product->get_slug(), $live_slugs, true ) ) {
		wp_delete_post( $product->get_id(), true );
		++$deleted;
		ng_sync_log( 'Removed extra product: ' . $product->get_name() );
	}
}

wp_cache_flush();

ng_sync_log( 'Synced: ' . $synced );
ng_sync_log( 'Removed extras: ' . $deleted );
ng_sync_log( 'Final product count: ' . count( wc_get_products( array( 'limit' => -1 ) ) ) );
ng_sync_log( '=== Live Shop Sync Complete ===' );