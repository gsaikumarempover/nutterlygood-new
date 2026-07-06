<?php
/**
 * Storefront category lists — same five parents + labels as the homepage row.
 */

if ( ! function_exists( 'nuttergood_farmley_get_shop_category_terms' ) ) {
	/**
	 * Homepage-aligned parent categories for menus, filters, and widgets.
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

if ( ! function_exists( 'nuttergood_farmley_is_storefront_category_context' ) ) {
	function nuttergood_farmley_is_storefront_category_context() {
		return ! is_admin() && ! wp_doing_ajax() && ! wp_doing_cron();
	}
}

if ( ! function_exists( 'nuttergood_farmley_should_align_product_cat_terms_query' ) ) {
	/**
	 * @param array        $args       get_terms() args.
	 * @param array|string $taxonomies Taxonomy name(s).
	 */
	function nuttergood_farmley_should_align_product_cat_terms_query( $args, $taxonomies ) {
		if ( ! nuttergood_farmley_is_storefront_category_context() ) {
			return false;
		}

		$taxonomy = is_array( $taxonomies ) ? reset( $taxonomies ) : $taxonomies;
		if ( 'product_cat' !== $taxonomy ) {
			return false;
		}

		if ( ! empty( $args['slug'] ) || ! empty( $args['name'] ) ) {
			return false;
		}

		if ( ! empty( $args['child_of'] ) ) {
			return false;
		}

		if ( ! empty( $args['object_ids'] ) ) {
			return false;
		}

		if ( isset( $args['parent'] ) && (int) $args['parent'] > 0 ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'nuttergood_farmley_filter_aligned_product_cat_terms' ) ) {
	/**
	 * @param array         $terms      Terms array.
	 * @param array|string  $taxonomies Taxonomy name(s).
	 * @param array         $args       Query args.
	 * @param WP_Term_Query $term_query Term query object.
	 * @return array
	 */
	function nuttergood_farmley_filter_aligned_product_cat_terms( $terms, $taxonomies, $args, $term_query ) {
		if ( ! nuttergood_farmley_should_align_product_cat_terms_query( $args, $taxonomies ) ) {
			return $terms;
		}

		$aligned = nuttergood_farmley_get_shop_category_terms();

		return ! empty( $aligned ) ? $aligned : $terms;
	}
	add_filter( 'get_terms', 'nuttergood_farmley_filter_aligned_product_cat_terms', 20, 4 );
}

if ( ! function_exists( 'nuttergood_farmley_is_shop_category_terms_query' ) ) {
	/**
	 * @deprecated Use nuttergood_farmley_should_align_product_cat_terms_query().
	 * @param array        $args       get_terms() args.
	 * @param array|string $taxonomies Taxonomy name(s).
	 */
	function nuttergood_farmley_is_shop_category_terms_query( $args, $taxonomies ) {
		return nuttergood_farmley_should_align_product_cat_terms_query( $args, $taxonomies );
	}
}

if ( ! function_exists( 'nuttergood_farmley_should_align_category_filter_items' ) ) {
	function nuttergood_farmley_should_align_category_filter_items() {
		if ( ! nuttergood_farmley_is_storefront_category_context() ) {
			return false;
		}

		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			return true;
		}

		return function_exists( 'is_front_page' ) && is_front_page();
	}
}

if ( ! function_exists( 'nuttergood_farmley_filter_shop_category_filter_items' ) ) {
	/**
	 * @param array|string $items  Filter terms.
	 * @param array        $params Shortcode params.
	 * @return array|string
	 */
	function nuttergood_farmley_filter_shop_category_filter_items( $items, $params ) {
		if ( ! nuttergood_farmley_should_align_category_filter_items() || ! is_array( $items ) ) {
			return $items;
		}

		if ( empty( $params['tax'] ) || 'product_cat' !== $params['tax'] ) {
			return $items;
		}

		$aligned = nuttergood_farmley_get_shop_category_terms();

		return ! empty( $aligned ) ? $aligned : $items;
	}
	add_filter( 'greenpath_filter_get_filter_items', 'nuttergood_farmley_filter_shop_category_filter_items', 25, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_build_aligned_category_menu_items' ) ) {
	/**
	 * Build nav menu items for the five aligned parent categories.
	 *
	 * @param WP_Post[]    $items Existing menu items (used for menu/order metadata).
	 * @param stdClass|array|null $args  wp_nav_menu() args.
	 *
	 * @return WP_Post[]
	 */
	function nuttergood_farmley_build_aligned_category_menu_items( $items, $args = null ) {
		$terms = nuttergood_farmley_get_shop_category_terms();
		if ( empty( $terms ) ) {
			return $items;
		}

		$template = ! empty( $items[0] ) ? $items[0] : null;
		$menu_id  = $template instanceof WP_Post ? (int) $template->menu_order : 0;
		$aligned  = array();

		foreach ( $terms as $index => $term ) {
			$link = get_term_link( $term );
			if ( is_wp_error( $link ) ) {
				continue;
			}

			$item                   = new stdClass();
			$item->ID               = 900000 + (int) $term->term_id;
			$item->db_id            = $item->ID;
			$item->title            = $term->name;
			$item->url              = $link;
			$item->menu_order       = $menu_id + $index + 1;
			$item->menu_item_parent = 0;
			$item->type             = 'taxonomy';
			$item->object           = 'product_cat';
			$item->object_id        = (int) $term->term_id;
			$item->classes          = array();
			$item->target           = '';
			$item->attr_title       = '';
			$item->xfn              = '';

			$aligned[] = $item;
		}

		return $aligned;
	}
}

if ( ! function_exists( 'nuttergood_farmley_align_extended_category_nav_menu' ) ) {
	/**
	 * Replace legacy "Categories" nav (with dry-fruit children) with five parent categories.
	 *
	 * @param WP_Post[] $items Menu items.
	 * @param stdClass  $args  wp_nav_menu() args.
	 *
	 * @return WP_Post[]
	 */
	function nuttergood_farmley_align_extended_category_nav_menu( $items, $args ) {
		if ( empty( $args->theme_location ) || 'extended-dropdown-menu' !== $args->theme_location ) {
			return $items;
		}

		$aligned = nuttergood_farmley_build_aligned_category_menu_items( $items, $args );

		return ! empty( $aligned ) ? $aligned : $items;
	}
	add_filter( 'wp_nav_menu_objects', 'nuttergood_farmley_align_extended_category_nav_menu', 25, 2 );
}