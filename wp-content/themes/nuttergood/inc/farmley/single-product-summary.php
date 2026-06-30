<?php
/**
 * Farmley single product summary — weights, sharing, and product tabs.
 */

if ( ! function_exists( 'nuttergood_farmley_setup_single_product_summary' ) ) {
	function nuttergood_farmley_setup_single_product_summary() {
		if ( ! nuttergood_farmley_is_single_product_page() ) {
			return;
		}

		add_action( 'woocommerce_single_product_summary', 'nuttergood_farmley_render_single_weight_row', 9 );
		add_action( 'woocommerce_single_product_summary', 'nuttergood_farmley_render_product_sku', 11 );
		add_action( 'woocommerce_single_product_summary', 'nuttergood_farmley_render_product_share', 38 );
		add_filter( 'woocommerce_product_tabs', 'nuttergood_farmley_customize_product_tabs', 60 );

		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		remove_action( 'woocommerce_share', 'greenpath_woo_product_render_social_share_html' );
	}

	add_action( 'wp', 'nuttergood_farmley_setup_single_product_summary', 25 );
}

if ( ! function_exists( 'nuttergood_farmley_render_product_sku' ) ) {
	/**
	 * Product SKU below price (uses product ID when SKU is not set).
	 */
	function nuttergood_farmley_render_product_sku() {
		global $product;

		if ( ! $product instanceof WC_Product || ! wc_product_sku_enabled() ) {
			return;
		}

		$sku = $product->get_sku();

		if ( '' === $sku && ! $product->is_type( 'variable' ) ) {
			$sku = (string) $product->get_id();
		}

		if ( '' === $sku ) {
			$sku = esc_html__( 'N/A', 'nuttergood' );
		}

		echo '<div class="ng-farmley-sp-sku">';
		echo '<span class="ng-farmley-sp-sku__label">' . esc_html__( 'SKU:', 'nuttergood' ) . '</span>';
		echo '<span class="ng-farmley-sp-sku__value sku">' . esc_html( $sku ) . '</span>';
		echo '</div>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_single_weight_row' ) ) {
	function nuttergood_farmley_render_single_weight_row() {
		global $product;

		if ( ! $product instanceof WC_Product || $product->is_type( 'variable' ) ) {
			return;
		}

		$sizes = nuttergood_farmley_get_product_size_options( $product );
		if ( empty( $sizes ) ) {
			return;
		}

		$multi = count( $sizes ) > 1;

		echo '<div class="ng-farmley-sp-weight" data-product-id="' . esc_attr( (string) $product->get_id() ) . '">';
		echo '<span class="ng-farmley-sp-weight__label">' . esc_html__( 'Size', 'nuttergood' ) . '</span>';

		printf(
			'<div class="ng-farmley-card-weight ng-farmley-sp-weight__options%1$s" role="%2$s" aria-label="%3$s">',
			$multi ? ' ng-farmley-card-weight--multi' : '',
			$multi ? 'listbox' : 'presentation',
			esc_attr__( 'Weight options', 'nuttergood' )
		);
		echo '<div class="ng-farmley-card-weight__options">';

		foreach ( $sizes as $idx => $size ) {
			if ( ! is_array( $size ) ) {
				continue;
			}

			$label          = nuttergood_farmley_format_weight_label( $size['weight'] ?? $size['label'] ?? '' );
			$badge_discount = nuttergood_farmley_get_badge_discount_percent( $product, $size );
			$img_id         = (int) ( $size['image_id'] ?? 0 );
			$img_src        = $img_id ? wp_get_attachment_image_url( $img_id, 'woocommerce_single' ) : '';

			if ( '' === $label ) {
				continue;
			}

			if ( $multi ) {
				printf(
					'<button type="button" class="ng-farmley-card-weight__btn ng-farmley-sp-weight__btn%1$s" role="option" data-index="%2$d" data-price="%3$s" data-mrp="%4$s" data-regular="%5$s" data-discount="%6$d" data-image="%7$s" aria-selected="%8$s"><span class="ng-farmley-card-weight__text">%9$s</span></button>',
					0 === $idx ? ' is-active' : '',
					(int) $idx,
					esc_attr( $size['price'] ?? '' ),
					esc_attr( $size['mrp'] ?? $size['regular_price'] ?? '' ),
					esc_attr( $size['regular_price'] ?? '' ),
					(int) $badge_discount,
					esc_url( $img_src ? $img_src : '' ),
					0 === $idx ? 'true' : 'false',
					esc_html( $label )
				);
			} else {
				printf(
					'<span class="ng-farmley-card-weight__badge ng-farmley-sp-weight__badge"><span class="ng-farmley-card-weight__text">%s</span></span>',
					esc_html( $label )
				);
			}
		}

		echo '</div></div></div>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_product_share' ) ) {
	/**
	 * Styled share row with icon buttons (replaces plain text theme share).
	 */
	function nuttergood_farmley_render_product_share() {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$url       = rawurlencode( get_permalink( $product->get_id() ) );
		$title     = rawurlencode( wp_strip_all_tags( $product->get_name() ) );
		$text      = rawurlencode( wp_strip_all_tags( $product->get_name() . ' - Nutterly Good' ) );
		$instagram = 'https://www.instagram.com/';
		$facebook  = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
		$whatsapp  = 'https://api.whatsapp.com/send?text=' . $text . '%20' . $url;

		echo '<div class="ng-farmley-sp-share">';
		echo '<div class="ng-farmley-sp-share__inner">';
		echo '<span class="ng-farmley-sp-share__label">' . esc_html__( 'Share', 'nuttergood' ) . '</span>';
		echo '<div class="ng-farmley-sp-share__icons">';
		echo '<ul class="ng-farmley-sp-share__list">';
		printf(
			'<li><a class="ng-farmley-sp-share__link ng-farmley-sp-share__link--instagram" href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s">%3$s</a></li>',
			esc_url( $instagram ),
			esc_attr__( 'Open Instagram', 'nuttergood' ),
			nuttergood_farmley_footer_social_icon( 'instagram' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		printf(
			'<li><a class="ng-farmley-sp-share__link ng-farmley-sp-share__link--facebook" href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s">%3$s</a></li>',
			esc_url( $facebook ),
			esc_attr__( 'Share on Facebook', 'nuttergood' ),
			nuttergood_farmley_footer_social_icon( 'facebook' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		printf(
			'<li><a class="ng-farmley-sp-share__link ng-farmley-sp-share__link--whatsapp" href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s">%3$s</a></li>',
			esc_url( $whatsapp ),
			esc_attr__( 'Share on WhatsApp', 'nuttergood' ),
			nuttergood_farmley_footer_social_icon( 'whatsapp' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		echo '</ul>';
		echo '</div></div></div>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_customize_product_tabs' ) ) {
	/**
	 * @param array<string, array<string, mixed>> $tabs Product tabs.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	function nuttergood_farmley_customize_product_tabs( $tabs ) {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return $tabs;
		}

		if ( isset( $tabs['description'] ) ) {
			$tabs['description']['title']    = __( 'Description', 'nuttergood' );
			$tabs['description']['priority'] = 10;
			$tabs['description']['callback'] = 'nuttergood_farmley_render_description_tab';

			if ( ! nuttergood_farmley_product_has_description_tab_content( $product ) ) {
				unset( $tabs['description'] );
			}
		} elseif ( nuttergood_farmley_product_has_description_tab_content( $product ) ) {
			$tabs['description'] = array(
				'title'    => __( 'Description', 'nuttergood' ),
				'priority' => 10,
				'callback' => 'nuttergood_farmley_render_description_tab',
			);
		}

		if ( isset( $tabs['additional_information'] ) ) {
			$has_details = ! empty( nuttergood_farmley_get_additional_detail_values( $product ) );
			if ( $has_details ) {
				$tabs['additional_information']['title']    = __( 'Additional Details', 'nuttergood' );
				$tabs['additional_information']['priority'] = 20;
				$tabs['additional_information']['callback']   = 'nuttergood_farmley_render_additional_details_panel';
			} else {
				unset( $tabs['additional_information'] );
			}
		}

		unset( $tabs['reviews'] );

		return $tabs;
	}
}
