<?php
/**
 * GreenPath-style category clipart grid + Featured Products filter fix.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

function ng_cat_log( $msg ) {
	echo $msg . PHP_EOL;
}

function ng_assign_category_clipart() {
	$icons_file = __DIR__ . '/nutterly-category-icons.json';
	if ( ! file_exists( $icons_file ) ) {
		ng_cat_log( 'Missing nutterly-category-icons.json — run generate-category-icons.py first.' );
		return;
	}

	$icons = json_decode( file_get_contents( $icons_file ), true );
	if ( ! is_array( $icons ) ) {
		ng_cat_log( 'Invalid nutterly-category-icons.json.' );
		return;
	}

	foreach ( $icons as $slug => $info ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term ) {
			ng_cat_log( "Skip missing term: $slug" );
			continue;
		}

		if ( empty( $info['svg'] ) ) {
			ng_cat_log( "No SVG for: $slug" );
			continue;
		}

		update_term_meta( $term->term_id, 'qodef_product_category_alternate_svg', $info['svg'] );
		if ( ! empty( $info['bg'] ) ) {
			update_term_meta( $term->term_id, 'qodef_product_category_svg_bg', $info['bg'] );
		}
		ng_cat_log( "Icon set: $slug" );
	}
}

function ng_fix_homepage_widgets() {
	$page_id  = (int) get_option( 'page_on_front' );
	$raw      = get_post_meta( $page_id, '_elementor_data', true );
	$elements = json_decode( $raw, true );
	if ( ! is_array( $elements ) ) {
		ng_cat_log( 'Invalid Elementor data.' );
		return;
	}

	$filter_slugs = array( 'dry-fruits', 'chips', 'mixes', 'brittles', 'mouth-fresheners' );
	$filter_ids   = array();
	foreach ( $filter_slugs as $slug ) {
		$t = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $t ) {
			$filter_ids[] = (int) $t->term_id;
		}
	}

	$cat_slugs = array(
		'dry-fruits',
		'almonds',
		'cashews',
		'khishmish',
		'cranberry',
		'walnuts',
		'chips',
		'mixes',
		'brittles',
		'mouth-fresheners',
	);
	$cat_ids   = array();
	foreach ( $cat_slugs as $slug ) {
		$t = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $t ) {
			$cat_ids[] = (int) $t->term_id;
		}
	}

	$walker = function ( &$items ) use ( &$walker, $filter_slugs, $filter_ids, $cat_slugs, $cat_ids ) {
		foreach ( $items as &$el ) {
			$id = $el['id'] ?? '';
			$w  = $el['widgetType'] ?? '';

			if ( 'bf23057' === $id && 'greenpath_core_product_category_list' === $w ) {
				$el['settings']['behavior']             = 'slider';
				$el['settings']['use_alternate_image']  = 'yes';
				$el['settings']['layout']               = 'info-below';
				$el['settings']['space']                = 'tiny';
				$el['settings']['vertical_space']       = 'tiny';
				$el['settings']['columns']              = '10';
				$el['settings']['columns_responsive']   = 'custom';
				$el['settings']['columns_1512']         = '9';
				$el['settings']['columns_1368']         = '9';
				$el['settings']['columns_1200']         = '8';
				$el['settings']['columns_1024']         = '8';
				$el['settings']['columns_880']          = '6';
				$el['settings']['columns_680']          = '3';
				$el['settings']['posts_per_page']       = '10';
				$el['settings']['orderby']              = 'menu_order';
				$el['settings']['additional_params']    = 'slug';
				$el['settings']['taxonomy_slugs']       = implode( ',', $cat_slugs );
				$el['settings']['taxonomy_ids']         = implode( ', ', $cat_ids );
				$el['settings']['slider_navigation']    = 'no';
				$el['settings']['slider_pagination']    = 'no';
				$el['settings']['slider_speed']         = '3500';
				$el['settings']['slider_loop']          = 'yes';
				$el['settings']['slider_autoplay']      = 'yes';
				unset( $el['settings']['custom_image_width'], $el['settings']['custom_image_height'] );
				ng_cat_log( 'Category widget: slider carousel, alternate SVG, 10 cols.' );
			}

			if ( '3a8d545' === $id && 'greenpath_core_product_list' === $w ) {
				$el['settings']['additional_params']    = 'tax';
				$el['settings']['tax']                  = 'product_cat';
				$el['settings']['tax_slug']             = implode( ',', $filter_slugs );
				$el['settings']['tax__in']              = implode( ', ', $filter_ids );
				$el['settings']['enable_custom_filter'] = 'yes';
				$el['settings']['filter_type']          = 'simple';
				$el['settings']['behavior']             = 'columns';
				$el['settings']['posts_per_page']       = '12';
				$el['settings']['columns']              = '4';
				$el['settings']['layout']               = 'info-below';
				$el['settings']['space']                = 'small';
				ng_cat_log( 'Featured widget: filter tabs = ' . implode( ', ', $filter_slugs ) );
			}

			if ( ! empty( $el['elements'] ) ) {
				$walker( $el['elements'] );
			}
		}
	};
	$walker( $elements );

	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
	delete_post_meta( $page_id, '_elementor_css' );

	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	ng_cat_log( 'Homepage Elementor data updated.' );
}

ng_cat_log( '=== Fix GreenPath Categories ===' );
ng_assign_category_clipart();
ng_fix_homepage_widgets();
wp_cache_flush();
ng_cat_log( '=== Done. Hard refresh http://localhost/nutterlyGood/ ===' );