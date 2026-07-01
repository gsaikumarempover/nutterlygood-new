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

if ( ! function_exists( 'nuttergood_farmley_popular_cart_icon_svg' ) ) {
	/**
	 * Cart + plus icon for popular card Add to Cart button.
	 */
	function nuttergood_farmley_popular_cart_icon_svg() {
		return '<svg class="ng-farmley-popular-btn-svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 5h2l1.6 9.2a1.5 1.5 0 0 0 1.48 1.3h7.4a1.5 1.5 0 0 0 1.48-1.2L18 8H7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="19" r="1.4" fill="currentColor"/><circle cx="16" cy="19" r="1.4" fill="currentColor"/><circle cx="18.5" cy="5.5" r="3.2" fill="currentColor"/><path d="M18.5 4.2v2.6M17.2 5.5h2.6" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/></svg>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_popular_bag_icon_svg' ) ) {
	/**
	 * Shopping bag icon for popular card Buy Now button.
	 */
	function nuttergood_farmley_popular_bag_icon_svg() {
		return '<svg class="ng-farmley-popular-btn-svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M7 9V7.2A5 5 0 0 1 17 7.2V9" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M6 9h12l-1.1 10.2a1.2 1.2 0 0 1-1.2 1.1H8.3a1.2 1.2 0 0 1-1.2-1.1L6 9z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_popular_leaf_icon_svg' ) ) {
	/**
	 * Small leaf accent for weight pill.
	 */
	function nuttergood_farmley_popular_leaf_icon_svg() {
		return '<svg class="ng-farmley-popular-weight__leaf" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 4C14 4 8.5 8.5 6 14c3.5-1 6.5-3.5 8.5-7-4 2.5-7 6-8 10.5C9 14.5 14 9.5 20 4z" fill="currentColor"/></svg>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_popular_button_inner' ) ) {
	/**
	 * Icon-only markup for sidebar popular card actions.
	 *
	 * @param string $type  cart|bag
	 * @param string $label Unused — kept for call-site compatibility; use aria-label on the link.
	 */
	function nuttergood_farmley_popular_button_inner( $type, $label ) {
		unset( $label );

		$icon = 'cart' === $type && function_exists( 'nuttergood_farmley_popular_cart_icon_svg' )
			? nuttergood_farmley_popular_cart_icon_svg()
			: ( 'bag' === $type && function_exists( 'nuttergood_farmley_popular_bag_icon_svg' )
				? nuttergood_farmley_popular_bag_icon_svg()
				: '' );

		return sprintf(
			'<span class="ng-farmley-card-btn__inner"><span class="ng-farmley-popular-btn-icon ng-farmley-popular-btn-icon--%1$s" aria-hidden="true">%2$s</span></span>',
			esc_attr( $type ),
			$icon
		);
	}
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

		$leaf = function_exists( 'nuttergood_farmley_popular_leaf_icon_svg' )
			? nuttergood_farmley_popular_leaf_icon_svg()
			: '';

		printf(
			'<span class="ng-farmley-popular-weight" aria-label="%1$s">%2$s<span class="ng-farmley-popular-weight__text">%3$s</span></span>',
			esc_attr( sprintf( __( 'Net weight %s', 'nuttergood' ), $label ) ),
			$leaf,
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
