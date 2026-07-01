<?php
/**
 * Shop sidebar categories — same five parents + labels as the homepage row.
 */

if ( ! function_exists( 'nuttergood_farmley_get_shop_category_terms' ) ) {
	/**
	 * Homepage-aligned parent categories for the shop filter.
	 *
	 * @return WP_Term[]
	 */
	function nuttergood_farmley_get_shop_category_terms() {
		if ( ! function_exists( 'nuttergood_farmley_home_category_slugs' ) ) {
			return array();
		}

		$icons = function_exists( 'nuttergood_farmley_home_category_icons_data' )
			? nuttergood_farmley_home_category_icons_data()
			: array();
		$terms = array();

		foreach ( nuttergood_farmley_home_category_slugs() as $slug ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( ! $term || is_wp_error( $term ) ) {
				continue;
			}

			if ( ! empty( $icons[ $slug ]['label'] ) ) {
				$term->name = $icons[ $slug ]['label'];
			}

			$terms[] = $term;
		}

		return $terms;
	}
}

if ( ! function_exists( 'nuttergood_farmley_is_shop_category_terms_query' ) ) {
	/**
	 * @param array         $args       get_terms() args.
	 * @param array|string  $taxonomies Taxonomy name(s).
	 */
	function nuttergood_farmley_is_shop_category_terms_query( $args, $taxonomies ) {
		if ( ! nuttergood_farmley_is_main_shop_page() ) {
			return false;
		}

		$taxonomy = is_array( $taxonomies ) ? reset( $taxonomies ) : $taxonomies;
		if ( 'product_cat' !== $taxonomy ) {
			return false;
		}

		if ( ! empty( $args['child_of'] ) || ! empty( $args['slug'] ) ) {
			return false;
		}

		// Product list shortcode category checkboxes (no parent/include restriction).
		if ( ! empty( $args['hide_empty'] ) && empty( $args['include'] ) && empty( $args['exclude'] ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'nuttergood_farmley_filter_shop_category_terms' ) ) {
	/**
	 * @param array         $terms      Terms array.
	 * @param array|string  $taxonomies Taxonomy name(s).
	 * @param array         $args       Query args.
	 * @param WP_Term_Query $term_query Term query object.
	 * @return array
	 */
	function nuttergood_farmley_filter_shop_category_terms( $terms, $taxonomies, $args, $term_query ) {
		if ( is_admin() || ! nuttergood_farmley_is_shop_category_terms_query( $args, $taxonomies ) ) {
			return $terms;
		}

		$aligned = nuttergood_farmley_get_shop_category_terms();

		return ! empty( $aligned ) ? $aligned : $terms;
	}
	add_filter( 'get_terms', 'nuttergood_farmley_filter_shop_category_terms', 25, 4 );
}

if ( ! function_exists( 'nuttergood_farmley_filter_shop_category_filter_items' ) ) {
	/**
	 * @param array|string $items  Filter terms.
	 * @param array        $params Shortcode params.
	 * @return array|string
	 */
	function nuttergood_farmley_filter_shop_category_filter_items( $items, $params ) {
		if ( ! nuttergood_farmley_is_main_shop_page() || ! is_array( $items ) ) {
			return $items;
		}

		$aligned = nuttergood_farmley_get_shop_category_terms();

		return ! empty( $aligned ) ? $aligned : $items;
	}
	add_filter( 'greenpath_filter_get_filter_items', 'nuttergood_farmley_filter_shop_category_filter_items', 25, 2 );
}