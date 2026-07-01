<?php
/**
 * Farmley-style right sidebar quick view drawer.
 */

$ng_farmley_meta = get_template_directory() . '/inc/farmley/product-meta.php';
if ( file_exists( $ng_farmley_meta ) ) {
	include_once $ng_farmley_meta;
}

if ( ! function_exists( 'nuttergood_farmley_quick_view_drawer_classes' ) ) {
	function nuttergood_farmley_quick_view_drawer_classes( $classes ) {
		if ( is_array( $classes ) ) {
			$classes[] = 'ng-farmley-qv-drawer';
			$classes[] = 'qqvfw-type--sidebar';
		}
		return $classes;
	}
	add_filter( 'qode_quick_view_for_woocommerce_filter_set_quick_view_pop_up_classes', 'nuttergood_farmley_quick_view_drawer_classes' );
}

if ( ! function_exists( 'nuttergood_farmley_quick_view_strip_defaults' ) ) {
	function nuttergood_farmley_quick_view_strip_defaults() {
		remove_all_actions( 'qode_quick_view_for_woocommerce_action_product_image' );
		remove_all_actions( 'qode_quick_view_for_woocommerce_action_product_summary' );
		remove_all_actions( 'qode_quick_view_for_woocommerce_action_before_product_summary' );
		remove_all_actions( 'qode_quick_view_for_woocommerce_action_after_product_summary' );

		add_action( 'qode_quick_view_for_woocommerce_action_product_image', 'nuttergood_farmley_qv_render_panel', 5 );
	}
	add_action( 'qode_quick_view_for_woocommerce_action_after_quick_view_templates_load', 'nuttergood_farmley_quick_view_strip_defaults', 1 );
}

