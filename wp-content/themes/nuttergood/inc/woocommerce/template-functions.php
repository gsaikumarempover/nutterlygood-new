<?php

/**
 * Global templates hooks
 */

if ( ! function_exists( 'greenpath_add_main_woo_page_template_holder' ) ) {
	/**
	 * Function that render additional content for main shop page
	 */
	function greenpath_add_main_woo_page_template_holder() {
		echo '<main id="qodef-page-content" class="qodef-grid qodef-layout--template ' . esc_attr( greenpath_get_page_grid_sidebar_classes() ) . ' ' . esc_attr( greenpath_get_grid_gutter_classes() ) . '" ' . greenpath_get_grid_gutter_styles() . ' role="main"><div class="qodef-grid-inner">';
	}
}

if ( ! function_exists( 'greenpath_add_main_woo_page_template_holder_end' ) ) {
	/**
	 * Function that render additional content for main shop page
	 */
	function greenpath_add_main_woo_page_template_holder_end() {
		echo '</div></main>';
	}
}

if ( ! function_exists( 'greenpath_add_main_woo_page_holder' ) ) {
	/**
	 * Function that render additional content around WooCommerce pages
	 */
	function greenpath_add_main_woo_page_holder() {
		$classes = array();

		// add class to single page
		if ( greenpath_is_woo_page( 'single' ) ) {
			$classes[] = 'qodef-grid-item qodef-col--12';
		}

		// add class to pages with sidebar
		if ( greenpath_is_woo_page( 'archive' ) ) {
			$classes[] = 'qodef-grid-item';
			$classes[] = greenpath_get_page_content_sidebar_classes();
		}

		$classes[] = greenpath_get_woo_main_page_classes();

		echo '<div id="qodef-woo-page" class="' . esc_attr( implode( ' ', $classes ) ) . '">';
	}
}

if ( ! function_exists( 'greenpath_add_main_woo_page_holder_end' ) ) {
	/**
	 * Function that render additional content around WooCommerce pages
	 */
	function greenpath_add_main_woo_page_holder_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_add_main_woo_page_sidebar_holder' ) ) {
	/**
	 * Function that render sidebar layout for main shop page
	 */
	function greenpath_add_main_woo_page_sidebar_holder() {

		if ( ! greenpath_is_woo_page( 'single' ) ) {
			// Include page content sidebar
			greenpath_template_part( 'sidebar', 'templates/sidebar' );
		}
	}
}

/**
 * Shop page templates hooks
 */

if ( ! function_exists( 'greenpath_add_results_and_ordering_holder' ) ) {
	/**
	 * Function that render additional content around results and ordering templates on main shop page
	 */
	function greenpath_add_results_and_ordering_holder() {
		echo '<div class="qodef-woo-results">';
	}
}

if ( ! function_exists( 'greenpath_add_results_and_ordering_holder_end' ) ) {
	/**
	 * Function that render additional content around results and ordering templates on main shop page
	 */
	function greenpath_add_results_and_ordering_holder_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_add_product_list_item_holder' ) ) {
	/**
	 * Function that render additional content around product list item on main shop page
	 */
	function greenpath_add_product_list_item_holder() {
		echo '<div class="qodef-e-inner">';
	}
}

if ( ! function_exists( 'greenpath_add_product_list_item_holder_end' ) ) {
	/**
	 * Function that render additional content around product list item on main shop page
	 */
	function greenpath_add_product_list_item_holder_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_add_product_list_item_media_holder' ) ) {
	/**
	 * Function that render additional content around image template on main shop page
	 */
	function greenpath_add_product_list_item_media_holder() {
		echo '<div class="qodef-e-media">';
	}
}

if ( ! function_exists( 'greenpath_add_product_list_item_media_holder_end' ) ) {
	/**
	 * Function that render additional content around image template on main shop page
	 */
	function greenpath_add_product_list_item_media_holder_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_add_product_list_item_media_image_holder' ) ) {
	/**
	 * Function that render additional content around image template on main shop page
	 */
	function greenpath_add_product_list_item_media_image_holder() {
		echo '<div class="qodef-e-media-image">';
		do_action( 'qodef_woo_product_list_title_tag_link_open' );
	}
}

