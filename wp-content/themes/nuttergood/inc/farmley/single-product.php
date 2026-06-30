<?php
/**
 * Farmley single product — remove reviews, style wishlist, hide compare on related.
 */

if ( ! function_exists( 'nuttergood_farmley_is_single_product_page' ) ) {
	function nuttergood_farmley_is_single_product_page() {
		return function_exists( 'is_product' ) && is_product();
	}
}

if ( ! function_exists( 'nuttergood_farmley_disable_product_reviews' ) ) {
	function nuttergood_farmley_disable_product_reviews() {
		if ( ! nuttergood_farmley_is_single_product_page() ) {
			return;
		}

		remove_action( 'woocommerce_single_product_summary', 'greenpath_core_woo_product_get_single_rating_html', 8 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		remove_action( 'woocommerce_after_single_product_summary', 'greenpath_core_woo_single_product_big_images_reviews_position', 18 );

		add_filter( 'woocommerce_product_tabs', 'nuttergood_farmley_remove_reviews_tab', 98 );
		add_filter( 'woocommerce_reviews_title', '__return_empty_string', 99 );
		add_filter( 'woocommerce_product_get_rating_html', 'nuttergood_farmley_empty_rating_html', 99, 2 );
		add_filter( 'comments_open', 'nuttergood_farmley_close_product_comments', 10, 2 );
	}
	add_action( 'wp', 'nuttergood_farmley_disable_product_reviews', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_remove_reviews_tab' ) ) {
	function nuttergood_farmley_remove_reviews_tab( $tabs ) {
		unset( $tabs['reviews'] );

		return $tabs;
	}
}

if ( ! function_exists( 'nuttergood_farmley_empty_rating_html' ) ) {
	function nuttergood_farmley_empty_rating_html( $html, $rating ) {
		if ( nuttergood_farmley_is_single_product_page() ) {
			return '';
		}

		return $html;
	}
}

if ( ! function_exists( 'nuttergood_farmley_close_product_comments' ) ) {
	function nuttergood_farmley_close_product_comments( $open, $post_id ) {
		if ( 'product' === get_post_type( $post_id ) ) {
			return false;
		}

		return $open;
	}
}

// Product HTML cleanup lives in inc/farmley/product-content.php.

if ( ! function_exists( 'nuttergood_farmley_single_wishlist_button_type' ) ) {
	function nuttergood_farmley_single_wishlist_button_type( $type, $atts ) {
		if ( ! empty( $atts['is_single'] ) ) {
			return 'icon';
		}

		return $type;
	}
	add_filter( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_shortcode_button_type', 'nuttergood_farmley_single_wishlist_button_type', 20, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_remove_single_wishlist_from_cart' ) ) {
	function nuttergood_farmley_remove_single_wishlist_from_cart() {
		if ( ! nuttergood_farmley_is_single_product_page() ) {
			return;
		}

		if ( ! class_exists( 'Qode_Wishlist_For_WooCommerce_Wishlist_Module' ) ) {
			return;
		}

		$module = Qode_Wishlist_For_WooCommerce_Wishlist_Module::get_instance();
		$hooks  = array(
			array( 'woocommerce_single_product_summary', 35 ),
			array( 'woocommerce_single_product_summary', 25 ),
			array( 'woocommerce_single_product_summary', 3 ),
			array( 'woocommerce_after_add_to_cart_button', 10 ),
			array( 'woocommerce_product_thumbnails', 30 ),
			array( 'woocommerce_after_single_product_summary', 11 ),
			array( 'woocommerce_after_single_product_summary', 5 ),
		);

		foreach ( $hooks as $hook ) {
			remove_action( $hook[0], array( $module, 'add_button' ), $hook[1] );
		}
	}
	add_action( 'wp', 'nuttergood_farmley_remove_single_wishlist_from_cart', 100 );
}

if ( ! function_exists( 'nuttergood_farmley_render_gallery_wishlist' ) ) {
	function nuttergood_farmley_render_gallery_wishlist( $product_id ) {
		if ( ! class_exists( 'Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Shortcode' ) ) {
			return;
		}

		echo '<div class="ng-farmley-sp-gallery__wishlist" aria-label="' . esc_attr__( 'Add to wishlist', 'nuttergood' ) . '">';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Shortcode::call_shortcode(
			array(
				'item_id'         => $product_id,
				'button_type'     => 'icon',
				'button_behavior' => 'add',
				'single_layout'   => 'yes',
			)
		);
		echo '</div>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_replace_product_gallery' ) ) {
	function nuttergood_farmley_replace_product_gallery() {
		if ( ! nuttergood_farmley_is_single_product_page() ) {
			return;
		}

		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
		add_action( 'woocommerce_before_single_product_summary', 'nuttergood_farmley_render_single_product_gallery', 20 );
	}
	add_action( 'wp', 'nuttergood_farmley_replace_product_gallery', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_remove_buy_now_button' ) ) {
	function nuttergood_farmley_remove_buy_now_button() {
		remove_action( 'woocommerce_after_add_to_cart_button', 'greenpath_woo_get_single_product_buy_now_button', 10 );
	}

	add_action( 'wp', 'nuttergood_farmley_remove_buy_now_button', 25 );
}

if ( ! function_exists( 'nuttergood_farmley_render_buy_now_button' ) ) {
	function nuttergood_farmley_render_buy_now_button() {
		global $product;

		if ( ! $product instanceof WC_Product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
			return;
		}

		printf(
			'<button type="submit" name="ng-farmley-buy-now" value="%1$d" class="button ng-farmley-sp-buy-now"><span class="qodef-m-text">%2$s</span></button>',
			(int) $product->get_id(),
			esc_html__( 'Buy Now', 'nuttergood' )
		);
	}

	add_action( 'woocommerce_after_add_to_cart_button', 'nuttergood_farmley_render_buy_now_button', 11 );
}

if ( ! function_exists( 'nuttergood_farmley_handle_buy_now' ) ) {
	function nuttergood_farmley_handle_buy_now() {
		if ( empty( $_POST['ng-farmley-buy-now'] ) || ! function_exists( 'WC' ) ) {
			return;
		}

		$product_id   = absint( wp_unslash( $_POST['ng-farmley-buy-now'] ) );
		$quantity     = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 1;
		$variation_id = isset( $_POST['variation_id'] ) ? absint( wp_unslash( $_POST['variation_id'] ) ) : 0;
		$variation    = array();

		foreach ( $_POST as $key => $value ) {
			if ( 0 === strpos( $key, 'attribute_' ) ) {
				$variation[ sanitize_key( $key ) ] = wc_clean( wp_unslash( $value ) );
			}
		}

		if ( $product_id && WC()->cart ) {
			WC()->cart->add_to_cart( $product_id, max( 1, $quantity ), $variation_id, $variation );
			wp_safe_redirect( wc_get_checkout_url() );
			exit;
		}
	}

	add_action( 'template_redirect', 'nuttergood_farmley_handle_buy_now', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_render_single_product_gallery' ) ) {
	function nuttergood_farmley_render_single_product_gallery() {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$gallery = nuttergood_farmley_get_gallery_ids( $product );
		if ( empty( $gallery ) ) {
			return;
		}

		$count = count( $gallery );
		$name  = $product->get_name();

		echo '<div class="ng-farmley-sp-gallery" data-count="' . esc_attr( (string) $count ) . '">';

		echo '<div class="ng-farmley-sp-gallery__stage-wrap">';
		nuttergood_farmley_render_gallery_wishlist( $product->get_id() );
		if ( $count > 1 ) {
			echo '<button type="button" class="ng-farmley-sp-gallery__nav ng-farmley-sp-gallery__nav--prev" aria-label="' . esc_attr__( 'Previous image', 'nuttergood' ) . '"><span aria-hidden="true">&lsaquo;</span></button>';
		}

		echo '<div class="ng-farmley-sp-gallery__stage">';
		foreach ( $gallery as $i => $img_id ) {
			$src = wp_get_attachment_image_url( $img_id, 'woocommerce_single' );
			if ( ! $src ) {
				continue;
			}

			printf(
				'<img class="ng-farmley-sp-gallery__stage-img%1$s" src="%2$s" alt="%3$s" data-index="%4$d" loading="%5$s" />',
				0 === $i ? ' is-active' : '',
				esc_url( $src ),
				esc_attr( $name ),
				(int) $i,
				0 === $i ? 'eager' : 'lazy'
			);
		}
		echo '</div>';

		if ( $count > 1 ) {
			echo '<button type="button" class="ng-farmley-sp-gallery__nav ng-farmley-sp-gallery__nav--next" aria-label="' . esc_attr__( 'Next image', 'nuttergood' ) . '"><span aria-hidden="true">&rsaquo;</span></button>';
		}
		echo '</div>';

		if ( $count > 1 ) {
			echo '<div class="ng-farmley-sp-gallery__thumbs" role="tablist">';
			foreach ( $gallery as $i => $img_id ) {
				$thumb = wp_get_attachment_image_url( $img_id, 'woocommerce_gallery_thumbnail' );
				if ( ! $thumb ) {
					$thumb = wp_get_attachment_image_url( $img_id, 'thumbnail' );
				}
				if ( ! $thumb ) {
					continue;
				}

				printf(
					'<button type="button" class="ng-farmley-sp-gallery__thumb%1$s" data-index="%2$d" role="tab" aria-selected="%5$s" aria-label="%3$s"><img src="%4$s" alt="" /></button>',
					0 === $i ? ' is-active' : '',
					(int) $i,
					esc_attr( sprintf( __( 'View image %d', 'nuttergood' ), $i + 1 ) ),
					esc_url( $thumb ),
					0 === $i ? 'true' : 'false'
				);
			}
			echo '</div>';
		}

		echo '</div>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_dequeue_wc_gallery_scripts' ) ) {
	function nuttergood_farmley_dequeue_wc_gallery_scripts() {
		if ( ! nuttergood_farmley_is_single_product_page() ) {
			return;
		}

		wp_dequeue_script( 'flexslider' );
		wp_deregister_script( 'flexslider' );
		wp_dequeue_script( 'zoom' );
		wp_deregister_script( 'zoom' );
		wp_dequeue_script( 'photoswipe' );
		wp_deregister_script( 'photoswipe' );
		wp_dequeue_script( 'photoswipe-ui-default' );
		wp_deregister_script( 'photoswipe-ui-default' );
		wp_dequeue_style( 'photoswipe' );
		wp_dequeue_style( 'photoswipe-default-skin' );

		// Keep wc-single-product for product tabs; gallery scripts stay dequeued (custom gallery).
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_dequeue_wc_gallery_scripts', 999 );
}

if ( ! function_exists( 'nuttergood_farmley_single_product_assets' ) ) {
	function nuttergood_farmley_single_product_assets() {
		if ( ! nuttergood_farmley_is_single_product_page() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-single-product.css';
		$js  = $dir . '/assets/js/farmley-single-product.js';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-single-product',
				$uri . '/assets/css/farmley-single-product.css',
				array( 'nuttergood-farmley-wishlist', 'greenpath-style', 'nuttergood-qode-product-list' ),
				filemtime( $css )
			);
		}

		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'nuttergood-farmley-single-product',
				$uri . '/assets/js/farmley-single-product.js',
				array( 'jquery', 'nuttergood-farmley-wishlist' ),
				filemtime( $js ),
				true
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_single_product_assets', 36 );
}

if ( ! function_exists( 'nuttergood_farmley_single_product_inline_css' ) ) {
	function nuttergood_farmley_single_product_inline_css( $style ) {
		if ( ! nuttergood_farmley_is_single_product_page() ) {
			return $style;
		}

		$style .= '
#qodef-woo-page.qodef--single .woocommerce-product-rating,
#qodef-woo-page.qodef--single .qodef-woo-single-product-rating,
#qodef-woo-page.qodef--single .reviews_tab,
#qodef-woo-page.qodef--single .woocommerce-Tabs-panel--reviews,
#qodef-woo-page.qodef--single .woocommerce-Reviews,
#qodef-woo-page.qodef--single #review_form_wrapper,
#qodef-woo-page.qodef--single #o_product_page_reviews,
#qodef-woo-page.qodef--single .o_shop_discussion_rating,
#qodef-woo-page.qodef--single .o_product_page_reviews_link,
#qodef-woo-page.qodef--single .o_website_rating_static { display: none !important; }
';

		return $style;
	}
	add_filter( 'greenpath_filter_add_inline_style', 'nuttergood_farmley_single_product_inline_css' );
}