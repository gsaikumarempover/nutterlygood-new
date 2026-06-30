<?php
/**
 * Populate WooCommerce products with Farmley-style gallery, sizes, and meta.
 * Run: php enrich-farmley-products.php
 */
require __DIR__ . '/wp-load.php';

if ( ! class_exists( 'WooCommerce' ) ) {
	die( "WooCommerce not loaded\n" );
}

function ng_attachment_by_filename( $filename, $subdir = '' ) {
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

function ng_ensure_attachment( $path, $title, $subdir = '' ) {
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$basename = basename( $path );
	$existing = ng_attachment_by_filename( $basename, $subdir );
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

	$filetype = wp_check_filetype( $basename );
	$attach   = array(
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

$manifest_path = __DIR__ . '/product-ai-manifest.json';
$manifest      = file_exists( $manifest_path ) ? json_decode( file_get_contents( $manifest_path ), true ) : array();
$by_title      = array();
foreach ( $manifest as $row ) {
	$by_title[ strtolower( $row['title'] ) ] = $row;
}

$products_dir = WP_CONTENT_DIR . '/uploads/2026/06/ai-products/';
$source_dir   = $products_dir . 'source/';
$subdir       = '2026/06/ai-products';
$source_sub   = '2026/06/ai-products/source';
$packed_by    = 'Nutterly Good, CS-09, Etna Block, Rajapushpa Atria, Golden Mile Road, Kokapet, Hyderabad, Telangana 500075';

$ingredient_map = array(
	'trail'     => 'Black Currant, Pumpkin Seeds, Sunflower Seeds, Cranberry Slice, Blueberry, Cashew, Almond',
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
);

function ng_guess_ingredients( $name ) {
	global $ingredient_map;
	$lower = strtolower( $name );
	foreach ( $ingredient_map as $key => $val ) {
		if ( false !== strpos( $lower, $key ) ) {
			return $val;
		}
	}
	return 'Premium natural ingredients, spices, and edible oil';
}

function ng_extract_weight_label( $slug, $title ) {
	if ( preg_match( '/(\d{2,4})\s*g/i', $slug . ' ' . $title, $m ) ) {
		return $m[1] . ' g';
	}
	if ( preg_match( '/-(\d{2,4})-/', $slug, $m ) ) {
		return $m[1] . ' g';
	}
	return '160 g';
}

function ng_farmley_description( $title ) {
	$trail = "Nutterly Good's {$title} is a handpicked blend of premium dry fruits, nuts, dried berries, and seeds. "
		. "It delivers a natural energy boost without adulteration, rich in protein and omega 3. "
		. "Packed with antioxidants, it promotes overall wellness while satisfying your taste buds. "
		. "Enjoy straight from the pack, with yogurt, or in your favourite recipes.";

	$default = "Nutterly Good's {$title} is made with carefully sourced ingredients and slow-roasted in small batches. "
		. "It delivers a natural energy boost without adulteration, rich in protein and wholesome nutrients. "
		. "Enjoy straight from the pack, with yogurt, or in your favourite recipes.";

	return ( false !== stripos( $title, 'trail' ) || false !== stripos( $title, '7-in-1' ) ) ? $trail : $default;
}

function ng_product_overrides( $title ) {
	$key = strtolower( $title );
	if ( 'seed mix' === $key || '7-in-1 trail mix' === $key ) {
		return array(
			'title'       => '7-in-1 Trail Mix',
			'subtitle'    => '7-in-1 Trail Mix - Dried Cranberries, Almonds, Cashews, Dried Blueberries, Sunflower Seeds, Pumpkin Seeds, Black Currant',
			'weight'      => '160 g',
			'price'       => 249,
			'regular'     => 299,
			'mrp'         => 349,
			'ingredients' => 'Black Currant, Pumpkin Seeds, Sunflower Seeds, Cranberry Slice, Blueberry, Cashew, Almond',
			'description' => "Nutterly Good's 7-in-1 Trail Mix is a handpicked blend of 7 premium dry fruits, nuts, dried berries, and seeds. "
				. "Bringing together Almonds, Blackcurrant, Cashews, Sunflower Seeds, Blueberries, Pumpkin Seeds & Cranberries, it's a perfect on-the-go snack. "
				. "This wholesome mix provides a natural energy boost without any adulteration and is rich in protein and omega 3. "
				. "Packed with antioxidants, it promotes overall wellness while satisfying your taste buds. "
				. "Versatile and delicious, our trail mix can be enjoyed straight from the pack, sprinkled over yogurt, or incorporated into recipes.",
		);
	}
	return array();
}

$products = wc_get_products( array( 'limit' => -1, 'status' => 'publish', 'return' => 'objects' ) );
$updated  = 0;

foreach ( $products as $product ) {
	$id    = $product->get_id();
	$title = $product->get_name();
	$key   = strtolower( $title );
	$row   = $by_title[ $key ] ?? null;
	$over  = ng_product_overrides( $title );

	if ( ! empty( $over['title'] ) && $over['title'] !== $title ) {
		$product->set_name( $over['title'] );
		$title = $over['title'];
		$key   = strtolower( $title );
		$row   = $by_title['seed mix'] ?? $row;
	}

	$gallery_ids = array();

	if ( $row && ! empty( $row['file'] ) && file_exists( $products_dir . $row['file'] ) ) {
		$feat = ng_ensure_attachment( $products_dir . $row['file'], $title . ' pack', $subdir );
		if ( $feat ) {
			$gallery_ids[] = $feat;
		}
	}

	if ( $row && ! empty( $row['file'] ) ) {
		$stem = pathinfo( $row['file'], PATHINFO_FILENAME );
		foreach ( array( 'angle-top', 'angle-side', 'angle-close' ) as $suffix ) {
			$angle_file = $stem . '-' . $suffix . '.jpg';
			if ( ! file_exists( $products_dir . $angle_file ) ) {
				continue;
			}
			$aid = ng_ensure_attachment( $products_dir . $angle_file, $title . ' ' . $suffix, $subdir );
			if ( $aid && ! in_array( $aid, $gallery_ids, true ) ) {
				$gallery_ids[] = $aid;
			}
		}
	}

	$gallery_ids = array_values( array_unique( $gallery_ids ) );
	if ( ! empty( $gallery_ids ) ) {
		$product->set_image_id( $gallery_ids[0] );
		$product->set_gallery_image_ids( array_slice( $gallery_ids, 1 ) );
	}

	$price   = ! empty( $over['price'] ) ? (float) $over['price'] : (float) $product->get_price();
	$regular = ! empty( $over['regular'] ) ? (float) $over['regular'] : round( $price * 1.18, 0 );
	$mrp     = ! empty( $over['mrp'] ) ? (float) $over['mrp'] : round( $regular * 1.12, 0 );
	$weight  = $over['weight'] ?? ng_extract_weight_label( $row['slug'] ?? '', $title );
	$weight_kg = (float) preg_replace( '/[^0-9.]/', '', $weight ) / 1000;

	$product->set_regular_price( (string) $regular );
	$product->set_sale_price( (string) $price );
	$product->set_price( (string) $price );
	if ( $weight_kg > 0 ) {
		$product->set_weight( (string) $weight_kg );
	}

	$subtitle = $over['subtitle'] ?? ( $title . ' - Premium handpicked snack crafted for everyday wellness' );
	$desc     = $over['description'] ?? ng_farmley_description( $title );

	$product->set_short_description( $subtitle );
	$product->set_description( $desc );
	$product->save();

	$size_options = array();
	$jar_label    = preg_match( '/trail/i', $title ) ? 'Trail mix 325g Jar' : preg_replace( '/\d+\s*g/i', '325g Jar', $weight );
	$labels       = array(
		array( 'label' => $weight, 'mult' => 1, 'img' => $gallery_ids[0] ?? 0 ),
		array( 'label' => 'Pack of 3 (' . preg_replace( '/\s+/', '', $weight ) . ' each)', 'mult' => 2.85, 'img' => $gallery_ids[1] ?? ( $gallery_ids[0] ?? 0 ) ),
		array( 'label' => $jar_label, 'mult' => 1.65, 'img' => $gallery_ids[2] ?? ( $gallery_ids[0] ?? 0 ) ),
	);

	foreach ( $labels as $opt ) {
		$size_options[] = array(
			'label'         => $opt['label'],
			'image_id'      => (int) $opt['img'],
			'price'         => (string) round( $price * $opt['mult'], 0 ),
			'regular_price' => (string) round( $regular * $opt['mult'], 0 ),
			'mrp'           => (string) round( $mrp * $opt['mult'], 0 ),
			'weight'        => $opt['label'],
		);
	}

	update_post_meta( $id, '_ng_subtitle', $subtitle );
	update_post_meta( $id, '_ng_country_origin', 'India' );
	update_post_meta( $id, '_ng_mrp', (string) $mrp );
	update_post_meta( $id, '_ng_offer_price', (string) $price );
	update_post_meta( $id, '_ng_shelf_life', '9 Months' );
	update_post_meta( $id, '_ng_ingredients', $over['ingredients'] ?? ng_guess_ingredients( $title ) );
	update_post_meta( $id, '_ng_packed_by', $packed_by );
	update_post_meta( $id, '_ng_farmley_sizes', wp_json_encode( $size_options ) );

	$updated++;
	echo "Updated #{$id} {$title} — " . count( $gallery_ids ) . " images, " . count( $size_options ) . " sizes\n";
}

echo "\nDone. Updated {$updated} products.\n";