if ( ! function_exists( 'greenpath_add_product_list_item_media_image_holder_end' ) ) {
	/**
	 * Function that render additional content around image template on main shop page
	 */
	function greenpath_add_product_list_item_media_image_holder_end() {
		do_action( 'qodef_woo_product_list_title_tag_link_close' );
		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_add_product_list_item_price_holder' ) ) {
	/**
	 * Function that render additional content around image and sale templates on main shop page
	 */
	function greenpath_add_product_list_item_price_holder() {
		echo '<div class="qodef-e-price-holder">';
	}
}

if ( ! function_exists( 'greenpath_add_product_list_item_price_holder_end' ) ) {
	/**
	 * Function that render additional content around image and sale templates on main shop page
	 */
	function greenpath_add_product_list_item_price_holder_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_add_product_list_item_content_holder' ) ) {
	/**
	 * Function that render additional content around product info on main shop page
	 */
	function greenpath_add_product_list_item_content_holder() {
		echo '<div class="qodef-e-content">';
	}
}

if ( ! function_exists( 'greenpath_add_product_list_item_content_holder_end' ) ) {
	/**
	 * Function that render additional content around product info on main shop page
	 */
	function greenpath_add_product_list_item_content_holder_end() {

		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_add_product_list_item_rating_holder' ) ) {
	/**
	 * Function that render additional content around product info on main shop page
	 */
	function greenpath_add_product_list_item_action_holder() {
		echo '<div class="qodef-action-holder">';
	}
}

if ( ! function_exists( 'greenpath_add_product_list_item_top_and_info_holder_end' ) ) {
	/**
	 * Function that render additional content around product info on main shop page
	 */
	function greenpath_add_product_list_item_action_holder_end() {
		// Hook to include additional content inside product list item content
		do_action( 'greenpath_action_product_list_item_additional_content' );

		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_add_product_list_item_categories' ) ) {
	/**
	 * Function that render product categories
	 */
	function greenpath_add_product_list_item_categories() {
		$categories = wp_get_post_terms( get_the_ID(), 'product_cat' );

		if ( ! empty( $categories ) ) { ?>
			<?php echo get_the_term_list( get_the_ID(), 'product_cat', '', '<span class="qodef-info-separator-single"></span>' ); ?>
			<div class="qodef-info-separator-end"></div>
			<?php
		}
	}
}

/**
 * Product single page templates hooks
 */

if ( ! function_exists( 'greenpath_add_product_single_content_holder' ) ) {
	/**
	 * Function that render additional content around image and summary templates on single product page
	 */
	function greenpath_add_product_single_content_holder() {
		echo '<div class="qodef-woo-single-inner">';
	}
}

if ( ! function_exists( 'greenpath_add_product_single_content_holder_end' ) ) {
	/**
	 * Function that render additional content around image and summary templates on single product page
	 */
	function greenpath_add_product_single_content_holder_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_add_product_single_image_holder' ) ) {
	/**
	 * Function that render additional content around featured image on single product page
	 */
	function greenpath_add_product_single_image_holder() {
		echo '<div class="qodef-woo-single-image">';
	}
}

if ( ! function_exists( 'greenpath_add_product_single_image_holder_end' ) ) {
	/**
	 * Function that render additional content around featured image on single product page
	 */
	function greenpath_add_product_single_image_holder_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_add_product_single_additional_buttons_holder' ) ) {
	/**
	 * Function that render additional content around featured image on single product page
	 */
	function greenpath_add_product_single_additional_buttons_holder() {
		echo '<div class="qodef-woo-single-buttons">';
	}
}

if ( ! function_exists( 'greenpath_add_product_single_additional_buttons_holder_end' ) ) {
	/**
	 * Function that render additional content around featured image on single product page
	 */
	function greenpath_add_product_single_additional_buttons_holder_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_woo_product_render_social_share_html' ) ) {
	/**
	 * Function that render social share html
	 */
	function greenpath_woo_product_render_social_share_html() {
		$social_share_enabled = 'yes' === greenpath_get_post_value_through_levels( 'qodef_woo_enable_social_share' );
		$social_share_layout  = greenpath_get_post_value_through_levels( 'qodef_social_share_layout' );

		if ( class_exists( 'GreenPathCore_Social_Share_Shortcode' ) && $social_share_enabled ) {
			$params = array(
				'title'  => esc_html__( 'Share:', 'nuttergood' ),
				'layout' => $social_share_layout,
			);

			echo GreenPathCore_Social_Share_Shortcode::call_shortcode( $params ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

if ( ! function_exists( 'greenpath_woo_product_render_slider_html' ) ) {
	/**
	 * Function that render slider html
	 */
	function greenpath_woo_product_render_slider_html( $params = array() ) {
		echo greenpath_get_template_part( 'woocommerce', 'templates/parts/slider', '', $params );
	}
}

/**
 * Override default WooCommerce templates
 */

if ( ! function_exists( 'greenpath_woo_disable_page_heading' ) ) {
	/**
	 * Function that disable heading template on main shop page
	 *
	 * @return bool
	 */
	function greenpath_woo_disable_page_heading() {
		return false;
	}
}

if ( ! function_exists( 'greenpath_add_product_list_holder' ) ) {
	/**
	 * Function that add additional content around product lists on main shop page
	 *
	 * @param string $html
	 *
	 * @return string which contains html content
	 */
	function greenpath_add_product_list_holder( $html ) {
		$classes        = array();
		$layout         = greenpath_get_post_value_through_levels( 'qodef_product_list_item_layout' );
		$space          = greenpath_get_post_value_through_levels( 'qodef_woo_product_list_columns_space' );
		$vertical_space = greenpath_get_post_value_through_levels( 'qodef_woo_product_list_columns_vertical_space' );

		if ( ! empty( $layout ) ) {
			$classes[] = 'qodef-item-layout--' . $layout;
		}

		if ( ! empty( $space ) ) {
			$classes[] = 'qodef-gutter--' . $space;
		}

		if ( ! empty( $vertical_space ) ) {
			$classes[] = 'qodef-vertical-gutter--' . $vertical_space;
		}

		$styles = greenpath_get_gutter_custom_styles( 'qodef_woo_product_list_columns_space_', 'qodef_woo_product_list_columns_vertical_space_', array(), true );

		return '<div class="qodef-woo-product-list ' . esc_attr( implode( ' ', $classes ) ) . '" ' . greenpath_get_inline_style( $styles ) . '>' . $html;
	}
}

if ( ! function_exists( 'greenpath_add_product_list_holder_end' ) ) {
	/**
	 * Function that add additional content around product lists on main shop page
	 *
	 * @param string $html
	 *
	 * @return string which contains html content
	 */
	function greenpath_add_product_list_holder_end( $html ) {
		return $html . '</div>';
	}
}

if ( ! function_exists( 'greenpath_woo_product_list_columns' ) ) {
	/**
	 * Function that set number of columns for main shop page
	 *
	 * @return int
	 */
	function greenpath_woo_product_list_columns() {
		$option = greenpath_get_post_value_through_levels( 'qodef_woo_product_list_columns' );

		if ( ! empty( $option ) ) {
			$columns = intval( $option );
		} else {
			$columns = 3;
		}

		return $columns;
	}
}

if ( ! function_exists( 'greenpath_woo_products_per_page' ) ) {
	/**
	 * Function that set number of items for main shop page
	 *
	 * @param int $products_per_page
	 *
	 * @return int
	 */
	function greenpath_woo_products_per_page( $products_per_page ) {
		$option = greenpath_get_post_value_through_levels( 'qodef_woo_product_list_products_per_page' );

		if ( ! empty( $option ) ) {
			$products_per_page = intval( $option );
		}

		return $products_per_page;
	}
}

if ( ! function_exists( 'greenpath_woo_pagination_args' ) ) {
	/**
	 * Function that override pagination args on main shop page
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	function greenpath_woo_pagination_args( $args ) {
		$args['prev_text']          = greenpath_get_svg_icon( 'pagination-arrow-left' );
		$args['next_text']          = greenpath_get_svg_icon( 'pagination-arrow-right' );
		$args['type']               = 'plain';
		$args['before_page_number'] = '0';

		return $args;
	}
}

if ( ! function_exists( 'greenpath_add_single_product_classes' ) ) {
	/**
	 * Function that render additional content around WooCommerce pages
	 *
	 * @param array  $classes Default argument array
	 * @param string $class
	 * @param int    $post_id
	 *
	 * @return array
	 */
	function greenpath_add_single_product_classes( $classes, $class = '', $post_id = 0 ) {
		if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'product', 'product_variation' ), true ) ) {
			return $classes;
		}

		$product = wc_get_product( $post_id );

		if ( $product ) {
			$new = get_post_meta( $post_id, 'qodef_show_new_sign', true );

			if ( 'yes' === $new ) {
				$classes[] = 'new';
			}
		}

		return $classes;
	}
}

if ( ! function_exists( 'greenpath_add_sale_flash_on_product' ) ) {
	/**
	 * Function for adding on sale template for product
	 */
	function greenpath_add_sale_flash_on_product() {
		echo greenpath_woo_set_sale_flash(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'greenpath_woo_set_sale_flash' ) ) {
	/**
	 * Function that override on sale template for product
	 *
	 * @return string which contains html content
	 */
	function greenpath_woo_set_sale_flash() {
		$product = greenpath_woo_get_global_product();

		if ( ! empty( $product ) && $product->is_on_sale() && $product->is_in_stock() ) {
			return greenpath_woo_get_woocommerce_sale( $product );
		}

		return '';
	}
}

if ( ! function_exists( 'greenpath_woo_get_woocommerce_sale' ) ) {
	/**
	 * Function that return sale mark label
	 *
	 * @param object $product
	 *
	 * @return string
	 */
	function greenpath_woo_get_woocommerce_sale( $product ) {
		$enable_percent_mark = greenpath_get_post_value_through_levels( 'qodef_woo_enable_percent_sign_value' );
		$price               = floatval( $product->get_regular_price() );
		$sale_price          = floatval( $product->get_sale_price() );

		if ( $price > 0 && 'yes' === $enable_percent_mark ) {
			$sale_label = '-' . ( 100 - round( ( $sale_price * 100 ) / $price ) ) . '%';
		} else {
			$sale_label = esc_html__( 'Sale', 'nuttergood' );
		}

		return '<span class="qodef-woo-product-mark qodef-woo-onsale">' . $sale_label . '</span>';
	}
}

if ( ! function_exists( 'greenpath_add_out_of_stock_mark_on_product' ) ) {
	/**
	 * Function for adding out of stock template for product
	 */
	function greenpath_add_out_of_stock_mark_on_product() {
		$product = greenpath_woo_get_global_product();

		if ( ! empty( $product ) && ! $product->is_in_stock() ) {
			echo greenpath_get_out_of_stock_mark(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

if ( ! function_exists( 'greenpath_get_out_of_stock_mark' ) ) {
	/**
	 * Function for adding out of stock template for product
	 *
	 * @return string
	 */
	function greenpath_get_out_of_stock_mark() {
		return '<span class="qodef-woo-product-mark qodef-out-of-stock">' . esc_html__( 'Sold', 'nuttergood' ) . '</span>';
	}
}

if ( ! function_exists( 'greenpath_add_short_description_on_product' ) ) {
	/**
	 * Function for adding out of stock template for product
	 */
	function greenpath_add_short_description_on_product() {
		$product = greenpath_woo_get_global_product();

		if ( ! empty( $product ) && $product->get_id() !== '' ) {
			echo greenpath_get_short_description( $product->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

if ( ! function_exists( 'greenpath_get_short_description' ) ) {
	/**
	 * Function for adding out of stock template for product
	 *
	 * @return string
	 */
	function greenpath_get_short_description( $product_id ) {
		$excerpt = get_post_meta( $product_id, 'qodef_product_short_description', true );

		if ( ! empty( $excerpt ) ) {
			return '<p itemprop="description" class="qodef-woo-product-excerpt">' . esc_html( $excerpt ) . '</p>';
		}

		return false;
	}
}

if ( ! function_exists( 'greenpath_add_new_mark_on_product' ) ) {
	/**
	 * Function for adding out of stock template for product
	 */
	function greenpath_add_new_mark_on_product() {
		$product = greenpath_woo_get_global_product();

		if ( ! empty( $product ) && $product->get_id() !== '' ) {
			echo greenpath_get_new_mark( $product->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

if ( ! function_exists( 'greenpath_get_new_mark' ) ) {
	/**
	 * Function for adding out of stock template for product
	 *
	 * @param int $product_id
	 *
	 * @return string
	 */
	function greenpath_get_new_mark( $product_id ) {
		$option = get_post_meta( $product_id, 'qodef_show_new_sign', true );

		if ( 'yes' === $option ) {
			return '<span class="qodef-woo-product-mark qodef-new">' . esc_html__( 'New', 'nuttergood' ) . '</span>';
		}

		return false;
	}
}

if ( ! function_exists( 'greenpath_add_price_per_unit_on_product' ) ) {
	/**
	 * Function for adding out of stock template for product
	 */
	function greenpath_add_price_per_unit_on_product() {
		$product = greenpath_woo_get_global_product();

		if ( ! empty( $product ) && $product->get_id() !== '' ) {
			echo greenpath_get_price_per_unit( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

if ( ! function_exists( 'greenpath_get_price_per_unit' ) ) {
	/**
	 * Function for adding out of stock template for product
	 *
	 * @param object $product
	 *
	 * @return string
	 */
	function greenpath_get_price_per_unit( $product ) {
		$option = get_post_meta( $product->get_id(), 'qodef_show_price_per_unit', true );
		$weight = $product->get_weight();

		if ( 'yes' === $option && ! empty( $weight ) ) {
			$weight_html  = '';
			$weight_num   = floatval( $weight );
			$price_num    = floatval( $product->get_price() );
			$weight_html .= '<span class="qodef-price-per-unit">(';
			$weight_html .= $weight_num > 1 ? $price_num / $weight_num : $price_num / $weight_num;
			$weight_html .= '/' . get_option( 'woocommerce_weight_unit' ) . ')</span>';

			return $weight_html;
		}

		return false;
	}
}

if ( ! function_exists( 'greenpath_woo_shop_loop_item_title' ) ) {
	/**
	 * Function that override product list item title template
	 */
	function greenpath_woo_shop_loop_item_title() {
		$option    = greenpath_get_post_value_through_levels( 'qodef_woo_product_list_title_tag' );
		$title_tag = ! empty( $option ) ? esc_attr( $option ) : 'h6';

		echo '<' . greenpath_escape_title_tag( $title_tag ) . ' class="qodef-woo-product-title woocommerce-loop-product__title">';
		do_action( 'qodef_woo_product_list_title_tag_link_open' );
		echo wp_kses_post( get_the_title() );
		do_action( 'qodef_woo_product_list_title_tag_link_close' );
		echo '</' . greenpath_escape_title_tag( $title_tag ) . '>';
	}
}

if ( ! function_exists( 'greenpath_woo_template_single_title' ) ) {
	/**
	 * Function that override product single item title template
	 */
	function greenpath_woo_template_single_title() {
		$option    = greenpath_get_post_value_through_levels( 'qodef_woo_single_title_tag' );
		$title_tag = ! empty( $option ) ? esc_attr( $option ) : 'h1';

		$title_classes[] = 'qodef-woo-product-title';
		$title_classes[] = 'product_title';
		$title_classes[] = 'entry-title';
		$title_classes[] = ! empty( $option ) ? '' : 'qodef--default-title';
		$title_classes   = implode( ' ', $title_classes );

		echo '<' . greenpath_escape_title_tag( $title_tag ) . ' class="' . esc_attr( $title_classes ) . '">' . wp_kses_post( get_the_title() ) . '</' . greenpath_escape_title_tag( $title_tag ) . '>';
	}
}

if ( ! function_exists( 'greenpath_woo_single_thumbnail_images_columns' ) ) {
	/**
	 * Function that set number of columns for thumbnail images on single product page
	 *
	 * @param int $columns
	 *
	 * @return int
	 */
	function greenpath_woo_single_thumbnail_images_columns( $columns ) {
		$option = greenpath_get_post_value_through_levels( 'qodef_woo_single_thumbnail_images_columns' );

		if ( ! empty( $option ) ) {
			$columns = intval( $option );
		}

		return $columns;
	}
}

if ( ! function_exists( 'greenpath_woo_single_thumbnail_images_size' ) ) {
	/**
	 * Function that set thumbnail images size on single product page
	 *
	 * @return string
	 */
	function greenpath_woo_single_thumbnail_images_size() {
		return apply_filters( 'greenpath_filter_woo_single_thumbnail_size', 'woocommerce_thumbnail' );
	}
}

if ( ! function_exists( 'greenpath_woo_single_thumbnail_images_wrapper' ) ) {
	/**
	 * Function that add additional wrapper around thumbnail images on single product
	 */
	function greenpath_woo_single_thumbnail_images_wrapper() {
		echo '<div class="qodef-woo-thumbnails-wrapper">';
	}
}

if ( ! function_exists( 'greenpath_woo_single_thumbnail_images_wrapper_end' ) ) {
	/**
	 * Function that add additional wrapper around thumbnail images on single product
	 */
	function greenpath_woo_single_thumbnail_images_wrapper_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'greenpath_woo_single_related_product_list_columns' ) ) {
	/**
	 * Function that set number of columns for related product list on single product page
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	function greenpath_woo_single_related_product_list_columns( $args ) {
		$option = greenpath_get_post_value_through_levels( 'qodef_woo_single_related_product_list_columns' );

		if ( ! empty( $option ) ) {
			$args['posts_per_page'] = intval( $option );
			$args['columns']        = intval( $option );
		}

		return $args;
	}
}

if ( ! function_exists( 'greenpath_woo_product_get_rating_html' ) ) {
	/**
	 * Function that override ratings templates
	 *
	 * @param string $html - contains html content
	 * @param float  $rating
	 *
	 * @return string
	 */
	function greenpath_woo_product_get_rating_html( $html, $rating ) {
		if ( ! empty( $rating ) ) {
			$average = is_product() ? '' : '<span class="qodef-rating-average">' . $rating . '</span>';

			$html  = '<div class="qodef-woo-ratings qodef-m"><div class="qodef-m-inner">';
			$html .= '<div class="qodef-m-star qodef--initial">';
			for ( $i = 0; $i < 5; $i ++ ) {
				$html .= greenpath_get_svg_icon( 'star', 'qodef-m-star-item' );
			}
			$html .= '</div>';
			$html .= '<div class="qodef-m-star qodef--active" style="width:' . ( ( $rating / 5 ) * 100 ) . '%">';
			for ( $i = 0; $i < 5; $i ++ ) {
				$html .= greenpath_get_svg_icon( 'star', 'qodef-m-star-item' );
			}
			$html .= '</div>';
			$html .= '</div>' . $average . '</div>';
		}

		return $html;
	}
}

if ( ! function_exists( 'greenpath_woo_get_product_search_form' ) ) {
	/**
	 * Function that override product search widget form
	 *
	 * @return string which contains html content
	 */
	function greenpath_woo_get_product_search_form() {
		return greenpath_get_template_part( 'woocommerce', 'templates/product-searchform' );
	}
}

if ( ! function_exists( 'greenpath_woo_get_content_widget_product' ) ) {
	/**
	 * Function that override product content widget
	 *
	 * @param string $located
	 * @param string $template_name
	 *
	 * @return string which contains html content
	 */
	function greenpath_woo_get_content_widget_product( $located, $template_name ) {

		if ( 'content-widget-product.php' === $template_name && file_exists( GREENPATH_INC_ROOT_DIR . '/woocommerce/templates/content-widget-product.php' ) ) {
			$located = GREENPATH_INC_ROOT_DIR . '/woocommerce/templates/content-widget-product.php';
		}

		return $located;
	}
}

if ( ! function_exists( 'greenpath_woo_get_quantity_input' ) ) {
	/**
	 * Function that override quantity input
	 *
	 * @param string $located
	 * @param string $template_name
	 *
	 * @return string which contains html content
	 */
	function greenpath_woo_get_quantity_input( $located, $template_name ) {

		if ( 'global/quantity-input.php' === $template_name && file_exists( GREENPATH_INC_ROOT_DIR . '/woocommerce/templates/global/quantity-input.php' ) ) {
			$located = GREENPATH_INC_ROOT_DIR . '/woocommerce/templates/global/quantity-input.php';
		}

		return $located;
	}
}

if ( ! function_exists( 'greenpath_woo_get_single_product_meta' ) ) {
	/**
	 * Function that override single product meta
	 *
	 * @param string $located
	 * @param string $template_name
	 *
	 * @return string which contains html content
	 */
	function greenpath_woo_get_single_product_meta( $located, $template_name ) {

		if ( 'single-product/meta.php' === $template_name && file_exists( GREENPATH_INC_ROOT_DIR . '/woocommerce/templates/single-product/meta.php' ) ) {
			$located = GREENPATH_INC_ROOT_DIR . '/woocommerce/templates/single-product/meta.php';
		}

		return $located;
	}
}

if ( ! function_exists( 'greenpath_woo_add_search_widget_icon' ) ) {
	/**
	 * Function that add search icon into global js object
	 *
	 * @param $array
	 *
	 * @return mixed
	 */
	function greenpath_woo_add_search_widget_icon( $array ) {
		$array['iconSearch'] = greenpath_get_svg_icon( 'search' );

		return $array;
	}

	add_filter( 'greenpath_filter_localize_main_js', 'greenpath_woo_add_search_widget_icon' );
}

if ( ! function_exists( 'greenpath_woo_get_single_product_buy_now_button' ) ) {
	/**
	 * Function that adds Buy Now button
	 */
	function greenpath_woo_get_single_product_buy_now_button() {
		if( false === Greenpath_WooCommerce::get_instance()->buy_now_added ) {
			$product = greenpath_woo_get_global_product();

			if ( file_exists( GREENPATH_INC_ROOT_DIR . '/woocommerce/templates/single-product/buy-now.php' ) && 'external' !== $product->get_type() ) {
				echo greenpath_get_template_part( 'woocommerce', 'templates/single-product/buy-now' );
			}

			Greenpath_WooCommerce::get_instance()->buy_now_added = true;
		}
	}
}
