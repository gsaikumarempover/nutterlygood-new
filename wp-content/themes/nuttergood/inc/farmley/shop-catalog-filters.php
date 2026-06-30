<?php
/**
 * Unified catalog sort + discount filter for shop and product archives.
 */

if ( ! function_exists( 'nuttergood_farmley_is_catalog_listing_page' ) ) {
	function nuttergood_farmley_is_catalog_listing_page() {
		if ( nuttergood_farmley_is_main_shop_page() ) {
			return true;
		}

		return nuttergood_farmley_is_woo_archive_loop();
	}
}

if ( ! function_exists( 'nuttergood_farmley_catalog_sort_options' ) ) {
	/**
	 * @return array<string, string>
	 */
	function nuttergood_farmley_catalog_sort_options() {
		return array(
			'popularity' => __( 'Sort by popularity', 'nuttergood' ),
			'price'      => __( 'Sort by price: low to high', 'nuttergood' ),
			'price-desc' => __( 'Sort by price: high to low', 'nuttergood' ),
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_filter_catalog_sort_options' ) ) {
	function nuttergood_farmley_filter_catalog_sort_options( $options ) {
		if ( ! nuttergood_farmley_is_catalog_listing_page() ) {
			return $options;
		}

		return nuttergood_farmley_catalog_sort_options();
	}
	add_filter( 'woocommerce_catalog_orderby', 'nuttergood_farmley_filter_catalog_sort_options', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_filter_product_list_sort_options' ) ) {
	function nuttergood_farmley_filter_product_list_sort_options( $options ) {
		if ( ! nuttergood_farmley_is_main_shop_page() ) {
			return $options;
		}

		return array(
			'popularity'       => __( 'Sort by popularity', 'nuttergood' ),
			'price-range-low'  => __( 'Sort by price: low to high', 'nuttergood' ),
			'price-range-high' => __( 'Sort by price: high to low', 'nuttergood' ),
		);
	}
	add_filter( 'woocommerce_catalog_orderby', 'nuttergood_farmley_filter_product_list_sort_options', 25 );
}

if ( ! function_exists( 'nuttergood_farmley_default_catalog_orderby' ) ) {
	function nuttergood_farmley_default_catalog_orderby( $orderby ) {
		if ( ! nuttergood_farmley_is_catalog_listing_page() ) {
			return $orderby;
		}

		return 'popularity';
	}
	add_filter( 'woocommerce_default_catalog_orderby', 'nuttergood_farmley_default_catalog_orderby', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_get_discounted_product_ids' ) ) {
	/**
	 * Product IDs with a real Farmley MRP discount (not only WooCommerce sale flag).
	 *
	 * @return array<int, int>
	 */
	function nuttergood_farmley_get_discounted_product_ids() {
		static $ids = null;

		if ( null !== $ids ) {
			return $ids;
		}

		$ids = array();

		if ( ! function_exists( 'nuttergood_farmley_get_badge_discount_percent' ) ) {
			return $ids;
		}

		$query = new WP_Query(
			array(
				'post_type'              => 'product',
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		foreach ( $query->posts as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( ! $product instanceof WC_Product ) {
				continue;
			}

			if ( nuttergood_farmley_get_badge_discount_percent( $product ) > 0 ) {
				$ids[] = (int) $product_id;
			}
		}

		return $ids;
	}
}

if ( ! function_exists( 'nuttergood_farmley_is_discount_filter_active' ) ) {
	/**
	 * @param array<string, mixed>|null $params Shortcode / AJAX params.
	 */
	function nuttergood_farmley_is_discount_filter_active( $params = null ) {
		if ( is_array( $params ) && isset( $params['ng_discount'] ) ) {
			return '1' === (string) $params['ng_discount'];
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return isset( $_GET['ng_discount'] ) && '1' === (string) wp_unslash( $_GET['ng_discount'] );
	}
}

if ( ! function_exists( 'nuttergood_farmley_filter_catalog_products_by_discount' ) ) {
	function nuttergood_farmley_filter_catalog_products_by_discount( $query ) {
		if ( is_admin() || ! $query->is_main_query() || ! nuttergood_farmley_is_woo_archive_loop() ) {
			return;
		}

		if ( ! nuttergood_farmley_is_discount_filter_active() ) {
			return;
		}

		$discounted = nuttergood_farmley_get_discounted_product_ids();
		$query->set( 'post__in', ! empty( $discounted ) ? $discounted : array( 0 ) );
	}
	add_action( 'pre_get_posts', 'nuttergood_farmley_filter_catalog_products_by_discount', 25 );
}

if ( ! function_exists( 'nuttergood_farmley_filter_product_list_by_discount' ) ) {
	function nuttergood_farmley_filter_product_list_by_discount( $args, $params ) {
		if ( ! nuttergood_farmley_is_shop_product_list_request( $params ) || ! nuttergood_farmley_is_discount_filter_active() ) {
			return $args;
		}

		$discounted = nuttergood_farmley_get_discounted_product_ids();
		$args['post__in'] = ! empty( $discounted ) ? $discounted : array( 0 );

		return $args;
	}
	add_filter( 'greenpath_filter_query_params', 'nuttergood_farmley_filter_product_list_by_discount', 25, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_catalog_filter_query_args' ) ) {
	/**
	 * Preserve current sort + discount when toggling filters.
	 *
	 * @return array<string, string>
	 */
	function nuttergood_farmley_catalog_filter_query_args() {
		$args = array();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['orderby'] ) ) {
			$orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
			if ( array_key_exists( $orderby, nuttergood_farmley_catalog_sort_options() ) ) {
				$args['orderby'] = $orderby;
			}
		}

		if ( nuttergood_farmley_is_discount_filter_active() ) {
			$args['ng_discount'] = '1';
		}

		return $args;
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_catalog_toolbar' ) ) {
	function nuttergood_farmley_render_catalog_toolbar() {
		if ( ! nuttergood_farmley_is_woo_archive_loop() ) {
			return;
		}

		$sort_options = nuttergood_farmley_catalog_sort_options();
		$base_args    = nuttergood_farmley_catalog_filter_query_args();
		$current      = isset( $base_args['orderby'] ) ? $base_args['orderby'] : 'popularity';
		$discount_on  = nuttergood_farmley_is_discount_filter_active();
		$discount_url = add_query_arg(
			array_merge(
				$base_args,
				array(
					'ng_discount' => $discount_on ? null : '1',
					'paged'       => null,
				)
			)
		);
		?>
		<div class="ng-farmley-catalog-toolbar">
			<form class="ng-farmley-catalog-toolbar__sort" method="get">
				<label class="screen-reader-text" for="ng-farmley-catalog-orderby"><?php esc_html_e( 'Sort products', 'nuttergood' ); ?></label>
				<select id="ng-farmley-catalog-orderby" name="orderby" class="ng-farmley-catalog-toolbar__select">
					<?php foreach ( $sort_options as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php if ( $discount_on ) : ?>
					<input type="hidden" name="ng_discount" value="1" />
				<?php endif; ?>
				<?php wc_query_string_form_fields( null, array( 'orderby', 'submit', 'ng_discount', 'paged', 'product-page' ) ); ?>
			</form>
			<a class="ng-farmley-catalog-toolbar__discount<?php echo $discount_on ? ' is-active' : ''; ?>" href="<?php echo esc_url( $discount_url ); ?>">
				<?php esc_html_e( 'Discount', 'nuttergood' ); ?>
			</a>
		</div>
		<?php
	}
	add_action( 'woocommerce_before_shop_loop', 'nuttergood_farmley_render_catalog_toolbar', 25 );
}

if ( ! function_exists( 'nuttergood_farmley_remove_native_catalog_ordering' ) ) {
	function nuttergood_farmley_remove_native_catalog_ordering() {
		if ( ! nuttergood_farmley_is_woo_archive_loop() ) {
			return;
		}

		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	}
	add_action( 'woocommerce_before_shop_loop', 'nuttergood_farmley_remove_native_catalog_ordering', 6 );
}
