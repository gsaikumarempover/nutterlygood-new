<?php
/**
 * Full migration: remove demo content, import live products, configure RevSlider + homepage.
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

function ng_log( $message ) {
	echo $message . PHP_EOL;
}

function ng_get_or_create_category( $name, $slug, $parent_id = 0 ) {
	$existing = get_term_by( 'slug', $slug, 'product_cat' );
	if ( $existing && ! is_wp_error( $existing ) ) {
		wp_update_term(
			$existing->term_id,
			'product_cat',
			array(
				'name'   => $name,
				'parent' => $parent_id,
			)
		);
		return (int) $existing->term_id;
	}
	$result = wp_insert_term(
		$name,
		'product_cat',
		array(
			'slug'   => $slug,
			'parent' => $parent_id,
		)
	);
	if ( is_wp_error( $result ) ) {
		ng_log( 'Category error: ' . $result->get_error_message() );
		return 0;
	}
	return (int) $result['term_id'];
}

function ng_parse_products_from_html( $file ) {
	if ( ! file_exists( $file ) ) {
		return array();
	}
	$html     = file_get_contents( $file );
	$products = array();
	preg_match_all(
		'/<form[^>]*class="oe_product_cart[^"]*"[^>]*aria-label="([^"]+)"[^>]*>(.*?)<\/form>/s',
		$html,
		$forms,
		PREG_SET_ORDER
	);
	foreach ( $forms as $form ) {
		$name  = trim( $form[1] );
		$block = $form[2];
		$img   = '';
		if ( preg_match( '/product\.product\/(\d+)\/image_1024\/([^"?]+)/', $block, $im ) ) {
			$img = 'https://www.nutterlygood.com/web/image/product.product/' . $im[1] . '/image_1024/' . rawurldecode( $im[2] );
		}
		$price = 0;
		if ( preg_match( '/oe_currency_value">([\d.]+)/', $block, $pr ) ) {
			$price = (float) $pr[1];
		}
		$slug = '';
		if ( preg_match( '/href="\/shop\/([^"]+)"/', $block, $sl ) ) {
			$slug = $sl[1];
		}
		$products[ $name ] = array(
			'name'  => $name,
			'price' => $price,
			'image' => $img,
			'slug'  => $slug,
		);
	}
	return $products;
}

function ng_fetch_live_shop_pages() {
	$pages = array();
	for ( $p = 1; $p <= 6; $p++ ) {
		$url = 1 === $p ? 'https://www.nutterlygood.com/shop' : 'https://www.nutterlygood.com/shop/page/' . $p;
		$response = wp_remote_get(
			$url,
			array(
				'timeout'   => 30,
				'sslverify' => false,
			)
		);
		if ( is_wp_error( $response ) ) {
			continue;
		}
		$body = wp_remote_retrieve_body( $response );
		if ( $body ) {
			$local = __DIR__ . '/live-shop-fetch-p' . $p . '.html';
			file_put_contents( $local, $body );
			$pages[] = $local;
		}
	}
	return $pages;
}

function ng_assign_categories( $name, $slug, $cats ) {
	$haystack = strtolower( $name . ' ' . $slug );

	if ( preg_match( '/^mx-|-mx-|\bmix\b/', $haystack ) ) {
		return array( $cats['mixes'] );
	}
	if ( preg_match( '/^ch-|-ch-|\bchip|\bsticks\b|\bcorn\b|\bragi\b/', $haystack ) ) {
		return array( $cats['chips'] );
	}
	if ( preg_match( '/brittle|brt-/', $haystack ) ) {
		return array( $cats['brittles'] );
	}
	if ( preg_match( '/^mf-|mouth|paan|goli|peda|shot|papad|dilranjan|rajwadi|anardana|jamun|guava|hing/', $haystack ) ) {
		return array( $cats['mouth'] );
	}

	$ids = array( $cats['dry_fruits'] );
	if ( preg_match( '/almond|almd/', $haystack ) ) {
		$ids[] = $cats['almonds'];
	} elseif ( preg_match( '/cashew|chw/', $haystack ) ) {
		$ids[] = $cats['cashews'];
	} elseif ( preg_match( '/kishmish|kish/', $haystack ) ) {
		$ids[] = $cats['khishmish'];
	} elseif ( preg_match( '/cranberry|\bcr-/', $haystack ) ) {
		$ids[] = $cats['cranberry'];
	} elseif ( preg_match( '/walnut|\bwa-/', $haystack ) ) {
		$ids[] = $cats['walnuts'];
	} else {
		return array( $cats['brittles'] );
	}
	return array_values( array_unique( $ids ) );
}

function ng_download_image_to_media( $url, $title, $target_size = 800 ) {
	if ( empty( $url ) ) {
		return 0;
	}

	$tmp = download_url( $url, 60 );
	if ( is_wp_error( $tmp ) ) {
		ng_log( 'Image download failed for ' . $title . ': ' . $tmp->get_error_message() );
		return 0;
	}

	$editor = wp_get_image_editor( $tmp );
	if ( ! is_wp_error( $editor ) ) {
		$size = $editor->get_size();
		if ( ! empty( $size['width'] ) && ! empty( $size['height'] ) ) {
			$max = max( $size['width'], $size['height'] );
			if ( $max > $target_size ) {
				$editor->resize( $target_size, $target_size, false );
			}
			$canvas = wp_get_image_editor( $tmp );
			if ( ! is_wp_error( $canvas ) ) {
				$canvas->resize( $target_size, $target_size, false );
				$saved = $canvas->save( $tmp );
				if ( ! is_wp_error( $saved ) ) {
					$tmp = $saved['path'];
				}
			}
		}
	}

	$file_array = array(
		'name'     => sanitize_file_name( $title ) . '.jpg',
		'tmp_name' => $tmp,
	);
	$attachment_id = media_handle_sideload( $file_array, 0, $title );
	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $tmp );
		ng_log( 'Media sideload failed for ' . $title . ': ' . $attachment_id->get_error_message() );
		return 0;
	}
	return (int) $attachment_id;
}

function ng_copy_revslider_assets() {
	$src_root = __DIR__ . '/export/revslider/main-home-extracted/images/uploads';
	$dest     = WP_CONTENT_DIR . '/uploads';
	if ( ! is_dir( $src_root ) ) {
		return;
	}
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $src_root, RecursiveDirectoryIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::SELF_FIRST
	);
	foreach ( $iterator as $item ) {
		$rel  = str_replace( '\\', '/', substr( $item->getPathname(), strlen( $src_root ) + 1 ) );
		$dest_path = $dest . '/' . $rel;
		if ( $item->isDir() ) {
			if ( ! is_dir( $dest_path ) ) {
				wp_mkdir_p( $dest_path );
			}
		} else {
			if ( ! file_exists( $dest_path ) ) {
				wp_mkdir_p( dirname( $dest_path ) );
				copy( $item->getPathname(), $dest_path );
			}
		}
	}
	ng_log( 'RevSlider asset images copied to uploads.' );
}

function ng_download_live_hero_image() {
	$url = 'https://www.nutterlygood.com/web/image/4861-b051588e/Frame%2012.webp';
	$dest_dir = WP_CONTENT_DIR . '/uploads/2024/01';
	wp_mkdir_p( $dest_dir );
	$dest = $dest_dir . '/ng-hero-frame12.webp';
	if ( ! file_exists( $dest ) ) {
		$response = wp_remote_get( $url, array( 'timeout' => 60, 'sslverify' => false ) );
		if ( ! is_wp_error( $response ) ) {
			file_put_contents( $dest, wp_remote_retrieve_body( $response ) );
			ng_log( 'Downloaded live hero image.' );
		}
	}
}

function ng_build_custom_slider_json() {
	$export_file = __DIR__ . '/export/revslider/main-home-extracted/slider_export.txt';
	$data        = json_decode( file_get_contents( $export_file ), true );

	$data['title'] = 'Nutterly Good Home';
	$data['alias'] = 'main-home';

	$slide_copy = array(
		array(
			'kicker'  => 'Flavor & Freshness',
			'headline' => "Your Everyday Treat of Tasty Goodness.",
			'sub'     => 'From ancient authentic roots to modern wellness trends, dry fruits and mouth fresheners have always been part of India\'s rich culinary heritage.',
			'cta'     => 'Free delivery for orders above ₹2,500',
			'bg'      => 'uploads/2023/09/h1-rev-img-1.jpg',
		),
		array(
			'kicker'  => 'Premium Dry Fruits',
			'headline' => 'Handpicked Almonds, Cashews & More',
			'sub'     => 'Bold flavours and wholesome crunch — roasted, seasoned, and crafted for everyday indulgence.',
			'cta'     => 'Shop Dry Fruits',
			'bg'      => 'uploads/2024/01/h1-rev-img-5.jpg',
		),
		array(
			'kicker'  => 'Crunchy Chips & Mixes',
			'headline' => 'Wholesome Snacking, Reimagined',
			'sub'     => 'From masala chips to protein mixes — tasty goodness for every mood and moment.',
			'cta'     => 'Explore Mixes & Chips',
			'bg'      => 'uploads/2024/01/h1-rev-img-7.jpg',
		),
		array(
			'kicker'  => 'Mouth Freshners',
			'headline' => 'Traditional Paan & Refreshing Shots',
			'sub'     => 'Authentic Indian mouth fresheners crafted with care — paan, goli, and fruity shots.',
			'cta'     => 'Discover Mouth Freshners',
			'bg'      => 'uploads/2023/09/h1-rev-img-2.png',
		),
		array(
			'kicker'  => 'Nutterly Good',
			'headline' => 'Your Everyday Treat of Tasty Goodness',
			'sub'     => 'Premium quality dry fruits, brittles, chips and mixes — delivered across India.',
			'cta'     => 'Start Shopping Now',
			'bg'      => 'uploads/2023/09/h1-rev-img-3.png',
		),
	);

	$template_slide = $data['slides'][0];
	$new_slides     = array();

	foreach ( $slide_copy as $index => $copy ) {
		$slide = $template_slide;
		$slide['id']          = (string) ( $index + 1 );
		$slide['slide_order'] = (string) ( $index + 1 );
		$slide['params']['bg']['image']     = $copy['bg'];
		$slide['params']['bg']['imageLib']  = 'medialibrary';
		$slide['params']['version']         = '6.6.20';

		$layer_idx = 0;
		foreach ( $slide['layers'] as $key => &$layer ) {
			if ( empty( $layer['text'] ) ) {
				continue;
			}
			if ( 0 === $layer_idx ) {
				$layer['text'] = $copy['kicker'];
				if ( isset( $layer['idle']['color']['d'] ) ) {
					$layer['idle']['color']['d']['v'] = '#B99531';
				}
			} else {
				$layer['text'] = $copy['headline'] . "\n" . $copy['sub'] . "\n" . $copy['cta'];
				if ( isset( $layer['idle']['color']['d'] ) ) {
					$layer['idle']['color']['d']['v'] = '#0C533D';
				}
			}
			if ( isset( $layer['idle']['fontSize']['d'] ) && $layer_idx === 1 ) {
				$layer['idle']['fontSize']['d']['v'] = '52px';
			}
			++$layer_idx;
		}
		unset( $layer );
		$new_slides[] = $slide;
	}

	$data['slides'] = $new_slides;

	if ( isset( $data['params']['nav']['bullets']['presets']['bullet_color'] ) ) {
		$data['params']['nav']['bullets']['presets']['bullet_color'] = '#B99531';
	}
	if ( isset( $data['params']['general']['slideshow']['stopAtSlide'] ) ) {
		$data['params']['general']['slideshow']['stopAtSlide'] = count( $new_slides );
	}

	return $data;
}

function ng_import_revslider() {
	$rs_admin_includes = array(
		'admin/includes/license.class.php',
		'admin/includes/addons.class.php',
		'admin/includes/template.class.php',
		'admin/includes/functions-admin.class.php',
		'admin/includes/folder.class.php',
		'admin/includes/import.class.php',
	);
	foreach ( $rs_admin_includes as $include ) {
		$path = WP_PLUGIN_DIR . '/revslider/' . $include;
		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
	if ( ! class_exists( 'RevSliderSliderImport' ) ) {
		ng_log( 'RevSlider not available.' );
		return false;
	}

	ng_copy_revslider_assets();
	ng_download_live_hero_image();

	$slider_data = ng_build_custom_slider_json();
	$work_dir    = WP_CONTENT_DIR . '/uploads/ng-rev-import';
	wp_mkdir_p( $work_dir );

	file_put_contents( $work_dir . '/slider_export.txt', wp_json_encode( $slider_data ) );
	file_put_contents( $work_dir . '/navigation.txt', file_get_contents( __DIR__ . '/export/revslider/main-home-extracted/navigation.txt' ) );

	$images_src = __DIR__ . '/export/revslider/main-home-extracted/images';
	$images_dst = $work_dir . '/images';
	if ( is_dir( $images_src ) ) {
		ng_recursive_copy( $images_src, $images_dst );
	}

	$zip_path = $work_dir . '/main-home-custom.zip';
	if ( file_exists( $zip_path ) ) {
		unlink( $zip_path );
	}
	$zip = new ZipArchive();
	if ( true !== $zip->open( $zip_path, ZipArchive::CREATE ) ) {
		ng_log( 'Could not create RevSlider zip.' );
		return false;
	}
	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $work_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::LEAVES_ONLY
	);
	foreach ( $files as $file ) {
		if ( $file->isDir() ) {
			continue;
		}
		$path = $file->getRealPath();
		if ( basename( $path ) === 'main-home-custom.zip' ) {
			continue;
		}
		$rel = substr( $path, strlen( $work_dir ) + 1 );
		$zip->addFile( $path, str_replace( '\\', '/', $rel ) );
	}
	$zip->close();

	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->prefix}revslider_sliders" );
	$wpdb->query( "DELETE FROM {$wpdb->prefix}revslider_slides" );
	$wpdb->query( "DELETE FROM {$wpdb->prefix}revslider_static_slides" );
	$wpdb->query( "DELETE FROM {$wpdb->prefix}revslider_css" );

	$import = new RevSliderSliderImport();
	$result = $import->import_slider( true, $zip_path );

	if ( ! empty( $result['success'] ) ) {
		ng_log( 'RevSlider imported: main-home (' . count( $slider_data['slides'] ) . ' slides).' );
		return true;
	}

	ng_log( 'RevSlider import failed: ' . wp_json_encode( $result ) );
	return false;
}

function ng_recursive_copy( $src, $dst ) {
	if ( ! is_dir( $dst ) ) {
		wp_mkdir_p( $dst );
	}
	$dir = opendir( $src );
	while ( false !== ( $file = readdir( $dir ) ) ) {
		if ( in_array( $file, array( '.', '..' ), true ) ) {
			continue;
		}
		$from = $src . '/' . $file;
		$to   = $dst . '/' . $file;
		if ( is_dir( $from ) ) {
			ng_recursive_copy( $from, $to );
		} else {
			copy( $from, $to );
		}
	}
	closedir( $dir );
}

function ng_delete_demo_content() {
	$keep_pages = array( 36, 9, 7246, 7247, 7248, 3431, 3437 );

	$products = wc_get_products( array( 'limit' => -1, 'status' => array( 'publish', 'draft', 'private' ) ) );
	foreach ( $products as $product ) {
		wp_delete_post( $product->get_id(), true );
	}
	ng_log( 'Deleted ' . count( $products ) . ' demo products.' );

	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		)
	);
	foreach ( $posts as $post_id ) {
		wp_delete_post( $post_id, true );
	}
	ng_log( 'Deleted ' . count( $posts ) . ' demo posts.' );

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		)
	);
	$deleted_pages = 0;
	foreach ( $pages as $page_id ) {
		if ( in_array( (int) $page_id, $keep_pages, true ) ) {
			continue;
		}
		wp_delete_post( $page_id, true );
		++$deleted_pages;
	}
	ng_log( 'Deleted ' . $deleted_pages . ' demo pages (kept essential pages).' );
}

function ng_import_live_products( $cats ) {
	$product_map = array();
	$files       = glob( __DIR__ . '/live-shop*.html' );
	$files       = array_merge( $files, ng_fetch_live_shop_pages() );
	$files       = array_unique( $files );

	foreach ( $files as $file ) {
		$product_map = array_merge( $product_map, ng_parse_products_from_html( $file ) );
	}

	$imported = 0;
	foreach ( $product_map as $item ) {
		$attachment_id = ng_download_image_to_media( $item['image'], $item['name'] );
		$product       = new WC_Product_Simple();
		$product->set_name( $item['name'] );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_regular_price( (string) $item['price'] );
		$product->set_price( (string) $item['price'] );
		$product->set_slug( $item['slug'] );
		$product->set_description( 'Premium quality ' . $item['name'] . ' from Nutterly Good.' );
		$product->set_short_description( 'Handcrafted with care. ₹' . number_format( $item['price'], 2 ) );
		if ( $attachment_id ) {
			$product->set_image_id( $attachment_id );
		}
		$product_id = $product->save();
		wp_set_object_terms( $product_id, ng_assign_categories( $item['name'], $item['slug'], $cats ), 'product_cat', false );
		++$imported;
	}
	ng_log( 'Imported ' . $imported . ' live products.' );
}

function ng_update_homepage( $cats ) {
	$page_id = (int) get_option( 'page_on_front' );
	$data    = get_post_meta( $page_id, '_elementor_data', true );
	if ( ! $data ) {
		return;
	}

	$elements = json_decode( $data, true );
	if ( ! is_array( $elements ) ) {
		return;
	}

	$shop_url       = get_permalink( wc_get_page_id( 'shop' ) );
	$dry_fruits_url = get_term_link( $cats['dry_fruits'], 'product_cat' );

	$elements[0] = array(
		'id'       => 'c81ed26',
		'elType'   => 'container',
		'settings' => array(
			'flex_direction'         => 'row',
			'content_width'          => 'full',
			'flex_gap'               => array(
				'column'   => '0',
				'row'      => '0',
				'isLinked' => false,
				'unit'     => 'px',
				'size'     => 0,
			),
			'padding'                => array(
				'unit'     => 'px',
				'top'      => '0',
				'right'    => '0',
				'bottom'   => '0',
				'left'     => '0',
				'isLinked' => false,
			),
			'qodef_offset_image_items' => array(),
		),
		'elements' => array(
			array(
				'id'         => '0f125b9',
				'elType'     => 'widget',
				'settings'   => array(
					'revslidertitle' => 'Nutterly Good Home',
					'shortcode'      => '[rev_slider alias="main-home" slidertitle="Nutterly Good Home"][/rev_slider]',
				),
				'elements'   => array(),
				'widgetType' => 'slider_revolution',
			),
		),
		'isInner'  => false,
	);

	$walker = function ( &$items ) use ( &$walker, $cats, $shop_url, $dry_fruits_url ) {
		foreach ( $items as &$element ) {
			if ( ! empty( $element['widgetType'] ) ) {
				$element['widgetType'] = str_replace( 'nutterlygood_core_', 'greenpath_core_', $element['widgetType'] );
			}
			if ( isset( $element['settings'] ) && is_array( $element['settings'] ) ) {
				$widget = $element['widgetType'] ?? '';
				if ( 'greenpath_core_section_title' === $widget ) {
					if ( isset( $element['settings']['title'] ) && stripos( $element['settings']['title'], 'Blog' ) !== false ) {
						$element['settings']['title'] = 'Why Nutterly Good';
						$element['settings']['text']  = 'Wholesome dry fruits and mouth fresheners for everyday goodness';
					}
				}
				if ( 'greenpath_core_product_list' === $widget ) {
					$element['settings']['tax']      = 'product_cat';
					$element['settings']['tax_slug'] = ( isset( $element['settings']['layout'] ) && 'horizontal' === $element['settings']['layout'] ) ? 'mixes' : 'dry-fruits';
				}
				if ( 'greenpath_core_product_category_list' === $widget ) {
					$element['settings']['taxonomy_slugs'] = 'dry-fruits,chips,mixes,brittles,mouth-fresheners';
					$element['settings']['taxonomy_ids']   = implode(
						', ',
						array(
							$cats['dry_fruits'],
							$cats['chips'],
							$cats['mixes'],
							$cats['brittles'],
							$cats['mouth'],
						)
					);
				}
				if ( 'greenpath_core_button' === $widget && isset( $element['settings']['link'] ) && is_array( $element['settings']['link'] ) ) {
					$element['settings']['link'] = $element['settings']['link']['url'] ?? $shop_url;
				}
				if ( 'greenpath_core_blog_list' === $widget ) {
					$element['settings']['posts_per_page'] = '0';
				}
			}
			if ( ! empty( $element['elements'] ) ) {
				$walker( $element['elements'] );
			}
		}
	};
	$walker( $elements );

	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
	delete_post_meta( $page_id, '_elementor_css' );
	wp_update_post(
		array(
			'ID'         => $page_id,
			'post_title' => 'Home',
		)
	);

	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	ng_log( 'Homepage updated with RevSlider (page ' . $page_id . ').' );
}

function ng_rebuild_menus( $cats ) {
	$shop_url       = get_permalink( wc_get_page_id( 'shop' ) );
	$dry_fruits_url = get_term_link( $cats['dry_fruits'], 'product_cat' );
	$chips_url      = get_term_link( $cats['chips'], 'product_cat' );
	$mixes_url      = get_term_link( $cats['mixes'], 'product_cat' );
	$brittles_url   = get_term_link( $cats['brittles'], 'product_cat' );
	$mouth_url      = get_term_link( $cats['mouth'], 'product_cat' );
	$about_url      = get_permalink( 3431 );
	$contact_url    = get_permalink( 3437 );
	$home_url       = home_url( '/' );

	$main_menu_items = array(
		array( 'title' => 'Home', 'url' => $home_url ),
		array( 'title' => 'Dry Fruits', 'url' => $dry_fruits_url ),
		array( 'title' => 'Chips', 'url' => $chips_url ),
		array( 'title' => 'Mixes', 'url' => $mixes_url ),
		array( 'title' => 'Brittles', 'url' => $brittles_url ),
		array( 'title' => 'Mouth Freshners', 'url' => $mouth_url ),
		array( 'title' => 'Shop', 'url' => $shop_url ),
		array( 'title' => 'About Us', 'url' => $about_url ),
		array( 'title' => 'Contact Us', 'url' => $contact_url ),
	);

	$clear_and_add = function ( $menu_id, $items ) {
		$existing = wp_get_nav_menu_items( $menu_id );
		if ( $existing ) {
			foreach ( $existing as $item ) {
				wp_delete_post( $item->ID, true );
			}
		}
		$order = 1;
		foreach ( $items as $item ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'    => $item['title'],
					'menu-item-url'      => $item['url'],
					'menu-item-status'   => 'publish',
					'menu-item-type'     => 'custom',
					'menu-item-position' => $order++,
				)
			);
		}
	};

	foreach ( array( 76, 71, 72, 70 ) as $menu_id ) {
		$clear_and_add( $menu_id, $main_menu_items );
	}
	ng_log( 'Menus rebuilt to match live site.' );
}

// --- Run migration ---
if ( defined( 'NG_MIGRATION_LIB_ONLY' ) && NG_MIGRATION_LIB_ONLY ) {
	return;
}

ng_log( '=== Nutterly Good Migration Start ===' );

update_option( 'woocommerce_currency', 'INR' );
update_option( 'woocommerce_currency_pos', 'left' );

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

$cats = compact( 'dry_fruits', 'almonds', 'cashews', 'khishmish', 'cranberry', 'walnuts', 'chips', 'mixes', 'brittles', 'mouth' );

ng_delete_demo_content();
ng_import_live_products( $cats );
ng_import_revslider();
ng_update_homepage( $cats );
ng_rebuild_menus( $cats );

wp_cache_flush();
ng_log( '=== Migration Complete ===' );
ng_log( 'Products: ' . count( wc_get_products( array( 'limit' => -1 ) ) ) );
ng_log( 'Home: ' . home_url( '/' ) );
ng_log( 'Shop: ' . get_permalink( wc_get_page_id( 'shop' ) ) );