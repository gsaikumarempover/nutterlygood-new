<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

global $wpdb;

$upload = wp_upload_dir();
$slides = array(
	1 => '2026/06/slider/ng-slide-1-flavor-freshness.jpg',
	2 => '2026/06/slider/ng-slide-2-dry-fruits.jpg',
	3 => '2026/06/slider/ng-slide-3-chips-mixes.jpg',
	4 => '2026/06/slider/ng-slide-4-mouth-fresheners.jpg',
	5 => '2026/06/slider/ng-slide-5-brand-hero.jpg',
);

foreach ( $slides as $order => $rel ) {
	$full_url  = $upload['baseurl'] . '/' . $rel;
	$full_path = $upload['basedir'] . '/' . $rel;

	$attach_id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
			$rel
		)
	);

	$row = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT id, params FROM {$wpdb->prefix}revslider_slides WHERE slide_order = %d",
			$order
		)
	);
	if ( ! $row ) {
		continue;
	}

	$params = json_decode( $row->params, true );
	if ( ! is_array( $params ) ) {
		$params = array();
	}

	$params['bg']['type']            = 'image';
	$params['bg']['image']           = $full_url;
	$params['bg']['imageId']         = (int) $attach_id;
	$params['bg']['imageLib']        = 'medialibrary';
	$params['bg']['imageSourceType'] = 'full';
	$params['bg']['imageWidth']      = 1920;
	$params['bg']['imageHeight']     = 886;
	$params['bg']['fit']             = 'cover';
	$params['bg']['position']        = 'center center';

	$wpdb->update(
		$wpdb->prefix . 'revslider_slides',
		array( 'params' => wp_json_encode( $params ) ),
		array( 'id' => $row->id ),
		array( '%s' ),
		array( '%d' )
	);

	if ( $attach_id ) {
		$meta = wp_get_attachment_metadata( $attach_id );
		if ( is_array( $meta ) ) {
			$meta['width']  = 1920;
			$meta['height'] = 886;
			wp_update_attachment_metadata( $attach_id, $meta );
		}
	}

	echo "Slide $order -> $full_url\n";
}

// Top-level keys — RevSlider reads forceLazyLoading directly from global settings.
$global = array(
	'forceLazyLoading' => 'none',
	'lazyonbg'         => false,
);
update_option( 'revslider-global-settings', $global );

$slider = $wpdb->get_row( "SELECT id, params FROM {$wpdb->prefix}revslider_sliders WHERE alias = 'main-home' LIMIT 1" );
if ( $slider ) {
	$sp = json_decode( $slider->params, true );
	if ( is_array( $sp ) ) {
		$sp['general']['lazyLoad'] = 'none';
		$wpdb->update(
			$wpdb->prefix . 'revslider_sliders',
			array( 'params' => wp_json_encode( $sp ) ),
			array( 'id' => $slider->id ),
			array( '%s' ),
			array( '%d' )
		);
	}
}

// Clear Elementor cache for homepage.
$page_id = (int) get_option( 'page_on_front' );
delete_post_meta( $page_id, '_elementor_css' );
if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

wp_cache_flush();
echo "Global settings fixed, caches cleared.\n";