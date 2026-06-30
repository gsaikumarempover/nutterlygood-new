<?php

if ( ! function_exists( 'greenpath_core_get_mobile_header_logo_image' ) ) {
	/**
	 * Function that return logo image html for current module
	 *
	 * @return string that contains html content
	 */
	function greenpath_core_get_mobile_header_logo_image() {
		$logo_height     = greenpath_core_get_post_value_through_levels( 'qodef_mobile_logo_height' );
		$logo_source     = greenpath_core_get_post_value_through_levels( 'qodef_mobile_logo_source' );
		$main_logo_id    = greenpath_core_get_post_value_through_levels( 'qodef_mobile_logo_main' );
		$logo_svg_path   = greenpath_core_get_post_value_through_levels( 'qodef_mobile_logo_svg_path' );
		$logo_text       = greenpath_core_get_post_value_through_levels( 'qodef_mobile_logo_text' );
		$customizer_logo = greenpath_core_get_customizer_logo();

		$logo_classes = array();
		if ( ! empty( $logo_height ) ) {
			$logo_classes[] = 'qodef-height--set';
		} else {
			$logo_classes[] = 'qodef-height--not-set';
		}

		if ( ! empty( $logo_source ) ) {
			$logo_classes[] = 'qodef-source--' . esc_attr( $logo_source );
		}

		$parameters = array(
			'logo_classes' => implode( ' ', $logo_classes ),
		);

		$available_logos = apply_filters(
			'greenpath_core_filter_available_mobile_header_logo_images',
			array(
				'main' => 'main',
			),
			$parameters
		);

		$logo_html  = array();
		$is_enabled = false;

		if ( 'svg-path' === $logo_source && ! empty( $logo_svg_path ) ) {
			$logo_html['logo_main_image'] = apply_filters( 'greenpath_core_filter_mobile_header_logo_svg_path', $logo_svg_path, $parameters );

			$is_enabled = true;
		} elseif ( 'textual' === $logo_source && ! empty( $logo_text ) ) {
			$logo_html['logo_main_image'] = esc_html( apply_filters( 'greenpath_core_filter_mobile_header_logo_textual', $logo_text, $parameters ) );

			$is_enabled = true;
		} else {
			foreach ( $available_logos as $logo_key => $option_value ) {
				$logo_html[ 'logo_' . $logo_key . '_image' ] = '';

				$logo_image_id = greenpath_core_get_post_value_through_levels( 'qodef_mobile_logo_' . $option_value );

				// Check if logo image is set, if not set main mobile logo image as default
				if ( ! empty( $main_logo_id ) && ( ( 'main' === $logo_key && empty( $logo_image_id ) ) || empty( $logo_image_id ) ) ) {
					$logo_image_id = $main_logo_id;
				}

				if ( ! empty( $logo_image_id ) ) {
					$logo_image_attr = array(
						'class'    => 'qodef-header-logo-image qodef--' . str_replace( '_', '-', $logo_key ),
						'itemprop' => 'image',
						'alt'      => sprintf( esc_attr__( 'logo %s', 'greenpath-core' ), str_replace( '_', ' ', $logo_key ) ),
					);

					$image      = wp_get_attachment_image( $logo_image_id, 'full', false, $logo_image_attr );
					$image_html = ! empty( $image ) ? $image : qode_framework_get_image_html_from_src( $logo_image_id, $logo_image_attr );

					$logo_html[ 'logo_' . $logo_key . '_image' ] = qode_framework_wp_kses_html( 'img', $image_html );

					$is_enabled = true;
				}
			}

			if ( ! empty( $customizer_logo ) ) {
				$logo_html['logo_main_image'] = $customizer_logo;
			}
		}

		$parameters['logo_image'] = implode( '', apply_filters( 'greenpath_core_filter_mobile_header_logo_image_html', $logo_html, $parameters ) );

		if ( $is_enabled ) {
			echo apply_filters( 'greenpath_core_filter_get_mobile_header_logo_image', greenpath_core_get_template_part( 'mobile-header/templates', 'parts/mobile-logo', '', $parameters ), $parameters, $logo_html );
		}
	}
}

