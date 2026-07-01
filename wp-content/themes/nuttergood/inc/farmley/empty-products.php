<?php
/**
 * Branded empty state for product listings (shop, archives, filters, search).
 */

if ( ! function_exists( 'nuttergood_farmley_is_product_list_empty_context' ) ) {
	/**
	 * Whether posts-not-found is being rendered for a WooCommerce product list.
	 */
	function nuttergood_farmley_is_product_list_empty_context() {
		if ( function_exists( 'nuttergood_farmley_is_catalog_listing_page' ) && nuttergood_farmley_is_catalog_listing_page() ) {
			return true;
		}

		if ( function_exists( 'nuttergood_farmley_is_product_search_results' ) && nuttergood_farmley_is_product_search_results() ) {
			return true;
		}

		$trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 15 ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace
		foreach ( $trace as $frame ) {
			if ( empty( $frame['file'] ) ) {
				continue;
			}

			$file = wp_normalize_path( $frame['file'] );
			if ( false !== strpos( $file, '/shortcodes/product-list/' ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_empty_products_context' ) ) {
	/**
	 * @return string search|category|shop|general
	 */
	function nuttergood_farmley_get_empty_products_context() {
		if ( function_exists( 'nuttergood_farmley_is_product_search_results' ) && nuttergood_farmley_is_product_search_results() ) {
			return 'search';
		}

		if ( function_exists( 'is_product_category' ) && is_product_category() ) {
			return 'category';
		}

		if ( function_exists( 'is_product_tag' ) && is_product_tag() ) {
			return 'category';
		}

		if ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
			return 'category';
		}

		if ( function_exists( 'nuttergood_farmley_is_main_shop_page' ) && nuttergood_farmley_is_main_shop_page() ) {
			return 'shop';
		}

		return 'general';
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_empty_products_copy' ) ) {
	/**
	 * @return array{title: string, text: string, primary: string, show_clear: bool}
	 */
	function nuttergood_farmley_get_empty_products_copy() {
		$context = nuttergood_farmley_get_empty_products_context();

		switch ( $context ) {
			case 'search':
				return array(
					'title'       => __( 'No products match your search', 'nuttergood' ),
					'text'        => __( 'Try different keywords or browse our full collection of premium snacks and dry fruits.', 'nuttergood' ),
					'primary'     => __( 'Browse all products', 'nuttergood' ),
					'show_clear'  => false,
				);
			case 'category':
				return array(
					'title'       => __( 'No products in this category', 'nuttergood' ),
					'text'        => __( 'We could not find items here right now. Explore our shop for more wholesome picks.', 'nuttergood' ),
					'primary'     => __( 'Continue shopping', 'nuttergood' ),
					'show_clear'  => false,
				);
			case 'shop':
				return array(
					'title'       => __( 'No products match your filters', 'nuttergood' ),
					'text'        => __( 'Try adjusting your filters or explore our full range of premium snacks and dry fruits.', 'nuttergood' ),
					'primary'     => __( 'Continue shopping', 'nuttergood' ),
					'show_clear'  => true,
				);
			default:
				return array(
					'title'       => __( 'No products found', 'nuttergood' ),
					'text'        => __( 'We could not find any products for this view. Browse our shop to discover wholesome snacks and dry fruits.', 'nuttergood' ),
					'primary'     => __( 'Continue shopping', 'nuttergood' ),
					'show_clear'  => false,
				);
		}
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_empty_products_state' ) ) {
	/**
	 * @param array<string, mixed> $args Optional overrides.
	 */
	function nuttergood_farmley_render_empty_products_state( $args = array() ) {
		$copy     = nuttergood_farmley_get_empty_products_copy();
		$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		$shop_url = $shop_url ? $shop_url : home_url( '/' );

		$title      = isset( $args['title'] ) ? (string) $args['title'] : $copy['title'];
		$text       = isset( $args['text'] ) ? (string) $args['text'] : $copy['text'];
		$primary    = isset( $args['primary'] ) ? (string) $args['primary'] : $copy['primary'];
		$shop_url   = isset( $args['shop_url'] ) ? (string) $args['shop_url'] : $shop_url;
		$show_clear = isset( $args['show_clear'] ) ? (bool) $args['show_clear'] : $copy['show_clear'];
		$grid_item  = ! isset( $args['grid_item'] ) || $args['grid_item'];

		$classes = array( 'ng-farmley-empty-products' );
		if ( $grid_item ) {
			$classes[] = 'qodef-grid-item';
		}

		$tag = $grid_item ? 'li' : 'div';
		?>
		<<?php echo tag_escape( $tag ); ?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" role="status" aria-live="polite">
			<div class="ng-farmley-empty-products__card">
				<div class="ng-farmley-empty-products__icon" aria-hidden="true">
					<svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
						<circle cx="28" cy="28" r="27" stroke="currentColor" stroke-width="1.5" opacity="0.22"/>
						<path d="M20 22h16l-1.5 14H21.5L20 22Z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
						<path d="M23 22V19a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
						<circle cx="24.5" cy="40.5" r="1.75" fill="currentColor"/>
						<circle cx="33.5" cy="40.5" r="1.75" fill="currentColor"/>
					</svg>
				</div>
				<h2 class="ng-farmley-empty-products__title"><?php echo esc_html( $title ); ?></h2>
				<p class="ng-farmley-empty-products__text"><?php echo esc_html( $text ); ?></p>
				<div class="ng-farmley-empty-products__actions">
					<a class="ng-farmley-empty-products__btn ng-farmley-empty-products__btn--primary" href="<?php echo esc_url( $shop_url ); ?>">
						<?php echo esc_html( $primary ); ?>
					</a>
					<button type="button" class="ng-farmley-empty-products__btn ng-farmley-empty-products__btn--ghost" data-ng-shop-go-back data-fallback="<?php echo esc_url( $shop_url ); ?>">
						<?php esc_html_e( 'Go back', 'nuttergood' ); ?>
					</button>
				</div>
				<?php if ( $show_clear ) : ?>
					<p class="ng-farmley-empty-products__hint">
						<a class="ng-farmley-empty-products__link" href="<?php echo esc_url( $shop_url ); ?>">
							<?php esc_html_e( 'Clear all filters', 'nuttergood' ); ?>
						</a>
					</p>
				<?php endif; ?>
			</div>
		</<?php echo tag_escape( $tag ); ?>>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_empty_products_assets' ) ) {
	function nuttergood_farmley_empty_products_assets() {
		if ( is_admin() ) {
			return;
		}

		$should_load = is_front_page();
		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$should_load = true;
		}
		if ( function_exists( 'nuttergood_farmley_is_product_search_results' ) && nuttergood_farmley_is_product_search_results() ) {
			$should_load = true;
		}

		if ( ! $should_load ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-empty-products.css';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-empty-products',
				$uri . '/assets/css/farmley-empty-products.css',
				array( 'greenpath-style' ),
				filemtime( $css )
			);
		}

		$js = $dir . '/assets/js/farmley-empty-products.js';
		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'nuttergood-farmley-empty-products',
				$uri . '/assets/js/farmley-empty-products.js',
				array(),
				filemtime( $js ),
				true
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_empty_products_assets', 41 );
}
