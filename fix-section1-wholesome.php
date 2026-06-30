<?php
/**
 * Section 1 only: Fresh, Tasty, and Wholesome — GreenPath carousel + matched category SVGs.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

function ng_s1_log( $msg ) {
	echo $msg . PHP_EOL;
}

function ng_s1_assign_category_icons() {
	$file = __DIR__ . '/nutterly-category-icons.json';
	if ( ! file_exists( $file ) ) {
		ng_s1_log( 'Missing nutterly-category-icons.json — run generate-category-icons.py first.' );
		return;
	}

	$icons = json_decode( file_get_contents( $file ), true );
	foreach ( $icons as $slug => $info ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term ) {
			ng_s1_log( "Skip missing term: $slug" );
			continue;
		}
		if ( empty( $info['svg'] ) ) {
			ng_s1_log( "No SVG for: $slug" );
			continue;
		}
		update_term_meta( $term->term_id, 'qodef_product_category_alternate_svg', $info['svg'] );
		if ( ! empty( $info['bg'] ) ) {
			update_term_meta( $term->term_id, 'qodef_product_category_svg_bg', $info['bg'] );
		}
		ng_s1_log( "Icon set: $slug" );
	}
}

function ng_s1_fix_widgets() {
	$page_id  = (int) get_option( 'page_on_front' );
	$raw      = get_post_meta( $page_id, '_elementor_data', true );
	$elements = json_decode( $raw, true );
	if ( ! is_array( $elements ) ) {
		ng_s1_log( 'Invalid Elementor data.' );
		return;
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

	$walker = function ( &$items ) use ( &$walker, $cat_slugs, $cat_ids ) {
		foreach ( $items as &$el ) {
			$id = $el['id'] ?? '';
			$w  = $el['widgetType'] ?? '';

			// Section title.
			if ( 'abca240' === $id && 'greenpath_core_section_title' === $w ) {
				$el['settings']['title']             = 'Fresh, Tasty, and Wholesome';
				$el['settings']['content_alignment'] = 'center';
				$el['settings']['title_tag']         = 'h3';
				ng_s1_log( 'Title: Fresh, Tasty, and Wholesome' );
			}

			// Product slider (dry-fruits carousel).
			if ( 'a463981' === $id && 'greenpath_core_product_list' === $w ) {
				$el['settings']['behavior']           = 'slider';
				$el['settings']['additional_params']  = 'tax';
				$el['settings']['tax']                = 'product_cat';
				$el['settings']['tax_slug']           = 'dry-fruits';
				$el['settings']['posts_per_page']     = '8';
				$el['settings']['columns']            = '4';
				$el['settings']['layout']             = 'info-below';
				$el['settings']['space']              = 'small';
				$el['settings']['enable_wishlist']        = 'yes';
				$el['settings']['enable_quickview']       = 'yes';
				$el['settings']['enable_compare_product'] = 'yes';
				$el['settings']['slider_navigation']  = 'combo';
				$el['settings']['slider_pagination']  = 'no';
				$el['settings']['columns_responsive'] = 'custom';
				$el['settings']['columns_1512']       = '4';
				$el['settings']['columns_1368']       = '4';
				$el['settings']['columns_1024']       = '2';
				$el['settings']['columns_880']        = '2';
				$el['settings']['columns_680']        = '2';
				$el['settings']['_padding']           = array(
					'unit'     => 'px',
					'top'      => '54',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'isLinked' => false,
				);
				ng_s1_log( 'Product slider: dry-fruits, 8 posts, slider.' );
			}

			// Category carousel — GreenPath bf23057 export settings.
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
				ng_s1_log( 'Category widget: slider carousel, 10 cols, tiny gutter.' );
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
	ng_s1_log( 'Homepage Elementor cache cleared.' );
}

ng_s1_log( '=== Fix Section 1: Fresh, Tasty, and Wholesome ===' );
ng_s1_assign_category_icons();
ng_s1_fix_widgets();
wp_cache_flush();
ng_s1_log( '=== Done. Hard refresh http://localhost/nutterlyGood/ ===' );