if ( ! function_exists( 'nuttergood_farmley_render_quantity_stepper' ) ) {
	/**
	 * Farmley-style horizontal quantity stepper (replaces theme vertical chevron control).
	 *
	 * @param WC_Product $product Product object.
	 */
	function nuttergood_farmley_render_quantity_stepper( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$min_value   = 1;
		$max_value   = 0 < $product->get_max_purchase_quantity() ? $product->get_max_purchase_quantity() : '';
		$input_id    = 'ng-fv-qty-' . $product->get_id() . '-' . wp_unique_id();
		$input_value = 1;

		echo '<div class="ng-farmley-qv__qty-control quantity">';
		printf(
			'<label class="screen-reader-text" for="%1$s">%2$s</label>',
			esc_attr( $input_id ),
			esc_html( sprintf( __( '%s quantity', 'nuttergood' ), $product->get_name() ) )
		);
		echo '<button type="button" class="ng-farmley-qv__qty-btn ng-farmley-qv__qty-btn--minus" aria-label="' . esc_attr__( 'Decrease quantity', 'nuttergood' ) . '">&minus;</button>';
		printf(
			'<input type="number" id="%1$s" class="ng-farmley-qv__qty-input input-text qty text" name="quantity" value="%2$d" min="%3$d" max="%4$s" step="1" inputmode="numeric" autocomplete="off" aria-label="%5$s" />',
			esc_attr( $input_id ),
			(int) $input_value,
			(int) $min_value,
			esc_attr( $max_value ),
			esc_attr__( 'Product quantity', 'nuttergood' )
		);
		echo '<button type="button" class="ng-farmley-qv__qty-btn ng-farmley-qv__qty-btn--plus" aria-label="' . esc_attr__( 'Increase quantity', 'nuttergood' ) . '">+</button>';
		echo '</div>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_qv_render_buy_now_button' ) ) {
	function nuttergood_farmley_qv_render_buy_now_button() {
		global $product;

		if ( ! $product instanceof WC_Product || 'external' === $product->get_type() ) {
			return;
		}

		printf(
			'<button type="button" class="ng-farmley-qv__buy-now ng-farmley-buy-now">%s</button>',
			esc_html__( 'Buy Now', 'nuttergood' )
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_qv_render_description' ) ) {
	/**
	 * @param WC_Product $product Product object.
	 */
	function nuttergood_farmley_qv_render_description( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$description = $product->get_description();
		if ( $description && function_exists( 'nuttergood_farmley_clean_product_html' ) ) {
			$description = nuttergood_farmley_clean_product_html( $description );
		}
		if ( ! $description ) {
			return;
		}

		echo '<div class="ng-farmley-qv__description">' . wp_kses_post( wpautop( $description ) ) . '</div>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_qv_render_panel' ) ) {
	function nuttergood_farmley_qv_render_panel() {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$meta        = nuttergood_farmley_get_product_meta( $product );
		$gallery     = nuttergood_farmley_get_gallery_ids( $product );
		$is_variable = $product->is_type( 'variable' );
		$sizes       = function_exists( 'nuttergood_farmley_get_product_size_options' )
			? nuttergood_farmley_get_product_size_options( $product )
			: ( ! empty( $meta['sizes'] ) ? $meta['sizes'] : array() );

		$mrp      = $meta['mrp'];
		$offer    = $meta['offer_price'] ?: $product->get_price();

		$regular_display = $product->get_regular_price() ?: $mrp;
		$sale_display    = $product->get_sale_price() ?: $offer;
		$sku_display     = $product->get_sku();
		if ( '' === $sku_display && ! $product->is_type( 'variable' ) ) {
			$sku_display = (string) $product->get_id();
		}

		echo '<div class="ng-farmley-qv" data-product-id="' . esc_attr( $product->get_id() ) . '">';

		echo '<div class="ng-farmley-qv__head">';
		echo '<span class="ng-farmley-qv__head-label">' . esc_html__( 'Quick view', 'nuttergood' ) . '</span>';
		echo '</div>';

		// Gallery.
		echo '<div class="ng-farmley-qv__gallery">';
		echo '<div class="ng-farmley-qv__stage">';
		foreach ( $gallery as $i => $img_id ) {
			$src = wp_get_attachment_image_url( $img_id, 'woocommerce_single' );
			if ( ! $src ) {
				$src = wp_get_attachment_image_url( $img_id, 'large' );
			}
			if ( ! $src ) {
				continue;
			}
			printf(
				'<img class="ng-farmley-qv__stage-img%1$s" src="%2$s" alt="%3$s" data-index="%4$d" loading="%5$s" decoding="async" />',
				0 === $i ? ' is-active' : '',
				esc_url( $src ),
				esc_attr( $product->get_name() ),
				(int) $i,
				0 === $i ? 'eager' : 'lazy'
			);
		}
		echo '</div>';
		if ( count( $gallery ) > 1 ) {
			echo '<div class="ng-farmley-qv__thumbs" role="tablist">';
			foreach ( $gallery as $i => $img_id ) {
				$src = wp_get_attachment_image_url( $img_id, 'thumbnail' );
				if ( ! $src ) {
					continue;
				}
				printf(
					'<button type="button" class="ng-farmley-qv__thumb%1$s" data-index="%2$d" aria-label="%3$s"><img src="%4$s" alt="" /></button>',
					0 === $i ? ' is-active' : '',
					(int) $i,
					esc_attr( sprintf( 'View image %d', $i + 1 ) ),
					esc_url( $src )
				);
			}
			echo '</div>';
		}
		echo '</div>';

		// Info column.
		echo '<div class="ng-farmley-qv__info">';

		echo '<div class="ng-farmley-qv__intro">';
		echo '<div class="ng-farmley-qv__title-row">';
		echo '<h2 class="ng-farmley-qv__title">' . esc_html( $product->get_name() ) . '</h2>';
		echo '<div class="ng-farmley-qv__price-row">';
		echo '<span class="ng-farmley-qv__price-values">';
		echo '<ins class="ng-farmley-qv__price-sale">' . wp_kses_post( nuttergood_farmley_format_money( $sale_display ) ) . '</ins>';
		if ( $regular_display && (float) $regular_display > (float) $sale_display ) {
			echo '<del class="ng-farmley-qv__price-regular">' . wp_kses_post( nuttergood_farmley_format_money( $regular_display ) ) . '</del>';
		}
		echo '</span></div>';
		echo '</div>';

		if ( wc_product_sku_enabled() && '' !== $sku_display ) {
			echo '<div class="ng-farmley-qv__sku">';
			echo '<span class="ng-farmley-qv__sku-label">' . esc_html__( 'SKU', 'nuttergood' ) . '</span>';
			echo '<span class="ng-farmley-qv__sku-value">' . esc_html( $sku_display ) . '</span>';
			echo '</div>';
		}
		echo '</div>';

		if ( $is_variable ) {
			echo '<div class="ng-farmley-qv__size">';
			echo '<span class="ng-farmley-qv__size-label">' . esc_html__( 'Size :', 'nuttergood' ) . '</span>';
			add_action( 'woocommerce_after_add_to_cart_button', 'nuttergood_farmley_qv_render_buy_now_button', 10 );
			woocommerce_template_single_add_to_cart();
			remove_action( 'woocommerce_after_add_to_cart_button', 'nuttergood_farmley_qv_render_buy_now_button', 10 );
			echo '</div>';
			nuttergood_farmley_qv_render_description( $product );
		} elseif ( ! empty( $sizes ) ) {
			echo '<div class="ng-farmley-qv__size">';
			echo '<span class="ng-farmley-qv__size-label">' . esc_html__( 'Size :', 'nuttergood' ) . '</span>';
			echo '<div class="ng-farmley-qv__size-options" role="listbox">';
			foreach ( $sizes as $idx => $size ) {
				$img_src = ! empty( $size['image_id'] ) ? wp_get_attachment_image_url( (int) $size['image_id'], 'thumbnail' ) : '';
				printf(
					'<button type="button" class="ng-farmley-qv__size-btn%1$s" role="option" data-index="%2$d" data-price="%3$s" data-regular="%4$s" data-mrp="%5$s" data-image="%6$s" aria-selected="%7$s">',
					0 === $idx ? ' is-active' : '',
					(int) $idx,
					esc_attr( $size['price'] ?? '' ),
					esc_attr( $size['regular_price'] ?? '' ),
					esc_attr( $size['mrp'] ?? '' ),
					esc_url( $img_src ? $img_src : '' ),
					0 === $idx ? 'true' : 'false'
				);
				if ( $img_src ) {
					echo '<img src="' . esc_url( $img_src ) . '" alt="" class="ng-farmley-qv__size-img" />';
				}
				echo '<span class="ng-farmley-qv__size-text">' . esc_html( $size['label'] ?? '' ) . '</span></button>';
			}
			echo '</div></div>';

			echo '<form class="ng-farmley-qv__cart cart" method="post" enctype="multipart/form-data">';
			echo '<div class="ng-farmley-qv__checkout">';
			echo '<div class="ng-farmley-qv__purchase">';
			echo '<div class="ng-farmley-qv__qty-row">';
			echo '<span class="ng-farmley-qv__qty-label">' . esc_html__( 'Qty', 'nuttergood' ) . '</span>';
			nuttergood_farmley_render_quantity_stepper( $product );
			echo '</div>';
			echo '<div class="ng-farmley-qv__actions">';
			printf(
				'<button type="submit" name="add-to-cart" value="%1$d" class="single_add_to_cart_button button alt ng-farmley-qv__atc">%2$s</button>',
				(int) $product->get_id(),
				esc_html__( 'Add to cart', 'nuttergood' )
			);
			nuttergood_farmley_qv_render_buy_now_button();
			echo '</div></div></div></form>';
			nuttergood_farmley_qv_render_description( $product );
		} else {
			add_action( 'woocommerce_after_add_to_cart_button', 'nuttergood_farmley_qv_render_buy_now_button', 10 );
			woocommerce_template_single_add_to_cart();
			remove_action( 'woocommerce_after_add_to_cart_button', 'nuttergood_farmley_qv_render_buy_now_button', 10 );
			nuttergood_farmley_qv_render_description( $product );
		}

		echo '</div></div>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_quick_view_enqueue_assets' ) ) {
	function nuttergood_farmley_quick_view_enqueue_assets() {
		if ( is_admin() || ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-quick-view.css';
		$js  = $dir . '/assets/js/farmley-quick-view.js';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-quick-view',
				$uri . '/assets/css/farmley-quick-view.css',
				array( 'qode-quick-view-for-woocommerce-main', 'greenpath-style' ),
				filemtime( $css )
			);
		}

		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'nuttergood-farmley-quick-view',
				$uri . '/assets/js/farmley-quick-view.js',
				array( 'jquery', 'qode-quick-view-for-woocommerce-main', 'wc-add-to-cart-variation' ),
				filemtime( $js ),
				true
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_quick_view_enqueue_assets', 35 );
}