<?php

if ( ! function_exists( 'greenpath_core_include_woocommerce_shortcodes_widget' ) ) {
	/**
	 * Function that includes widgets
	 */
	function greenpath_core_include_woocommerce_shortcodes_widget() {
		foreach ( glob( GREENPATH_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/*/widget/include.php' ) as $widget ) {
			include_once $widget;
		}
	}

	add_action( 'qode_framework_action_before_widgets_register', 'greenpath_core_include_woocommerce_shortcodes_widget' );
}

if ( ! function_exists( 'greenpath_core_register_product_for_meta_options' ) ) {
	/**
	 * Function that register product post type for meta box options
	 *
	 * @param array $post_types
	 *
	 * @return array
	 */
	function greenpath_core_register_product_for_meta_options( $post_types ) {
		$post_types[] = 'product';

		return $post_types;
	}

	add_filter( 'qode_framework_filter_meta_box_save', 'greenpath_core_register_product_for_meta_options' );
	add_filter( 'qode_framework_filter_meta_box_remove', 'greenpath_core_register_product_for_meta_options' );
}

if ( ! function_exists( 'greenpath_core_woo_get_global_product' ) ) {
	/**
	 * Function that return global WooCommerce object
	 *
	 * @return object
	 */
	function greenpath_core_woo_get_global_product() {
		global $product;

		return $product;
	}
}

if ( ! function_exists( 'greenpath_core_woo_set_admin_options_map_position' ) ) {
	/**
	 * Function that set dashboard admin options map position for this module
	 *
	 * @param int    $position
	 * @param string $map
	 *
	 * @return int
	 */
	function greenpath_core_woo_set_admin_options_map_position( $position, $map ) {

		if ( 'woocommerce' === $map ) {
			$position = 70;
		}

		return $position;
	}

	add_filter( 'greenpath_core_filter_admin_options_map_position', 'greenpath_core_woo_set_admin_options_map_position', 10, 2 );
}

if ( ! function_exists( 'greenpath_core_include_woocommerce_shortcodes' ) ) {
	/**
	 * Function that includes shortcodes
	 */
	function greenpath_core_include_woocommerce_shortcodes() {
		foreach ( glob( GREENPATH_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/*/include.php' ) as $shortcode ) {
			include_once $shortcode;
		}
	}

	add_action( 'qode_framework_action_before_shortcodes_register', 'greenpath_core_include_woocommerce_shortcodes' );
}

if ( ! function_exists( 'greenpath_core_woo_product_get_rating_html' ) ) {
	/**
	 * Function that return ratings templates
	 *
	 * @param string $html - contains html content
	 * @param float  $rating
	 * @param int    $count - total number of ratings
	 *
	 * @return string
	 */
	function greenpath_core_woo_product_get_rating_html( $html, $rating, $count ) {
		return qode_framework_is_installed( 'theme' ) ? greenpath_woo_product_get_rating_html( $html, $rating, $count ) : '';
	}
}

if ( ! function_exists( 'greenpath_core_set_product_styles' ) ) {
	/**
	 * Function that generates module inline styles
	 *
	 * @param string $style
	 *
	 * @return string
	 */
	function greenpath_core_set_product_styles( $style ) {
		$price_styles        = greenpath_core_get_typography_styles( 'qodef_product_price' );
		$price_single_styles = greenpath_core_get_typography_styles( 'qodef_product_single_price' );

		if ( ! empty( $price_styles ) ) {
			$style .= qode_framework_dynamic_style(
				array(
					'#qodef-woo-page .price',
					'.qodef-woo-shortcode .price',
				),
				$price_styles
			);
		}

		if ( ! empty( $price_single_styles ) ) {
			$style .= qode_framework_dynamic_style(
				array(
					'#qodef-woo-page.qodef--single .entry-summary .price',
				),
				$price_single_styles
			);
		}

		$price_discount_styles        = array();
		$price_discount_color         = greenpath_core_get_option_value( 'admin', 'qodef_product_price_discount_color' );
		$price_single_discount_styles = array();
		$price_single_discount_color  = greenpath_core_get_option_value( 'admin', 'qodef_product_single_price_discount_color' );

		if ( ! empty( $price_discount_color ) ) {
			$price_discount_styles['color'] = $price_discount_color;
		}

		if ( ! empty( $price_single_discount_color ) ) {
			$price_single_discount_styles['color'] = $price_single_discount_color;
		}

		if ( ! empty( $price_discount_styles ) ) {
			$style .= qode_framework_dynamic_style(
				array(
					'#qodef-woo-page .price del',
					'.qodef-woo-shortcode .price del',
				),
				$price_discount_styles
			);
		}

		if ( ! empty( $price_single_discount_styles ) ) {
			$style .= qode_framework_dynamic_style(
				array(
					'#qodef-woo-page.qodef--single .entry-summary .price del',
				),
				$price_single_discount_styles
			);
		}

		$label_styles      = greenpath_core_get_typography_styles( 'qodef_product_label' );
		$info_styles       = greenpath_core_get_typography_styles( 'qodef_product_info' );
		$info_hover_styles = greenpath_core_get_typography_hover_styles( 'qodef_product_info' );

		if ( ! empty( $label_styles ) ) {
			$style .= qode_framework_dynamic_style(
				array(
					'#qodef-woo-page.qodef--single .product_meta .qodef-woo-meta-label',
					'#qodef-woo-page.qodef--single .entry-summary .qodef-custom-label',
				),
				$label_styles
			);
		}

		if ( ! empty( $info_styles ) ) {
			$style .= qode_framework_dynamic_style(
				array(
					'#qodef-woo-page.qodef--single .product_meta .qodef-woo-meta-value',
					'#qodef-woo-page.qodef--single .shop_attributes th',
					'#qodef-woo-page.qodef--single .woocommerce-Reviews .woocommerce-review__author',
				),
				$info_styles
			);
		}

		if ( ! empty( $info_hover_styles ) ) {
			$style .= qode_framework_dynamic_style(
				array(
					'#qodef-woo-page.qodef--single .product_meta .qodef-woo-meta-value a:hover',
				),
				$info_hover_styles
			);
		}

		return $style;
	}

	add_filter( 'greenpath_filter_add_inline_style', 'greenpath_core_set_product_styles' );
}

if ( ! function_exists( 'greenpath_core_generate_woo_product_single_layout' ) ) {
	/**
	 * Function that return default layout for custom post type single page
	 *
	 * @return string
	 */
	function greenpath_core_generate_woo_product_single_layout() {

		$single_template = greenpath_core_get_post_value_through_levels( 'qodef_woo_single_layout', get_the_ID() );
		$single_template = ! empty( $single_template ) ? $single_template : '';

		return $single_template;
	}
}

if ( ! function_exists( 'greenpath_core_load_single_woo_template_hooks' ) ) {
	/**
	 * Function that add hook depend of item layout
	 *
	 */
	function greenpath_core_load_single_woo_template_hooks() {

		if ( is_singular( 'product' ) ) {
			$item_layout = greenpath_core_generate_woo_product_single_layout();

			$item_layout = str_replace( '-', '_', $item_layout );

			do_action( 'greenpath_core_action_load_template_hooks_' . $item_layout );
		}
	}

	add_action( 'wp', 'greenpath_core_load_single_woo_template_hooks' );
}

if ( ! function_exists( 'greenpath_core_set_woo_product_body_classes' ) ) {

	function greenpath_core_set_woo_product_body_classes( $classes ) {
		if ( is_singular( 'product' ) ) {
			$item_layout   = greenpath_core_generate_woo_product_single_layout();

			if ( ! empty( $item_layout ) ) {
				$classes[] = ' qodef-product-layout--' . $item_layout;
			}
		}

		return $classes;
	}

	add_filter( 'body_class', 'greenpath_core_set_woo_product_body_classes' );
}

if ( ! function_exists( 'greenpath_core_woo_get_fake_live_viewing_message' ) ) {
	/**
	 * Function for adding fake live viewing message
	 *
	 */
	function greenpath_core_woo_get_fake_live_viewing_message() {
		$fake_lw_enabled = 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_woo_single_enable_fake_live_viewing' );

		if ( $fake_lw_enabled ) {
			$flw_min   = greenpath_core_get_post_value_through_levels( 'qodef_woo_single_fake_live_viewing_min' );
			$flw_min   = ! empty( $flw_min ) ? intval( $flw_min ) : 2;
			$flw_max   = greenpath_core_get_post_value_through_levels( 'qodef_woo_single_fake_live_viewing_max' );
			$flw_max   = ! empty( $flw_max ) ? intval( $flw_max ) : 9;
			$flw_count = wp_rand( $flw_min, $flw_max );
			echo '<div class="qodef-woo-live-viewing">';
			// translators: %s - number of fake live viewers
			echo '<div class="qodef-woo-live-viewing-message">' . greenpath_core_get_svg_icon( 'eyes' ) . esc_html( sprintf( _n( '%s person currently viewing this item', '%s people currently viewing this item', $flw_count, 'greenpath_core' ), $flw_count ) ) . '</div>';
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'greenpath_core_woo_get_sales_count_message' ) ) {
	/**
	 * Function for adding sale count message
	 *
	 */
	function greenpath_core_woo_get_sales_count_message() {
		$sale_count_enabled = 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_woo_single_enable_sales_count' );

		if ( $sale_count_enabled ) {
			$sale_count_type = greenpath_core_get_post_value_through_levels( 'qodef_woo_single_sales_count_type' );
			$product         = greenpath_core_woo_get_global_product();

			echo '<div class="qodef-woo-sales-count">';
			if ( 'fake' === $sale_count_type ) {
				$fsc_min         = greenpath_core_get_post_value_through_levels( 'qodef_woo_single_fake_sales_count_min' );
				$fsc_min         = ! empty( $fsc_min ) ? intval( $fsc_min ) : 1;
				$fsc_max         = greenpath_core_get_post_value_through_levels( 'qodef_woo_single_fake_sales_count_max' );
				$fsc_max         = ! empty( $fsc_max ) ? intval( $fsc_max ) : 5;
				$fsc_count       = wp_rand( $fsc_min, $fsc_max );
				$fsc_time_frame  = greenpath_core_get_post_value_through_levels( 'qodef_woo_single_fake_sales_time_frame' );
				$fsc_time_frame  = ! empty( $fsc_time_frame ) ? intval( $fsc_time_frame ) : 3;
				$fsc_time_period = greenpath_core_get_post_value_through_levels( 'qodef_woo_single_fake_sales_time_period' );
				$fsc_time_period = ! empty( $fsc_time_period ) ? $fsc_time_period : 'hour';
				switch ( $fsc_time_period ) {
					case 'minute':
						// translators: %s - time frame
						$fsc_time_period = sprintf( _n( '%s minute', '%s minutes', $fsc_time_frame, 'greenpath-core' ), $fsc_time_frame );
						break;
					case 'hour':
						// translators: %s - time frame
						$fsc_time_period = sprintf( _n( '%s hour', '%s hours', $fsc_time_frame, 'greenpath-core' ), $fsc_time_frame );
						break;
					case 'day':
						// translators: %s - time frame
						$fsc_time_period = sprintf( _n( '%s day', '%s days', $fsc_time_frame, 'greenpath-core' ), $fsc_time_frame );
						break;
					case 'week':
						// translators: %s - time frame
						$fsc_time_period = sprintf( _n( '%s week', '%s weeks', $fsc_time_frame, 'greenpath-core' ), $fsc_time_frame );
						break;
				}
				echo '<div class="qodef-woo-sales-count-message">';
				greenpath_core_render_svg_icon( 'fire' );
				// translators: %s - fake sales count
				echo esc_html( sprintf( _n( '%s item sold in last ', '%s items sold in last ', $fsc_count, 'greenpath-core' ), $fsc_count ) . $fsc_time_period );
				echo '</div>';
			} else {
				$total_sales = $product->get_total_sales();
				// translators: %s - total sales count
				echo '<div class="qodef-woo-sales-count-message">' . greenpath_core_get_svg_icon( 'fire' ) . esc_html( sprintf( _n( '%s item sold', '%s items sold', $total_sales, 'greenpath-core' ), $total_sales ) ) . '</div>';
			}
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'greenpath_core_woo_get_sale_booster_features' ) ) {
	/**
	 * Function for adding fake live viewing message and sale count message
	 *
	 */
	function greenpath_core_woo_get_sale_booster_features() {
		$fake_lw_enabled    = 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_woo_single_enable_fake_live_viewing' );
		$sale_count_enabled = 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_woo_single_enable_sales_count' );

		if ( $fake_lw_enabled || $sale_count_enabled ) {
			echo '<div class="qodef-sale-boosters">';
			greenpath_core_woo_get_fake_live_viewing_message();
			greenpath_core_woo_get_sales_count_message();
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'greenpath_core_woo_get_progress_bar' ) ) {
	/**
	 * Function for adding free shipping progress bar
	 *
	 */
	function greenpath_core_woo_get_progress_bar() {
		$progress_bar_enabled = 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_woo_cart_enable_progress_bar' );

		if ( $progress_bar_enabled ) {
			$pb_max               = greenpath_core_get_post_value_through_levels( 'qodef_woo_cart_progress_bar_amount' );
			$pb_prefix            = greenpath_core_get_post_value_through_levels( 'qodef_woo_cart_progress_bar_amount_prefix' );
			$pb_suffix            = greenpath_core_get_post_value_through_levels( 'qodef_woo_cart_progress_bar_amount_suffix' );
			$free_shipping_amount = ! empty( $pb_max ) ? intval( $pb_max ) : 85;
			$cart_subtotal        = 0;

			if ( is_object( WC()->cart ) ) {
				$cart_subtotal = WC()->cart->get_displayed_subtotal();
			}
			echo '<div class="qodef-woo-cart-progress-bar">';
			if ( $cart_subtotal >= $free_shipping_amount ) {
				echo '<div class="qodef-woo-cart-progress-bar-message qodef-full-progress">' . greenpath_core_get_svg_icon( 'truck' ) . esc_html__( "Congratulations! You've got the free shipping.", 'greenpath-core' ) . '</div>';
			} else {
				echo '<div class="qodef-woo-cart-progress-bar-message">' . greenpath_core_get_svg_icon( 'truck' ) . esc_html( $pb_prefix ) . '<span class="qodef-woo-cart-progress-bar-amount">' . wc_price( $free_shipping_amount ) . '</span>' . esc_html( $pb_suffix ) . '</div>';
				if ( class_exists( 'GreenPathCore_Progress_Bar_Shortcode' ) ) {
					$params = array(
						'number'              => intval( ( $cart_subtotal / $free_shipping_amount ) * 100 ),
						'title'               => '',
						'active_line_width'   => 10,
						'active_line_color'   => '#88A842',
						'inactive_line_width' => 10,
						'inactive_line_color' => '#B5D176',
					);
					echo GreenPathCore_Progress_Bar_Shortcode::call_shortcode( $params );
				}
			}
			echo '</div>';
		}
	}
}


if ( ! function_exists( 'greenpath_core_woo_get_countdown' ) ) {
	/**
	 * Function for adding countdown on cart
	 *
	 */
	function greenpath_core_woo_get_countdown() {
		$countdown_enabled = 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_woo_cart_enable_countdown' );

		if ( $countdown_enabled ) {
			$data                 = array();
			$minutes              = greenpath_core_get_post_value_through_levels( 'qodef_woo_cart_countdown_minutes' );
			$minutes              = ! empty( $minutes ) ? intval( $minutes ) : 5;
			$data['data-minutes'] = $minutes;

			echo '<div class="qodef-woo-cart-countdown"' . qode_framework_get_inline_attrs( $data ) . '>';
			echo '<div class="qodef-woo-cart-countdown-message">' . greenpath_core_get_svg_icon( 'fire' ) . esc_html__( 'Limited quantities available. Checkout within ', 'greenpath-core' ) . '<span class="qodef-woo-cart-countdown-counter">' . $minutes . ':00</span></div>';
			echo '<div class="qodef-woo-cart-countdown-expired-message qodef--hidden">' . esc_html__( 'You are out of time! Checkout now to avoid losing your order!', 'greenpath-core' ) . '</div>';
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'greenpath_core_woo_get_cart_sale_booster_features' ) ) {
	/**
	 * Function for adding fake live viewing message and sale count message
	 *
	 */
	function greenpath_core_woo_get_cart_sale_booster_features() {
		$progress_bar_enabled = 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_woo_cart_enable_progress_bar' );
		$countdown_enabled    = 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_woo_cart_enable_countdown' );

		if ( $progress_bar_enabled || $countdown_enabled ) {
			echo '<div class="qodef-sale-boosters">';
			greenpath_core_woo_get_countdown();
			greenpath_core_woo_get_progress_bar();
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'greenpath_core_woo_show_overall_reviews' ) ) {
	/**
	 * Function for adding overall reviews info
	 *
	 */
	function greenpath_core_woo_show_overall_reviews( $reviews_title, $count, $product ) {
		$unset_params = array( 'with_title' );
		return $reviews_title . greenpath_core_list_review_details( 'per-mark', 'rating', $unset_params );
	}
}

if ( ! function_exists( 'greenpath_core_woo_return_module_part' ) ) {
	function greenpath_core_woo_return_module_part( $module ) {
		return $module;
	}
}

if ( ! function_exists( 'greenpath_core_add_rest_api_author_pagination_global_variables' ) ) {
	/**
	 * Extend main rest api variables with new case
	 *
	 * @param array $global - list of variables
	 * @param string $namespace - rest namespace url
	 *
	 * @return array
	 */
	function greenpath_core_add_rest_api_woo_refresh_free_shipping_global_variables( $global, $namespace ) {
		$global['wooFreeShippingRestRoute'] = $namespace . '/woo-refresh-free-shipping';

		return $global;
	}

	add_filter( 'greenpath_filter_rest_api_global_variables', 'greenpath_core_add_rest_api_woo_refresh_free_shipping_global_variables', 10, 2 );
}

if ( ! function_exists( 'greenpath_core_add_rest_api_woo_refresh_free_shipping_route' ) ) {
	/**
	 * Extend main rest api routes with new case
	 *
	 * @param array $routes - list of rest routes
	 *
	 * @return array
	 */
	function greenpath_core_add_rest_api_woo_refresh_free_shipping_route( $routes ) {
		$routes['woo-refresh-free-shipping'] = array(
			'route'    => 'woo-refresh-free-shipping',
			'methods'  => WP_REST_Server::READABLE,
			'callback' => 'greenpath_core_woo_refresh_free_shipping',
			'args'     => array(
				'options' => array(
					'required'          => false,
					'validate_callback' => function ( $param, $request, $key ) {
						// Simple solution for validation can be 'is_array' value instead of callback function
						return is_array( $param ) ? $param : (array) $param;
					},
					'description'       => esc_html__( 'Options data is array with all selected shortcode parameters value', 'greenpath-core' ),
				),
			),
		);

		return $routes;
	}

	add_filter( 'greenpath_filter_rest_api_routes', 'greenpath_core_add_rest_api_woo_refresh_free_shipping_route' );
}

if ( ! function_exists( 'greenpath_core_woo_refresh_free_shipping' ) ) {
	/**
	 * Function for adding overall reviews info
	 *
	 */
	function greenpath_core_woo_refresh_free_shipping() {

		ob_start();
		greenpath_core_woo_get_progress_bar();
		$content = ob_get_contents();
		ob_end_clean();

		qode_framework_get_ajax_status( 'success', esc_html__( 'Html is loaded', 'greenpath-core' ), $content );

	}
}

if ( ! function_exists( 'greenpath_core_remove_from_rest_api' ) ) {
	/**
	 * Function for adding overall reviews info
	 *
	 */
	function greenpath_core_remove_from_rest_api( $is_rest_api_request ) {

		if ( false !== strpos( $_SERVER['REQUEST_URI'], 'woo-refresh-free-shipping' ) ) {
			return false;
		}

		return $is_rest_api_request;
	}

	add_filter( 'woocommerce_is_rest_api_request', 'greenpath_core_remove_from_rest_api' );
}

if ( ! function_exists( 'greenpath_core_woo_product_get_single_rating_html' ) ) {
	/**
	 * Function that override single product ratings template
	 */
	function greenpath_core_woo_product_get_single_rating_html() {
		if ( function_exists( 'wc_review_ratings_enabled' ) && ! wc_review_ratings_enabled() ) {
			return;
		}
		$product      = greenpath_core_woo_get_global_product();
		$rating_count = $product->get_rating_count();
		$review_count = $product->get_review_count();
		$average      = $product->get_average_rating();

		$html  = '<div class="woocommerce-product-rating qodef-woo-single-product-rating">';
		$html .= wc_get_rating_html( $average, $rating_count );
		if ( comments_open() ) :
			//phpcs:disable
			$html .= '<a href="#reviews" class="woocommerce-review-link" rel="nofollow"><span class="average">' . esc_html( $average ) . '</span>(' . sprintf( _n( '%s customer review', '%s customer reviews', $review_count, 'greenpath-core' ), '<span class="count">' . esc_html( $review_count ) . '</span>' ) . ')</a>';
			// phpcs:enable
		endif;
		$html .= '</div>';

		echo greenpath_core_woo_return_module_part( $html );
	}
}

if ( ! function_exists( 'greenpath_core_get_info_panel_section' ) ) {
	/**
	 * Function for adding custom details template for product
	 *
	 */
	function greenpath_core_get_info_panel_section() {
		$info_panel_meta    = get_post_meta( get_the_ID(), 'qodef_woo_single_info_panels', true );
		$info_panel_section = '';

		if ( ! empty( $info_panel_meta ) ) {

			$info_panel_section .= '<div class="qodef-info-panel">';

			foreach ( $info_panel_meta as $info_panel ) {
				$info_panel_section .= '<div class="qodef-info-panel-item">';
				$info_panel_section .=	'<span class="qodef-info-panel-item-icon">' . qode_framework_wp_kses_html( 'svg', $info_panel['qodef_woo_single_info_panel_icon_svg'] ) . '</span>';
				$info_panel_section .=	'<div class="qodef-info-panel-item-text-holder">';
				$info_panel_section .=	 '<span class="qodef-info-panel-item-bold-text">' . qode_framework_wp_kses_html( 'content', $info_panel['qodef_woo_single_info_panel_bold_text'] ) . '</span>';
				$info_panel_section .=	 '<span class="qodef-info-panel-item-text">' . qode_framework_wp_kses_html( 'content', $info_panel['qodef_woo_single_info_panel_text'] ) . '</span>';
				$info_panel_section .=	'</div>';
				$info_panel_section .= '</div>';
			}

			$info_panel_section .= '</div>';
		}

		echo greenpath_core_woo_return_module_part( $info_panel_section );
	}
}

if ( ! function_exists( 'greenpath_core_woo_sticky_start' ) ) {

	function greenpath_core_woo_sticky_start() {
		$single_template = greenpath_core_get_post_value_through_levels( 'qodef_woo_single_layout', get_the_ID() );
		$html            = '';

		if ( 'product' === get_post_type() && ! empty( $single_template ) && 'big-gallery' === $single_template ) {
			$html .= '<div class="qodef-sticky-column--enable qodef-sticky-column-snap-to--top">';
		}

		echo qode_framework_wp_kses_html( 'content', $html );
	}
}

if ( ! function_exists( 'greenpath_core_woo_sticky_end' ) ) {

	function greenpath_core_woo_sticky_end() {
		$single_template = greenpath_core_get_post_value_through_levels( 'qodef_woo_single_layout', get_the_ID() );
		$html            = '';

		if ( 'product' === get_post_type() && ! empty( $single_template ) && 'big-gallery' === $single_template ) {
			$html .= '</div>';
		}

		echo qode_framework_wp_kses_html( 'content', $html );
	}
}

if ( ! function_exists( 'greenpath_core_register_brand_woocommerce_taxonomy' ) ) {
	/**
	 * Function for registering Brand taxonomy for WooCommerce Product
	 */
	function greenpath_core_register_brand_woocommerce_taxonomy() {

		register_taxonomy(
			'product_brand',
			apply_filters( 'woocommerce_taxonomy_objects_product_brand', array( 'product' ) ),
			apply_filters(
				'woocommerce_taxonomy_args_product_brand',
				array(
					'hierarchical'          => true,
					'update_count_callback' => '_wc_term_recount',
					'label'                 => __( 'Brand', 'greenpath-core' ),
					'labels'                => array(
						'name'              => __( 'Product Brand', 'greenpath-core' ),
						'singular_name'     => __( 'Brand', 'greenpath-core' ),
						'menu_name'         => _x( 'Brands', 'Admin menu name', 'greenpath-core' ),
						'search_items'      => __( 'Search Brands', 'greenpath-core' ),
						'all_items'         => __( 'All Brands', 'greenpath-core' ),
						'parent_item'       => __( 'Parent Brand', 'greenpath-core' ),
						'parent_item_colon' => __( 'Parent Brand:', 'greenpath-core' ),
						'edit_item'         => __( 'Edit Brand', 'greenpath-core' ),
						'update_item'       => __( 'Update Brand', 'greenpath-core' ),
						'add_new_item'      => __( 'Add new Brand', 'greenpath-core' ),
						'new_item_name'     => __( 'New Brand name', 'greenpath-core' ),
						'not_found'         => __( 'No Brands found', 'greenpath-core' ),
					),
					'show_ui'               => true,
					'query_var'             => true,
					'capabilities'          => array(
						'manage_terms' => 'manage_product_terms',
						'edit_terms'   => 'edit_product_terms',
						'delete_terms' => 'delete_product_terms',
						'assign_terms' => 'assign_product_terms',
					),
					'rewrite'               => array(
						'slug'         => 'product-brand',
						'with_front'   => false,
						'hierarchical' => true,
					),
				)
			)
		);
	}

	add_action( 'init', 'greenpath_core_register_brand_woocommerce_taxonomy' );
}

if ( ! function_exists( 'greenpath_core_woo_custom_cart_remove_link' ) ) {
	/**
	 * Function that overrides the Cart remove link template
	 */
	function greenpath_core_woo_custom_cart_remove_link( $cart_item_key ) {

		return sprintf(
			'<div class="%s">%s' . $cart_item_key . '</div>',
			esc_attr__( 'qodef-remove-text', 'greenpath-core' ),
			esc_html__( 'Remove', 'greenpath-core' )
		);
	}

	add_filter( 'woocommerce_cart_item_remove_link', 'greenpath_core_woo_custom_cart_remove_link' );
}

if ( ! function_exists( 'greenpath_core_woo_add_logo_to_side_cart' ) ) {
	/**
	 * Function that adds the logo image to Side Area Cart
	 */
	function greenpath_core_woo_add_logo_to_side_cart() {
		add_action( 'greenpath_core_action_woocommerce_before_side_area_cart_content', 'greenpath_core_get_header_logo_image' );
	}
}

if ( ! function_exists( 'greenpath_core_is_woo_page' ) ) {
	/**
	 * Function that check WooCommerce pages
	 *
	 * @param string $page
	 *
	 * @return bool
	 */
	function greenpath_core_is_woo_page( $page ) {

		if( qode_framework_is_installed( 'theme' ) ) {
			return greenpath_is_woo_page( $page );
		}

		return false;
	}
}

if ( ! function_exists( 'greenpath_core_single_display_cart_showcase' ) ) {
	/**
	 * Function that displays the Product Cart Showcase shortcode on single pages
	 *
	 */
	function greenpath_core_single_display_cart_showcase() {
		$enabled = greenpath_core_get_post_value_through_levels( 'qodef_woo_single_enable_cart_showcase', get_the_ID() );

		if( ! empty( $enabled ) && 'yes' === $enabled ) {
			echo GreenPathCore_Product_Cart_Showcase_Shortcode::call_shortcode( array() );
		}
	}
}

if ( ! function_exists( 'greenpath_core_single_cart_showcase_wrapper' ) ) {
	/**
	 * Function that displays additional tags around the Product Cart Showcase shortcode on single pages
	 *
	 */
	function greenpath_core_single_cart_showcase_wrapper() {
		$enabled = greenpath_core_get_post_value_through_levels( 'qodef_woo_single_enable_cart_showcase', get_the_ID() );

		if( ! empty( $enabled ) && 'yes' === $enabled ) {
			$html = '';

			$html .= '<div class="qodef-product-cart-showcase-wrapper">';
			$html .= '<h3 class="qodef-showcase-heading">';
			$html .= esc_html__( 'Buy it with', 'greenpath-core' );
			$html .= '</h3>';

			echo qode_framework_wp_kses_html( 'content', $html );
		}
	}
}

if ( ! function_exists( 'greenpath_core_single_cart_showcase_wrapper_end' ) ) {
	/**
	 * Function that displays additional tags around the Product Cart Showcase shortcode on single pages
	 *
	 */
	function greenpath_core_single_cart_showcase_wrapper_end() {
		$enabled = greenpath_core_get_post_value_through_levels( 'qodef_woo_single_enable_cart_showcase', get_the_ID() );

		if( ! empty( $enabled ) && 'yes' === $enabled ) {
			$html = '';

			$html .= '</div>';

			echo qode_framework_wp_kses_html( 'content', $html );
		}
	}
}