if ( ! function_exists( 'greenpath_core_set_mobile_header_logo_styles' ) ) {
	/**
	 * Function that generates module inline styles
	 *
	 * @param string $style
	 *
	 * @return string
	 */
	function greenpath_core_set_mobile_header_logo_styles( $style ) {
		$logo_styles  = array();
		$logo_height  = greenpath_core_get_post_value_through_levels( 'qodef_mobile_logo_height' );
		$logo_padding = greenpath_core_get_post_value_through_levels( 'qodef_mobile_logo_padding' );

		if ( ! empty( $logo_height ) ) {
			$logo_styles['height'] = intval( $logo_height ) . 'px';
		}

		if ( ! empty( $logo_padding ) ) {
			$logo_styles['padding'] = esc_attr( $logo_padding );
		}

		if ( ! empty( $logo_styles ) ) {
			$style .= qode_framework_dynamic_style(
				array(
					'#qodef-page-mobile-header .qodef-mobile-header-logo-link',
				),
				$logo_styles
			);
		}

		// Logo SVG Source
		$svg_styles     = array();
		$svg_icon_color = greenpath_core_get_post_value_through_levels( 'qodef_mobile_logo_svg_path_color' );

		if ( ! empty( $svg_icon_color ) ) {
			$svg_styles['color'] = $svg_icon_color;
		}

		if ( ! empty( $svg_styles ) ) {
			$style .= qode_framework_dynamic_style( '#qodef-page-mobile-header .qodef-mobile-header-logo-link.qodef-source--svg-path', $svg_styles );
		}

		$svg_icon_styles = array();
		$svg_icon_size   = greenpath_core_get_post_value_through_levels( 'qodef_mobile_logo_svg_path_size' );

		if ( ! empty( $svg_icon_size ) ) {
			if ( qode_framework_string_ends_with_typography_units( $svg_icon_size ) ) {
				$svg_icon_styles['width'] = $svg_icon_size;
			} else {
				$svg_icon_styles['width'] = intval( $svg_icon_size ) . 'px';
			}
		}

		if ( ! empty( $svg_icon_styles ) ) {
			$style .= qode_framework_dynamic_style( '#qodef-page-mobile-header .qodef-mobile-header-logo-link.qodef-source--svg-path svg', $svg_icon_styles );
		}

		$svg_hover_styles     = array();
		$svg_icon_hover_color = greenpath_core_get_post_value_through_levels( 'qodef_mobile_logo_svg_path_hover_color' );

		if ( ! empty( $svg_icon_hover_color ) ) {
			$svg_hover_styles['color'] = $svg_icon_hover_color;
		}

		if ( ! empty( $svg_hover_styles ) ) {
			$style .= qode_framework_dynamic_style(
				array(
					'#qodef-page-mobile-header .qodef-mobile-header-logo-link.qodef-source--svg-path:hover',
				),
				$svg_hover_styles
			);
		}

		// Logo Textual Source
		$textual_styles       = greenpath_core_get_typography_styles( 'qodef_mobile_logo_text', '', null );
		$textual_hover_styles = greenpath_core_get_typography_hover_styles( 'qodef_mobile_logo_text', null );

		if ( ! empty( $textual_styles ) ) {
			$style .= qode_framework_dynamic_style( '#qodef-page-mobile-header .qodef-mobile-header-logo-link.qodef-source--textual', $textual_styles );
		}

		if ( ! empty( $textual_hover_styles ) ) {
			$style .= qode_framework_dynamic_style( '#qodef-page-mobile-header .qodef-mobile-header-logo-link.qodef-source--textual:hover', $textual_hover_styles );
		}

		return $style;
	}

	add_filter( 'greenpath_filter_add_inline_style', 'greenpath_core_set_mobile_header_logo_styles' );
}

if ( ! function_exists( 'greenpath_core_set_predefined_icon_before_mobile_logo' ) ) {
	function greenpath_core_set_predefined_icon_before_mobile_logo() {
		$predefined_icon_mobile = greenpath_core_get_post_value_through_levels( 'qodef_mobile_logo_predefined_icon' );

		if ( 'yes' === $predefined_icon_mobile ) {
			echo '<span class="qodef--predefined-logo-icon">' . greenpath_get_svg_icon( 'logo-lemon' ) . '</span>';
		}
	}

	add_action( 'greenpath_core_before_mobile_header_logo_image', 'greenpath_core_set_predefined_icon_before_mobile_logo' );
}
