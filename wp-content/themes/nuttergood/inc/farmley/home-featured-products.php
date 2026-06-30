<?php
/**
 * Homepage Featured Products — GreenPath-style 4×2 grid (8 products) with category filters.
 */

if ( ! function_exists( 'nuttergood_farmley_is_home_featured_products_widget' ) ) {
	/**
	 * @param \Elementor\Widget_Base $widget Elementor widget instance.
	 */
	function nuttergood_farmley_is_home_featured_products_widget( $widget ) {
		if ( ! is_front_page() || ! is_object( $widget ) || ! method_exists( $widget, 'get_name' ) ) {
			return false;
		}

		if ( 'greenpath_core_product_list' !== $widget->get_name() ) {
			return false;
		}

		return method_exists( $widget, 'get_id' ) && '3a8d545' === $widget->get_id();
	}
}

if ( ! function_exists( 'nuttergood_farmley_home_featured_filter_slugs' ) ) {
	/**
	 * Category tabs shown above the featured grid (same set as homepage category row).
	 *
	 * @return string[]
	 */
	function nuttergood_farmley_home_featured_filter_slugs() {
		if ( function_exists( 'nuttergood_farmley_home_category_slugs' ) ) {
			return nuttergood_farmley_home_category_slugs();
		}

		return array(
			'dry-fruits',
			'chips',
			'mixes',
			'brittles',
			'mouth-fresheners',
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_home_featured_filter_ids' ) ) {
	/**
	 * @return int[]
	 */
	function nuttergood_farmley_home_featured_filter_ids() {
		$ids = array();

		foreach ( nuttergood_farmley_home_featured_filter_slugs() as $slug ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( $term && ! is_wp_error( $term ) ) {
				$ids[] = (int) $term->term_id;
			}
		}

		return $ids;
	}
}

if ( ! function_exists( 'nuttergood_farmley_home_featured_widget_settings' ) ) {
	/**
	 * GreenPath demo: 4 columns, 8 products (2 rows), simple category filter tabs.
	 *
	 * @return array<string, string>
	 */
	function nuttergood_farmley_home_featured_widget_settings() {
		$slugs = nuttergood_farmley_home_featured_filter_slugs();
		$ids   = nuttergood_farmley_home_featured_filter_ids();

		return array(
			'behavior'               => 'columns',
			'columns'                => '4',
			'columns_responsive'     => 'custom',
			'columns_1512'           => '4',
			'columns_1368'           => '4',
			'columns_1200'           => '3',
			'columns_1024'           => '2',
			'columns_880'            => '2',
			'columns_680'            => '2',
			'posts_per_page'         => '8',
			'orderby'                => 'date',
			'order'                  => 'ASC',
			'layout'                 => 'info-below',
			'title_tag'              => 'h6',
			'text_transform'         => 'capitalize',
			'space'                  => 'small',
			'vertical_space'         => 'normal',
			'pagination_type'        => 'no-pagination',
			'enable_custom_filter'   => 'yes',
			'filter_type'            => 'simple',
			'additional_params'      => 'tax',
			'tax'                    => 'product_cat',
			'tax_slug'               => implode( ',', $slugs ),
			'tax__in'                => implode( ', ', $ids ),
			'enable_wishlist'        => 'yes',
			'enable_quickview'       => 'yes',
			'enable_compare_product' => 'yes',
			'item_enable_border'     => 'yes',
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_is_home_featured_product_list_request' ) ) {
	/**
	 * @param array<string, mixed> $params Shortcode params.
	 */
	function nuttergood_farmley_is_home_featured_product_list_request( $params ) {
		if ( ! is_front_page() || ! is_array( $params ) ) {
			return false;
		}

		return isset( $params['shortcode'], $params['behavior'], $params['columns'], $params['enable_custom_filter'] )
			&& 'product-list' === $params['shortcode']
			&& 'columns' === $params['behavior']
			&& '4' === (string) $params['columns']
			&& 'yes' === $params['enable_custom_filter']
			&& isset( $params['filter_type'] )
			&& 'simple' === $params['filter_type'];
	}
}

if ( ! function_exists( 'nuttergood_farmley_filter_home_featured_products_widget' ) ) {
	/**
	 * @param \Elementor\Widget_Base $widget Elementor widget instance.
	 */
	function nuttergood_farmley_filter_home_featured_products_widget( $widget ) {
		if ( ! nuttergood_farmley_is_home_featured_products_widget( $widget ) ) {
			return;
		}

		$widget->set_settings(
			array_merge(
				$widget->get_settings_for_display(),
				nuttergood_farmley_home_featured_widget_settings()
			)
		);
	}
	add_action( 'elementor/frontend/widget/before_render', 'nuttergood_farmley_filter_home_featured_products_widget', 10, 1 );
}

if ( ! function_exists( 'nuttergood_farmley_home_featured_query_params' ) ) {
	/**
	 * Keep AJAX category filter reloads capped at 8 products like GreenPath.
	 *
	 * @param array<string, mixed> $args   WP_Query args.
	 * @param array<string, mixed> $params Shortcode params.
	 * @return array<string, mixed>
	 */
	function nuttergood_farmley_home_featured_query_params( $args, $params ) {
		if ( ! nuttergood_farmley_is_home_featured_product_list_request( $params ) ) {
			return $args;
		}

		$args['posts_per_page'] = 8;

		return $args;
	}
	add_filter( 'greenpath_filter_query_params', 'nuttergood_farmley_home_featured_query_params', 15, 2 );
}