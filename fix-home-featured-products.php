<?php
/**
 * Persist GreenPath-style Featured Products settings in homepage Elementor data.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once get_template_directory() . '/inc/farmley/home-featured-products.php';

$page_id = (int) get_option( 'page_on_front' );
if ( ! $page_id ) {
	fwrite( STDERR, "No front page set.\n" );
	exit( 1 );
}

$raw = get_post_meta( $page_id, '_elementor_data', true );
if ( ! $raw ) {
	fwrite( STDERR, "No Elementor data on page {$page_id}.\n" );
	exit( 1 );
}

$elements = json_decode( $raw, true );
if ( ! is_array( $elements ) ) {
	fwrite( STDERR, "Invalid Elementor JSON.\n" );
	exit( 1 );
}

$settings = nuttergood_farmley_home_featured_widget_settings();
$updated  = false;

$walker = static function ( array &$nodes ) use ( &$walker, $settings, &$updated ) {
	foreach ( $nodes as &$el ) {
		if ( ! empty( $el['widgetType'] ) && 'greenpath_core_product_list' === $el['widgetType'] && ( $el['id'] ?? '' ) === '3a8d545' ) {
			$el['settings'] = array_merge( $el['settings'] ?? array(), $settings );
			$updated        = true;
			echo "Updated featured widget 3a8d545: posts_per_page=8, columns=4\n";
		}
		if ( ! empty( $el['elements'] ) ) {
			$walker( $el['elements'] );
		}
	}
};

$walker( $elements );

if ( ! $updated ) {
	fwrite( STDERR, "Widget 3a8d545 not found.\n" );
	exit( 1 );
}

update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
delete_post_meta( $page_id, '_elementor_css' );

if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

echo "Homepage Elementor data saved. Hard-refresh the front page.\n";