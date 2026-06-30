<?php
/**
 * Popular Products sidebar widget — weight badge + Buy Now.
 */

if ( ! function_exists( 'nuttergood_farmley_is_popular_products_list' ) ) {
	/**
	 * @param array<string, mixed> $params Shortcode params.
	 */
	function nuttergood_farmley_is_popular_products_list( $params = array() ) {
		return is_array( $params )
			&& isset( $params['layout'] )
			&& 'info-right' === $params['layout'];
	}
}

if ( ! function_exists( 'nuttergood_farmley_product_list_info_right_layout_path' ) ) {
	/**
	 * Theme override for sidebar popular products (info-right layout).
	 *
	 * @param string               $path   Default variation path.
	 * @param array<string, mixed> $params Shortcode params.
	 */
	function nuttergood_farmley_product_list_info_right_layout_path( $path, $params ) {
		if ( ! nuttergood_farmley_is_popular_products_list( $params ) ) {
			return $path;
		}

		if ( false === strpos( (string) $path, '/product-list/' ) ) {
			return $path;
		}

		$theme_path = get_template_directory() . '/inc/farmley/product-list/variations/info-right';
		return is_dir( $theme_path ) ? $theme_path : $path;
	}
	add_filter( 'qode_framework_list_sc_layout_path', 'nuttergood_farmley_product_list_info_right_layout_path', 12, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_render_popular_product_weight' ) ) {
	/**
	 * Compact default weight pill for sidebar cards.
	 *
	 * @param WC_Product|null $product Product object.
	 */
	function nuttergood_farmley_render_popular_product_weight( $product = null ) {
		if ( ! $product instanceof WC_Product ) {
			$product = wc_get_product( get_the_ID() );
		}

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$label = function_exists( 'nuttergood_farmley_get_product_weight_label' )
			? nuttergood_farmley_get_product_weight_label( $product )
			: '';

		if ( '' === $label ) {
			return;
		}

		printf(
			'<span class="ng-farmley-popular-weight" aria-label="%1$s">%2$s</span>',
			esc_attr( sprintf( __( 'Net weight %s', 'nuttergood' ), $label ) ),
			esc_html( $label )
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_popular_product_actions_open' ) ) {
	function nuttergood_farmley_popular_product_actions_open() {
		$GLOBALS['ng_farmley_popular_card_actions'] = true;
	}
}

if ( ! function_exists( 'nuttergood_farmley_popular_product_actions_close' ) ) {
	function nuttergood_farmley_popular_product_actions_close() {
		unset( $GLOBALS['ng_farmley_popular_card_actions'] );
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_popular_product_buy_now' ) ) {
	/**
	 * @param WC_Product|null $product Product object.
	 */
	function nuttergood_farmley_render_popular_product_buy_now( $product = null ) {
		if ( ! $product instanceof WC_Product ) {
			$product = wc_get_product( get_the_ID() );
		}

		if ( ! $product instanceof WC_Product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
			return;
		}

		if ( $product->is_type( 'simple' ) ) {
			printf(
				'<a href="%1$s" class="ng-farmley-popular-buy ng-farmley-buy-now add_to_cart_button ajax_add_to_cart product_type_simple" data-product_id="%2$d" data-product_sku="%3$s" data-quantity="1" rel="nofollow" aria-label="%4$s"><span>%5$s</span></a>',
				esc_url( $product->add_to_cart_url() ),
				(int) $product->get_id(),
				esc_attr( $product->get_sku() ),
				esc_attr( sprintf( __( 'Buy %s now', 'nuttergood' ), $product->get_name() ) ),
				esc_html__( 'Buy Now', 'nuttergood' )
			);
			return;
		}

		printf(
			'<a href="%1$s" class="ng-farmley-popular-buy ng-farmley-popular-buy--link" aria-label="%2$s"><span>%3$s</span></a>',
			esc_url( $product->get_permalink() ),
			esc_attr( sprintf( __( 'View %s', 'nuttergood' ), $product->get_name() ) ),
			esc_html__( 'Buy Now', 'nuttergood' )
		);
	}
}
