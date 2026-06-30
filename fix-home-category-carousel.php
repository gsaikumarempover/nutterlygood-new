<?php
/**
 * Homepage category icons: 5 parent categories in a static row (no carousel).
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$page_id = (int) get_option( 'page_on_front' );
$raw     = get_post_meta( $page_id, '_elementor_data', true );
$data    = json_decode( $raw, true );

if ( ! is_array( $data ) ) {
	echo "Invalid Elementor data.\n";
	exit( 1 );
}

$slugs = array(
	'dry-fruits',
	'chips',
	'mixes',
	'brittles',
	'mouth-fresheners',
);

$ids = array();
foreach ( $slugs as $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( $term ) {
		$ids[] = (int) $term->term_id;
		echo "OK: $slug ({$term->term_id})\n";
	} else {
		echo "MISSING: $slug\n";
	}
}

$walker = function ( &$items ) use ( &$walker, $slugs, $ids ) {
	foreach ( $items as &$el ) {
		$id = $el['id'] ?? '';
		$w  = $el['widgetType'] ?? '';

		if ( 'bf23057' === $id && 'greenpath_core_product_category_list' === $w ) {
			$el['settings']['behavior']             = 'columns';
			$el['settings']['additional_params']    = 'slug';
			$el['settings']['taxonomy_slugs']       = implode( ',', $slugs );
			$el['settings']['taxonomy_ids']         = implode( ', ', $ids );
			$el['settings']['posts_per_page']       = (string) count( $slugs );
			$el['settings']['orderby']              = 'include';
			$el['settings']['layout']               = 'info-below';
			$el['settings']['use_alternate_image']  = 'yes';
			$el['settings']['space']                = 'tiny';
			$el['settings']['vertical_space']       = 'tiny';
			$el['settings']['columns']              = '5';
			$el['settings']['columns_responsive']   = 'custom';
			$el['settings']['columns_1512']         = '5';
			$el['settings']['columns_1368']         = '5';
			$el['settings']['columns_1200']         = '5';
			$el['settings']['columns_1024']         = '5';
			$el['settings']['columns_880']          = '5';
			$el['settings']['columns_680']          = '5';
			unset(
				$el['settings']['slider_navigation'],
				$el['settings']['slider_pagination'],
				$el['settings']['slider_speed'],
				$el['settings']['slider_loop'],
				$el['settings']['slider_autoplay']
			);
			echo "Updated bf23057: static 5-column row, no carousel.\n";
		}

		if ( ! empty( $el['elements'] ) ) {
			$walker( $el['elements'] );
		}
	}
};

$walker( $data );

update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
delete_post_meta( $page_id, '_elementor_css' );

if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

echo "Done. Hard refresh homepage (Ctrl+F5).\n";