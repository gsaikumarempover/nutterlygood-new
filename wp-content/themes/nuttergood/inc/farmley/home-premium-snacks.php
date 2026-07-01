<?php
/**
 * Homepage Premium Quality Snacks — horizontal product list (widget ccb84d0).
 */

if ( ! function_exists( 'nuttergood_farmley_is_home_premium_snacks_widget' ) ) {
	/**
	 * @param \Elementor\Widget_Base $widget Elementor widget instance.
	 */
	function nuttergood_farmley_is_home_premium_snacks_widget( $widget ) {
		if ( ! is_front_page() || ! is_object( $widget ) || ! method_exists( $widget, 'get_name' ) ) {
			return false;
		}

		if ( 'greenpath_core_product_list' !== $widget->get_name() ) {
			return false;
		}

		return method_exists( $widget, 'get_id' ) && 'ccb84d0' === $widget->get_id();
	}
}

if ( ! function_exists( 'nuttergood_farmley_home_premium_snacks_category_slugs' ) ) {
	/**
	 * @return string[]
	 */
	function nuttergood_farmley_home_premium_snacks_category_slugs() {
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

if ( ! function_exists( 'nuttergood_farmley_home_premium_snacks_category_ids' ) ) {
	/**
	 * @return int[]
	 */
	function nuttergood_farmley_home_premium_snacks_category_ids() {
		$ids = array();

		foreach ( nuttergood_farmley_home_premium_snacks_category_slugs() as $slug ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( $term && ! is_wp_error( $term ) ) {
				$ids[] = (int) $term->term_id;
			}
		}

		return $ids;
	}
}

if ( ! function_exists( 'nuttergood_farmley_home_premium_snacks_widget_settings' ) ) {
	/**
	 * @return array<string, string>
	 */
	function nuttergood_farmley_home_premium_snacks_widget_settings() {
		$slugs = nuttergood_farmley_home_premium_snacks_category_slugs();
		$ids   = nuttergood_farmley_home_premium_snacks_category_ids();

		return array(
			'behavior'               => 'columns',
			'columns'                => '1',
			'columns_responsive'     => 'predefined',
			'posts_per_page'         => '9',
			'orderby'                => 'date',
			'order'                  => 'ASC',
			'layout'                 => 'horizontal',
			'title_tag'              => 'h6',
			'text_transform'         => 'capitalize',
			'space'                  => 'normal',
			'vertical_space'         => 'normal',
			'pagination_type'        => 'no-pagination',
			'enable_custom_filter'   => 'no',
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

if ( ! function_exists( 'nuttergood_farmley_filter_home_premium_snacks_widget' ) ) {
	/**
	 * @param \Elementor\Widget_Base $widget Elementor widget instance.
	 */
	function nuttergood_farmley_filter_home_premium_snacks_widget( $widget ) {
		if ( ! nuttergood_farmley_is_home_premium_snacks_widget( $widget ) ) {
			return;
		}

		$widget->set_settings(
			array_merge(
				$widget->get_settings_for_display(),
				nuttergood_farmley_home_premium_snacks_widget_settings()
			)
		);
	}
	add_action( 'elementor/frontend/widget/before_render', 'nuttergood_farmley_filter_home_premium_snacks_widget', 10, 1 );
}