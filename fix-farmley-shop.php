<?php
/**
 * Fix shop filters sidebar, popular products, title background, demo banners.
 * Run: php fix-farmley-shop.php
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

function ng_shop_log( $msg ) {
	echo $msg . PHP_EOL;
}

function ng_shop_ensure_attachment( $path, $title, $subdir ) {
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$basename = basename( $path );
	global $wpdb;
	$like = '%/' . $wpdb->esc_like( $basename );
	$aid  = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value LIKE %s ORDER BY post_id DESC LIMIT 1",
			$like
		)
	);
	if ( $aid ) {
		return $aid;
	}

	$upload = wp_upload_bits( $basename, null, file_get_contents( $path ) ); // phpcs:ignore
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$filetype  = wp_check_filetype( $basename );
	$attach_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	if ( ! is_wp_error( $attach_id ) ) {
		wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $upload['file'] ) );
		return (int) $attach_id;
	}
	return 0;
}

function ng_shop_generate_title_banner() {
	$dir = WP_CONTENT_DIR . '/uploads/2026/06/shop-assets';
	wp_mkdir_p( $dir );

	$fallbacks = array(
		WP_CONTENT_DIR . '/uploads/2024/01/ng-hero-frame12.webp',
		WP_CONTENT_DIR . '/uploads/2024/02/shop-list-land-2.jpg',
		WP_CONTENT_DIR . '/uploads/2024/02/land-home-img-7.jpg',
	);
	foreach ( $fallbacks as $src ) {
		if ( file_exists( $src ) ) {
			$dest = $dir . '/' . basename( $src );
			if ( ! file_exists( $dest ) ) {
				copy( $src, $dest );
			}
			$aid = ng_shop_ensure_attachment( $dest, 'Nutterly Good Shop Banner', '2026/06/shop-assets' );
			if ( $aid ) {
				ng_shop_log( 'Shop title banner from ' . basename( $src ) );
				return $aid;
			}
		}
	}

	if ( ! function_exists( 'imagecreatetruecolor' ) ) {
		return 0;
	}

	$file = $dir . '/ng-shop-title-banner.jpg';

	if ( ! file_exists( $file ) ) {
		$w = 1920;
		$h = 360;
		$img = imagecreatetruecolor( $w, $h );
		$green = imagecolorallocate( $img, 12, 83, 61 );
		$gold  = imagecolorallocate( $img, 185, 149, 49 );
		$cream = imagecolorallocate( $img, 252, 244, 235 );
		imagefilledrectangle( $img, 0, 0, $w, $h, $green );

		for ( $i = 0; $i < 14; $i++ ) {
			$x = 120 + ( $i * 130 );
			$y = 80 + ( $i % 3 ) * 40;
			imagefilledellipse( $img, $x, $y, 90, 90, $cream );
			imagefilledellipse( $img, $x + 20, $y + 10, 70, 70, $gold );
		}

		for ( $i = 0; $i < 10; $i++ ) {
			$x = 1000 + ( $i * 95 );
			$y = 120 + ( ( $i % 2 ) * 80 );
			imagefilledellipse( $img, $x, $y, 110, 110, $cream );
			imagefilledellipse( $img, $x, $y, 80, 80, $green );
		}

		imagestring( $img, 5, 60, 20, 'Nutterly Good — Premium Dry Fruits & Snacks', $cream );
		imagejpeg( $img, $file, 92 );
		imagedestroy( $img );
		ng_shop_log( 'Generated shop title banner.' );
	}

	return ng_shop_ensure_attachment( $file, 'Nutterly Good Shop Banner', '2026/06/shop-assets' );
}

function ng_shop_set_popular_sales() {
	$products = wc_get_products( array( 'limit' => -1, 'status' => 'publish', 'return' => 'ids' ) );
	$rank     = count( $products );
	foreach ( $products as $pid ) {
		update_post_meta( $pid, 'total_sales', max( 5, $rank * 3 ) );
		--$rank;
	}
	ng_shop_log( 'Set total_sales on ' . count( $products ) . ' products for popularity sorting.' );
}

function ng_shop_update_popular_widget() {
	$widgets = get_option( 'widget_greenpath_core_product_list', array() );
	if ( empty( $widgets[2] ) ) {
		return;
	}

	$widgets[2]['posts_per_page']       = '5';
	$widgets[2]['orderby']              = 'popularity';
	$widgets[2]['order']                = 'DESC';
	$widgets[2]['filterby']             = 'best_selling';
	$widgets[2]['images_proportion']    = 'thumbnail';
	$widgets[2]['custom_image_width']   = '';
	$widgets[2]['custom_image_height']  = '';
	$widgets[2]['layout']               = 'info-right';
	$widgets[2]['columns']              = '1';
	$widgets[2]['enable_wishlist']      = 'no';
	$widgets[2]['enable_quickview']     = 'no';
	$widgets[2]['enable_compare_product']= 'no';
	$widgets[2]['item_enable_border']    = 'no';

	update_option( 'widget_greenpath_core_product_list', $widgets );
	ng_shop_log( 'Updated Popular Products widget (best selling, thumbnails).' );
}

function ng_shop_clean_sidebar_widgets() {
	$sidebars = get_option( 'sidebars_widgets', array() );
	$sidebars['qodef-product-list-sidebar-widget-area'] = array(
		'greenpath_core_title_widget-2',
		'greenpath_core_product_list-2',
	);
	update_option( 'sidebars_widgets', $sidebars );
	ng_shop_log( 'Removed demo banner widgets from shop filter sidebar.' );
}

function ng_shop_set_title_meta( $banner_id ) {
	$shop_id = (int) get_option( 'woocommerce_shop_page_id' );
	if ( ! $shop_id ) {
		return;
	}

	update_post_meta( $shop_id, 'qodef_page_title_background_image', (string) $banner_id );
	update_post_meta( $shop_id, 'qodef_page_title_background_color', '#0C533D' );
	update_post_meta( $shop_id, 'qodef_page_title_height', '260' );
	update_post_meta( $shop_id, 'qodef_page_title_color', '#FFFFFF' );
	update_post_meta( $shop_id, 'qodef_page_title_background_image_behavior', '' );

	ng_shop_log( 'Shop page title background set (page ' . $shop_id . ').' );
}

ng_shop_log( '=== Fix Farmley Shop ===' );
$banner_id = ng_shop_generate_title_banner();
ng_shop_set_title_meta( $banner_id );
ng_shop_set_popular_sales();
ng_shop_update_popular_widget();
ng_shop_clean_sidebar_widgets();
wp_cache_flush();
ng_shop_log( '=== Done ===' );