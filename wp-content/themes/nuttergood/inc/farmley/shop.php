<?php
/**
 * Farmley shop — GreenPath four-column grid with category + price filters.
 */

if ( ! function_exists( 'nuttergood_farmley_is_main_shop_page' ) ) {
	function nuttergood_farmley_is_main_shop_page() {
		return function_exists( 'is_shop' ) && is_shop() && ! is_search();
	}
}

if ( ! function_exists( 'nuttergood_farmley_is_product_search_results' ) ) {
	function nuttergood_farmley_is_product_search_results() {
		if ( ! function_exists( 'is_search' ) || ! is_search() ) {
			return false;
		}

		$post_type = get_query_var( 'post_type' );
		if ( is_array( $post_type ) ) {
			return in_array( 'product', $post_type, true );
		}

		return 'product' === $post_type || 'product' === ( $_GET['post_type'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
}

if ( ! function_exists( 'nuttergood_farmley_is_woo_archive_loop' ) ) {
	/**
	 * Category/tag archives using the native WooCommerce loop (not the custom shop shortcode).
	 */
	function nuttergood_farmley_is_woo_archive_loop() {
		if ( nuttergood_farmley_is_product_search_results() ) {
			return true;
		}

		if ( ! function_exists( 'is_woocommerce' ) || ! is_woocommerce() || is_search() ) {
			return false;
		}

		if ( nuttergood_farmley_is_main_shop_page() ) {
			return false;
		}

		return is_shop() || is_product_category() || is_product_tag() || ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() );
	}
}

if ( ! function_exists( 'nuttergood_farmley_set_loop_product_icon_settings' ) ) {
	/**
	 * Configure GreenPath icon row for native WooCommerce product loops.
	 */
	function nuttergood_farmley_set_loop_product_icon_settings() {
		if ( ! class_exists( 'Nuttergood_Qode_Product_List_Icons' ) || ! method_exists( 'Nuttergood_Qode_Product_List_Icons', 'set_list_settings' ) ) {
			return;
		}

		Nuttergood_Qode_Product_List_Icons::set_list_settings(
			array(
				'enable_wishlist'        => is_user_logged_in() ? 'yes' : 'no',
				'enable_quickview'       => 'yes',
				'enable_compare_product' => 'no',
			)
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_remove_qode_loop_product_buttons' ) ) {
	/**
	 * Remove default QODE loop buttons so only the GreenPath icon row renders.
	 */
	function nuttergood_farmley_remove_qode_loop_product_buttons() {
		$priorities = array( 5, 8, 10, 11, 12, 15, 16, 17, 20 );

		if ( class_exists( 'Qode_Wishlist_For_WooCommerce_Wishlist_Module' ) ) {
			$module = Qode_Wishlist_For_WooCommerce_Wishlist_Module::get_instance();
			foreach ( $priorities as $priority ) {
				remove_action( 'woocommerce_after_shop_loop_item', array( $module, 'add_button' ), $priority );
				remove_action( 'woocommerce_before_shop_loop_item', array( $module, 'add_button' ), $priority );
			}
		}

		if ( class_exists( 'Qode_Compare_For_WooCommerce_Compare_Module' ) ) {
			$module = Qode_Compare_For_WooCommerce_Compare_Module::get_instance();
			foreach ( $priorities as $priority ) {
				remove_action( 'woocommerce_after_shop_loop_item', array( $module, 'add_button' ), $priority );
			}
		}

		if ( class_exists( 'Qode_Quick_View_For_WooCommerce_Module' ) ) {
			$module = Qode_Quick_View_For_WooCommerce_Module::get_instance();
			foreach ( $priorities as $priority ) {
				remove_action( 'woocommerce_after_shop_loop_item', array( $module, 'add_button' ), $priority );
			}
		}
	}
}

if ( ! function_exists( 'nuttergood_farmley_prepare_loop_product_icons' ) ) {
	/**
	 * Swap QODE text loop buttons for GreenPath-style icon row.
	 */
	function nuttergood_farmley_prepare_loop_product_icons() {
		nuttergood_farmley_set_loop_product_icon_settings();
		nuttergood_farmley_remove_qode_loop_product_buttons();
	}
}

if ( ! function_exists( 'nuttergood_farmley_prepare_archive_product_icons' ) ) {
	/**
	 * Swap QODE text loop buttons for GreenPath-style icon row on category archives.
	 */
	function nuttergood_farmley_prepare_archive_product_icons() {
		if ( ! nuttergood_farmley_is_woo_archive_loop() ) {
			return;
		}

		nuttergood_farmley_prepare_loop_product_icons();
	}
	add_action( 'woocommerce_before_shop_loop', 'nuttergood_farmley_prepare_archive_product_icons', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_is_single_product_sub_loop' ) ) {
	/**
	 * Related and up-sell carousels on product pages.
	 */
	function nuttergood_farmley_is_single_product_sub_loop() {
		if ( ! function_exists( 'wc_get_loop_prop' ) ) {
			return false;
		}

		return in_array( wc_get_loop_prop( 'name' ), array( 'related', 'up-sells' ), true );
	}
}

if ( ! function_exists( 'nuttergood_farmley_prepare_single_product_loop_icons' ) ) {
	/**
	 * Related/up-sell loops on single product pages do not fire woocommerce_before_shop_loop.
	 */
	function nuttergood_farmley_prepare_single_product_loop_icons() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}

		nuttergood_farmley_prepare_loop_product_icons();
	}
	add_action( 'wp', 'nuttergood_farmley_prepare_single_product_loop_icons', 25 );
	add_action( 'woocommerce_after_single_product_summary', 'nuttergood_farmley_prepare_single_product_loop_icons', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_prepare_single_product_sub_loop_icons' ) ) {
	/**
	 * Last-chance prep immediately before related/up-sell items render.
	 */
	function nuttergood_farmley_prepare_single_product_sub_loop_icons( $html ) {
		if ( nuttergood_farmley_is_single_product_sub_loop() ) {
			nuttergood_farmley_prepare_loop_product_icons();
		}

		return $html;
	}
	add_filter( 'woocommerce_product_loop_start', 'nuttergood_farmley_prepare_single_product_sub_loop_icons', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_shop_product_list_atts' ) ) {
	function nuttergood_farmley_shop_product_list_atts() {
		$category_ids = function_exists( 'nuttergood_farmley_home_category_ids' )
			? nuttergood_farmley_home_category_ids()
			: array();

		return array(
			'behavior'               => 'columns',
			'columns'                => '3',
			'columns_responsive'     => 'custom',
			'columns_1512'           => '3',
			'columns_1368'           => '3',
			'columns_1200'           => '3',
			'columns_1024'           => '2',
			'columns_880'            => '2',
			'columns_680'            => '1',
			'posts_per_page'         => '12',
			'orderby'                => 'popularity',
			'order'                  => 'DESC',
			'space'                  => 'normal',
			'vertical_space'         => 'medium',
			'sidebar_filter_margin'  => '56',
			'layout'                 => 'info-below',
			'title_tag'              => 'h5',
			'text_transform'         => 'capitalize',
			'enable_wishlist'        => is_user_logged_in() ? 'yes' : 'no',
			'enable_quickview'       => 'yes',
			'enable_compare_product' => 'no',
			'enable_custom_filter'   => 'yes',
			'filter_type'            => 'advanced',
			'advanced_filter_type'   => 'sidebar',
			'enable_ordering_filter' => 'yes',
			'first_attribute_filter' => '',
			'second_attribute_filter'=> '',
			'pagination_type'        => 'standard',
			'tax'                    => 'product_cat',
			'tax__in'                => ! empty( $category_ids ) ? implode( ', ', $category_ids ) : '',
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_shop_product_list' ) ) {
	function nuttergood_farmley_render_shop_product_list() {
		if ( ! nuttergood_farmley_is_main_shop_page() ) {
			return;
		}

		if ( ! class_exists( 'GreenPathCore_Product_List_Shortcode' ) ) {
			return;
		}

		$atts = nuttergood_farmley_shop_product_list_atts();

		if ( nuttergood_farmley_is_discount_filter_active() ) {
			$atts['ng_discount'] = '1';
		}

		if ( class_exists( 'Nuttergood_Qode_Product_List_Icons' ) && method_exists( 'Nuttergood_Qode_Product_List_Icons', 'set_list_settings' ) ) {
			Nuttergood_Qode_Product_List_Icons::set_list_settings( $atts );
		}

		echo GreenPathCore_Product_List_Shortcode::call_shortcode( $atts );
	}
}

if ( ! function_exists( 'nuttergood_farmley_prepare_shop_page' ) ) {
	function nuttergood_farmley_prepare_shop_page() {
		if ( ! nuttergood_farmley_is_main_shop_page() ) {
			return;
		}

		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		remove_action( 'woocommerce_before_shop_loop', 'greenpath_add_results_and_ordering_holder', 15 );
		remove_action( 'woocommerce_before_shop_loop', 'greenpath_add_results_and_ordering_holder_end', 40 );
	}
	add_action( 'woocommerce_before_main_content', 'nuttergood_farmley_prepare_shop_page', 6 );
}

if ( ! function_exists( 'nuttergood_farmley_inject_shop_product_list' ) ) {
	function nuttergood_farmley_inject_shop_product_list() {
		nuttergood_farmley_render_shop_product_list();
	}
	add_action( 'woocommerce_before_main_content', 'nuttergood_farmley_inject_shop_product_list', 12 );
}

if ( ! function_exists( 'nuttergood_farmley_shop_sidebar_layout' ) ) {
	function nuttergood_farmley_shop_sidebar_layout( $layout ) {
		if ( nuttergood_farmley_is_main_shop_page() ) {
			return 'no-sidebar';
		}

		return $layout;
	}
	add_filter( 'greenpath_filter_sidebar_layout', 'nuttergood_farmley_shop_sidebar_layout', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_is_shop_product_list_request' ) ) {
	function nuttergood_farmley_is_shop_product_list_request( $params ) {
		return is_array( $params )
			&& isset( $params['shortcode'] )
			&& 'product-list' === $params['shortcode']
			&& ( nuttergood_farmley_is_main_shop_page() || ! empty( $params['enable_custom_filter'] ) );
	}
}

if ( ! function_exists( 'nuttergood_farmley_normalize_filter_query' ) ) {
	function nuttergood_farmley_normalize_filter_query( $args, $params ) {
		if ( ! nuttergood_farmley_is_shop_product_list_request( $params ) ) {
			return $args;
		}

		if ( ! empty( $args['meta_query'] ) && is_array( $args['meta_query'] ) ) {
			$clauses = array();
			foreach ( $args['meta_query'] as $clause ) {
				if ( is_array( $clause ) && isset( $clause['key'] ) ) {
					$clauses[] = $clause;
				}
			}
			if ( ! empty( $clauses ) ) {
				$args['meta_query'] = count( $clauses ) > 1 ? array_merge( array( 'relation' => 'AND' ), $clauses ) : $clauses;
			}
		}

		if ( ! empty( $args['tax_query'] ) && is_array( $args['tax_query'] ) ) {
			$clauses = array();
			foreach ( $args['tax_query'] as $clause ) {
				if ( is_array( $clause ) && isset( $clause['taxonomy'] ) ) {
					$clauses[] = $clause;
				}
			}
			if ( ! empty( $clauses ) ) {
				$args['tax_query'] = count( $clauses ) > 1 ? array_merge( array( 'relation' => 'AND' ), $clauses ) : $clauses;
			}
		}

		return $args;
	}
	add_filter( 'greenpath_filter_query_params', 'nuttergood_farmley_normalize_filter_query', 20, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_shop_assets' ) ) {
	function nuttergood_farmley_shop_assets() {
		if ( ! nuttergood_farmley_is_catalog_listing_page() && ! nuttergood_farmley_is_product_search_results() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();

		wp_enqueue_style( 'jquery-ui-theme', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.min.css', array(), '1.13.2' );
		wp_enqueue_script( 'jquery-ui-slider' );

		$css = $dir . '/assets/css/farmley-shop.css';
		$js  = $dir . '/assets/js/farmley-shop.js';
		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-shop',
				$uri . '/assets/css/farmley-shop.css',
				array( 'nuttergood-farmley-product-cards', 'greenpath-style' ),
				filemtime( $css )
			);
		}
		if ( file_exists( $js ) ) {
			wp_enqueue_script( 'nuttergood-farmley-shop', $uri . '/assets/js/farmley-shop.js', array( 'jquery', 'jquery-ui-slider', 'greenpath-main-js' ), filemtime( $js ), true );
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_shop_assets', 40 );
}

if ( ! function_exists( 'nuttergood_farmley_shop_inline_css' ) ) {
	function nuttergood_farmley_shop_inline_css( $style ) {
		if ( ! nuttergood_farmley_is_main_shop_page() ) {
			return $style;
		}

		$shop_id = (int) get_option( 'woocommerce_shop_page_id' );
		$bg_id   = (int) get_post_meta( $shop_id, 'qodef_page_title_background_image', true );
		$bg_url  = $bg_id ? wp_get_attachment_image_url( $bg_id, 'full' ) : '';

		$style .= '
.qodef-woo-product-list.qodef-filter-type--sidebar { grid-template-areas: none !important; column-gap: 32px !important; }
@media (min-width: 1025px) {
  .qodef-woo-product-list.qodef-filter-type--sidebar .qodef-filter-top-bar { grid-area: auto !important; grid-column: 1 / -1 !important; grid-row: 1 !important; }
  .qodef-woo-product-list.qodef-filter-type--sidebar .qodef-filter-content { grid-area: auto !important; grid-column: 1 / span 3 !important; grid-row: 2 !important; }
  .qodef-woo-product-list.qodef-filter-type--sidebar > ul.qodef-grid-inner { grid-area: auto !important; grid-column: 4 / -1 !important; grid-row: 2 !important; width: 100% !important; }
  .qodef-woo-product-list.qodef-filter-type--sidebar .qodef-m-pagination { grid-area: auto !important; grid-column: 4 / -1 !important; grid-row: 3 !important; }
}
.ng-farmley-product-cards .qodef-woo-product-list.qodef-gutter--normal > ul.qodef-grid-inner { column-gap: 18px !important; row-gap: 24px !important; }
.qodef-woo-product-list.qodef-gutter--normal > ul.qodef-grid-inner { column-gap: 30px !important; row-gap: 40px !important; }
.qodef-woo-product-list.qodef-filter--advanced .qodef-filter-item.qodef-product-rating,
.qodef-woo-product-list.qodef-filter--advanced .qodef-product-rating { display: none !important; }
';

		return $style;
	}
	add_filter( 'greenpath_filter_add_inline_style', 'nuttergood_farmley_shop_inline_css' );
}