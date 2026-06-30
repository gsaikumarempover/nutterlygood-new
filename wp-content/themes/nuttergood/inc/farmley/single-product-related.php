<?php
/**
 * Related products — same Farmley card layout as homepage product list.
 */

if ( ! function_exists( 'nuttergood_farmley_related_product_list_atts' ) ) {
	/**
	 * @param array<int> $related_ids Product IDs.
	 *
	 * @return array<string, mixed>
	 */
	function nuttergood_farmley_related_product_list_atts( $related_ids ) {
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
			'posts_per_page'         => (string) count( $related_ids ),
			'orderby'                => 'post__in',
			'order'                  => 'ASC',
			'space'                  => 'normal',
			'vertical_space'         => 'medium',
			'layout'                 => 'info-below',
			'title_tag'              => 'h6',
			'text_transform'         => 'capitalize',
			'enable_wishlist'        => is_user_logged_in() ? 'yes' : 'no',
			'enable_quickview'       => 'yes',
			'enable_compare_product' => 'no',
			'enable_custom_filter'   => 'no',
			'pagination_type'        => 'no-pagination',
			'additional_params'      => 'id',
			'post_ids'               => implode( ',', $related_ids ),
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_related_products' ) ) {
	function nuttergood_farmley_render_related_products() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}

		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$args = apply_filters(
			'woocommerce_output_related_products_args',
			array(
				'posts_per_page' => 4,
				'columns'        => 4,
				'orderby'        => 'rand',
			)
		);

		$related_ids = wc_get_related_products( $product->get_id(), (int) $args['posts_per_page'] );
		$related_ids = array_values( array_filter( array_map( 'intval', $related_ids ) ) );

		if ( empty( $related_ids ) ) {
			return;
		}

		if ( ! class_exists( 'GreenPathCore_Product_List_Shortcode' ) ) {
			woocommerce_output_related_products();
			return;
		}

		if ( function_exists( 'nuttergood_farmley_prepare_loop_product_icons' ) ) {
			nuttergood_farmley_prepare_loop_product_icons();
		}

		$atts = nuttergood_farmley_related_product_list_atts( $related_ids );

		if ( class_exists( 'Nuttergood_Qode_Product_List_Icons' ) && method_exists( 'Nuttergood_Qode_Product_List_Icons', 'set_list_settings' ) ) {
			Nuttergood_Qode_Product_List_Icons::set_list_settings( $atts );
		}

		$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

		echo '<section class="related products ng-farmley-related-products">';
		if ( $heading ) {
			echo '<h2>' . esc_html( $heading ) . '</h2>';
		}
		echo '<div class="ng-farmley-product-cards ng-farmley-related-cards">';
		echo GreenPathCore_Product_List_Shortcode::call_shortcode( $atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div></section>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_setup_related_products' ) ) {
	function nuttergood_farmley_setup_related_products() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}

		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		add_action( 'woocommerce_after_single_product_summary', 'nuttergood_farmley_render_related_products', 20 );
	}

	add_action( 'wp', 'nuttergood_farmley_setup_related_products', 30 );
}
