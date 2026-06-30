<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_extend_block_categories' ) ) {
	/**
	 * Function that extend default array of block categories
	 *
	 * @param array $block_categories - Array of block categories
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_extend_block_categories( $block_categories ) {

		$block_categories[] = array(
			'slug'  => 'qode-woocommerce-blocks',
			'title' => esc_html__( 'Qode WooCommerce', 'qode-wishlist-for-woocommerce' ),
		);

		return $block_categories;
	}

	if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
		add_filter( 'block_categories_all', 'qode_wishlist_for_woocommerce_extend_block_categories' );
	} else {
		add_filter( 'block_categories', 'qode_wishlist_for_woocommerce_extend_block_categories' );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_enqueue_blocks_admin_styles' ) ) {
	/**
	 * Function that enqueue main blocks style for admin
	 */
	function qode_wishlist_for_woocommerce_enqueue_blocks_admin_styles() {
		// Enqueue global blocks CSS styles.
		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters
		wp_enqueue_style( 'qode-wishlist-for-woocommerce-main-editor', QODE_WISHLIST_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/blocks/main-editor.min.css' );

		$style = apply_filters( 'qode_wishlist_for_woocommerce_filter_add_inline_editor_style', '' );

		if ( ! empty( $style ) ) {
			wp_add_inline_style( 'qode-wishlist-for-woocommerce-main-editor', $style );
		}
	}

	add_action( 'admin_enqueue_scripts', 'qode_wishlist_for_woocommerce_enqueue_blocks_admin_styles' );
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_check_is_block_template_page' ) ) {
	/**
	 * Function that check is forward WooCommerce page a block template
	 *
	 * @param string $template_name
	 *
	 * @return bool
	 */
	function qode_wishlist_for_woocommerce_check_is_block_template_page( $template_name ) {
		static $page_flag = array();

		if ( ! isset( $page_flag[ $template_name ] ) ) {
			// Block templates are available since WooCommerce 7.9.
			$page_flag[ $template_name ] = function_exists( 'WC' ) && version_compare( WC()->version, '7.9.0', '>=' );

			// Predefined WooCommerce block templates.
			$predefined_templates = array( 'archive-product', 'product-search-results', 'single-product', 'taxonomy-product_attribute', 'taxonomy-product_cat', 'taxonomy-product_tag' );

			// Check is the current theme is Block theme.
			$page_flag[ $template_name ] = $page_flag[ $template_name ] && function_exists( 'wp_is_block_theme' ) && wp_is_block_theme();

			if ( $page_flag[ $template_name ] ) {
				$templates = get_block_templates( array( 'slug__in' => array( $template_name ) ) );

				// Helper function to check block templates content.
				$is_block_template = function ( $content ) use ( $template_name ) {
					switch ( $template_name ) {
						case 'cart':
							return has_block( 'woocommerce/cart', $content );
						case 'checkout':
							return has_block( 'woocommerce/checkout', $content );
						default:
							return ! has_block( 'woocommerce/legacy-template', $content );
					}
				};

				// Check block templates.
				if ( isset( $templates[0] ) ) {
					$content = $templates[0]->content;

					if ( ! $is_block_template( $content ) ) {
						$page_flag[ $template_name ] = false;
					} elseif ( has_block( 'core/pattern', $content ) ) {
						// Check block patterns.
						$blocks = parse_blocks( $content );

						foreach ( $blocks as $block ) {
							$name = $block['blockName'];

							if ( 'core/pattern' === $name && class_exists( 'WP_Block_Patterns_Registry' ) ) {
								$registry = WP_Block_Patterns_Registry::get_instance();
								$slug     = $block['attrs']['slug'] ?? '';

								if ( $registry->is_registered( $slug ) ) {
									$pattern = $registry->get_registered( $slug );

									if ( ! $is_block_template( $pattern['content'] ) ) {
										$page_flag[ $template_name ] = false;
										break;
									}
								}
							}
						}
					}
				} elseif ( ! in_array( $template_name, $predefined_templates, true ) ) {
					$page_flag[ $template_name ] = false;
				}
			}
		}

		return $page_flag[ $template_name ];
	}
}
