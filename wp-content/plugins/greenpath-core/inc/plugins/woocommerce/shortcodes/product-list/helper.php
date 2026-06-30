<?php

if ( ! function_exists( 'greenpath_core_get_product_list_query_order_by_array' ) ) {
	function greenpath_core_get_product_list_query_order_by_array() {
		$include_order_by = array(
			'price-range-high' => esc_html__( 'Price high to low', 'greenpath-core' ),
			'price-range-low'  => esc_html__( 'Price low to high', 'greenpath-core' ),
		);

		return greenpath_core_get_select_type_options_pool( 'order_by', false, array(), $include_order_by );
	}
}


if ( ! function_exists( 'greenpath_core_get_product_list_sorting_filter' ) ) {
	function greenpath_core_get_product_list_sorting_filter() {
		$sorting_list_html = '';

		$include_order_by = apply_filters(
			'woocommerce_catalog_orderby',
			array(
				'menu-order'       => esc_html__( 'Default sorting', 'greenpath-core' ),
				'popularity'       => esc_html__( 'Sort by popularity', 'greenpath-core' ),
				'newness'          => esc_html__( 'Sort by latest', 'greenpath-core' ),
				'price-range-high' => esc_html__( 'Sort by price high to low', 'greenpath-core' ),
				'price-range-low'  => esc_html__( 'Sort by price low to high', 'greenpath-core' ),
			)
		);

		foreach ( $include_order_by as $key => $value ) {
			$sorting_list_html .= '<a href="#" class="qodef-e-order-link" data-value="' . $key . '"><span class="qodef-e-label">' . $value . '</span></a>';
		}

		return $sorting_list_html;
	}
}

if ( ! function_exists( 'greenpath_core_product_list_filter_query' ) ) {
	/**
	 * Function to adjust query for listing list parameters
	 */
	function greenpath_core_product_list_filter_query( $args, $params ) {

		switch ( $params['orderby'] ) {
			case 'menu_order':
				$args['order']   = 'asc';
				$args['orderby'] = 'menu_order title';
				break;
			case 'popularity':
				$args['meta_key'] = 'total_sales';
				$args['order']    = 'desc';
				$args['orderby']  = 'meta_value_num';
				break;
			case 'newness':
				$args['order']   = 'desc';
				$args['orderby'] = 'date';
				break;
			case 'price-range-high':
				$args['meta_key'] = '_price';
				$args['order']    = 'DESC';
				$args['orderby']  = 'meta_value_num';
				break;
			case 'price-range-low':
				$args['meta_key'] = '_price';
				$args['order']    = 'ASC';
				$args['orderby']  = 'meta_value_num';
				break;
		}

		return $args;
	}

	add_filter( 'greenpath_filter_query_params', 'greenpath_core_product_list_filter_query', 10, 2 );
}

if ( ! function_exists( 'greenpath_add_product_list_widget_area' ) ) {
	function greenpath_add_product_list_widget_area() {
		register_sidebar(
			array(
				'id'            => 'qodef-product-list-sidebar-widget-area',
				'name'          => esc_html__( 'Product List Sidebar Widget Area', 'greenpath-core' ),
				'description'   => esc_html__( 'Widgets added here will appear in product list advanced filter with sidebar type', 'greenpath-core' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s qodef-product-list-sidebar-widget-area" data-area="product-list-widget">',
				'after_widget'  => '</div>',
			)
		);
	}

	add_action( 'widgets_init', 'greenpath_add_product_list_widget_area' );
}

if ( ! function_exists( 'greenpath_add_product_list_side_area_widget_area' ) ) {
	function greenpath_add_product_list_side_area_widget_area() {
		register_sidebar(
			array(
				'id'            => 'qodef-product-list-side-area-widget-area',
				'name'          => esc_html__( 'Product List Side Area Widget Area', 'greenpath-core' ),
				'description'   => esc_html__( 'Widgets added here will appear in product list advanced filter with side area type', 'greenpath-core' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s qodef-product-list-side-area-widget-area" data-area="product-list-widget">',
				'after_widget'  => '</div>',
			)
		);
	}

	add_action( 'widgets_init', 'greenpath_add_product_list_side_area_widget_area' );
}

