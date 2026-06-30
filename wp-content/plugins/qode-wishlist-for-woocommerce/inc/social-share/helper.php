<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_social_networks_list' ) ) {
	/**
	 * Function that returns array of social networks.
	 *
	 * @return array - list of social networks
	 */
	function qode_wishlist_for_woocommerce_social_networks_list() {
		$social_networks = array(
			'facebook'  => array(
				'label' => esc_html__( 'Facebook', 'qode-wishlist-for-woocommerce' ),
			),
			'twitter'   => array(
				'label' => esc_html__( 'X', 'qode-wishlist-for-woocommerce' ),
			),
			'linkedin'  => array(
				'label' => esc_html__( 'LinkedIn', 'qode-wishlist-for-woocommerce' ),
			),
			'pinterest' => array(
				'label' => esc_html__( 'Pinterest', 'qode-wishlist-for-woocommerce' ),
			),
			'tumblr'    => array(
				'label' => esc_html__( 'Tumblr', 'qode-wishlist-for-woocommerce' ),
			),
			'vk'        => array(
				'label' => esc_html__( 'VK', 'qode-wishlist-for-woocommerce' ),
			),
			'email'     => array(
				'label' => esc_html__( 'Email', 'qode-wishlist-for-woocommerce' ),
			),
			'whatsapp'  => array(
				'label' => esc_html__( 'WhatsApp', 'qode-wishlist-for-woocommerce' ),
			),
		);

		return apply_filters( 'qode_wishlist_for_woocommerce_filter_social_networks_list', $social_networks );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_enabled_social_networks_list' ) ) {
	/**
	 * Function that returns array of social networks.
	 *
	 * @return array - list of social networks
	 */
	function qode_wishlist_for_woocommerce_enabled_social_networks_list() {
		$social_networks        = qode_wishlist_for_woocommerce_social_networks_list();
		$social_networks_option = (array) qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_social_share_networks' );

		foreach ( $social_networks as $network => $label ) {

			if ( ! in_array( $network, $social_networks_option, true ) ) {
				unset( $social_networks[ $network ] );
			}
		}

		return $social_networks;
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_social_network_share_link' ) ) {
	/**
	 * Get share link for networks
	 *
	 * @param string $net
	 * @param string $wishlist_page_title
	 * @param string $wishlist_page_token
	 * @param string $wishlist_table_name
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_get_social_network_share_link( $net, $wishlist_page_title, $wishlist_page_token, $wishlist_table_name ) {
		$url_args            = array(
			'view'  => $wishlist_page_token,
			'table' => $wishlist_table_name,
		);
		$wishlist_page       = qode_wishlist_for_woocommerce_get_wishlist_page_url_with_args( $url_args );
		$wishlist_page_image = get_the_post_thumbnail_url( qode_wishlist_for_woocommerce_get_wishlist_page_id(), 'full' );
		$image               = ! empty( $wishlist_page_image ) ? $wishlist_page_image : '';

		switch ( $net ) {
			case 'facebook':
				$facebook_app_id = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_facebook_app_id' );

				if ( wp_is_mobile() ) {
					$link = 'window.open(\'https://m.facebook.com/dialog/share?app_id=' . $facebook_app_id . '&href=' . rawurlencode( $wishlist_page ) . '\');';
				} else {
					$link = 'window.open(\'https://www.facebook.com/dialog/share?app_id=' . $facebook_app_id . '&href=' . rawurlencode( $wishlist_page ) . '&menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width=620,height=280\');';
				}
				break;
			case 'twitter':
				$link = 'window.open(\'https://twitter.com/intent/tweet?text=' . rawurlencode( qode_wishlist_for_woocommerce_get_social_network_excerpt() . $wishlist_page ) . '\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');';
				break;
			case 'linkedin':
				$link = 'popUp=window.open(\'https://www.linkedin.com/sharing/share-offsite?url=' . rawurlencode( $wishlist_page ) . '&amp;title=' . rawurlencode( $wishlist_page_title ) . '\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');popUp.focus();return false;';
				break;
			case 'tumblr':
				$link = 'popUp=window.open(\'https://www.tumblr.com/share/link?url=' . rawurlencode( $wishlist_page ) . '&amp;name=' . rawurlencode( $wishlist_page_title ) . '\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');popUp.focus();return false;';
				break;
			case 'pinterest':
				$media = ( $image ) ? '&amp;media=' . rawurlencode( $image ) : '';
				$link  = 'popUp=window.open(\'https://pinterest.com/pin/create/button/?url=' . rawurlencode( $wishlist_page ) . '&amp;description=' . rawurlencode( $wishlist_page_title ) . $media . '\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');popUp.focus();return false;';
				break;
			case 'vk':
				$media = ( $image ) ? '&amp;image=' . rawurlencode( $image ) : '';
				$link  = 'popUp=window.open(\'https://vkontakte.ru/share.php?url=' . rawurlencode( $wishlist_page ) . '&amp;title=' . rawurlencode( $wishlist_page_title ) . $media . '\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');popUp.focus();return false;';
				break;
			case 'email':
				$link = 'mailto:?subject=' . esc_attr( $wishlist_page_title ) . '&amp;body=' . rawurlencode( $wishlist_page );
				break;
			case 'whatsapp':
				$link = 'https://web.whatsapp.com/send?text=' . esc_attr( $wishlist_page_title ) . ' - ' . rawurlencode( $wishlist_page );
				break;
			default:
				$link = '';
		}

		return apply_filters( 'qode_wishlist_for_woocommerce_filter_social_network_share_link', $link, $net, $image );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_social_network_icon' ) ) {
	/**
	 * Function that returns svg icons of social networks.
	 *
	 * @return string - social network icon
	 */
	function qode_wishlist_for_woocommerce_get_social_network_icon( $network ) {
		$network_icon_type   = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_icon' );
		$network_custom_icon = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_custom_icon' );

		$custom_icon = 'custom-icon' === $network_icon_type && ! empty( $network_custom_icon ) ? qode_wishlist_for_woocommerce_get_icon_html( $network_custom_icon ) : '';

		switch ( $network ) {
			case 'facebook':
				$icon_svg = ! empty( $custom_icon ) ? $custom_icon : qode_wishlist_for_woocommerce_get_svg_icon( 'facebook' );
				break;
			case 'twitter':
				$icon_svg = ! empty( $custom_icon ) ? $custom_icon : qode_wishlist_for_woocommerce_get_svg_icon( 'twitter' );
				break;
			case 'linkedin':
				$icon_svg = ! empty( $custom_icon ) ? $custom_icon : qode_wishlist_for_woocommerce_get_svg_icon( 'linkedin' );
				break;
			case 'pinterest':
				$icon_svg = ! empty( $custom_icon ) ? $custom_icon : qode_wishlist_for_woocommerce_get_svg_icon( 'pinterest' );
				break;
			case 'tumblr':
				$icon_svg = ! empty( $custom_icon ) ? $custom_icon : qode_wishlist_for_woocommerce_get_svg_icon( 'tumblr' );
				break;
			case 'vk':
				$icon_svg = ! empty( $custom_icon ) ? $custom_icon : qode_wishlist_for_woocommerce_get_svg_icon( 'vk' );
				break;
			case 'email':
				$icon_svg = ! empty( $custom_icon ) ? $custom_icon : qode_wishlist_for_woocommerce_get_svg_icon( 'email' );
				break;
			case 'whatsapp':
				$icon_svg = ! empty( $custom_icon ) ? $custom_icon : qode_wishlist_for_woocommerce_get_svg_icon( 'whatsapp' );
				break;
			default:
				$icon_svg = esc_html( $network );
				break;
		}

		return apply_filters( 'qode_wishlist_for_woocommerce_filter_social_network_icon', $icon_svg, $network );
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_get_social_network_excerpt' ) ) {
	/**
	 * Function that return meta text for social network sharing
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_get_social_network_excerpt() {
		$twitter_text_meta = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_twitter_text' );
		$excerpt           = ! empty( $twitter_text_meta ) ? esc_attr( $twitter_text_meta ) : '';
		$charlength        = 139 - ( is_ssl() ? 23 : 22 );

		if ( mb_strlen( $excerpt ) > $charlength ) {
			$subex   = mb_substr( $excerpt, 0, $charlength );
			$exwords = explode( ' ', $subex );
			$excut   = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );

			if ( $excut < 0 ) {
				return mb_substr( $subex, 0, $excut );
			} else {
				return $subex;
			}
		} else {
			return $excerpt;
		}
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_set_social_share_styles' ) ) {
	/**
	 * Function that generates module inline styles
	 *
	 * @param string $style
	 *
	 * @return string
	 */
	function qode_wishlist_for_woocommerce_set_social_share_styles( $style ) {
		$styles = array();

		$icon_color       = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_social_share_icon_color' );
		$icon_hover_color = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_social_share_icon_hover_color' );
		$color            = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_social_share_color' );
		$hover_color      = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_social_share_hover_color' );
		$icon_size        = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_social_share_size' );

		if ( ! empty( $color ) ) {
			$styles['--qwfw-wt-social-color'] = $color;
		}

		if ( ! empty( $hover_color ) ) {
			$styles['--qwfw-wt-social-hover-color'] = $hover_color;
		}

		if ( ! empty( $icon_color ) ) {
			$styles['--qwfw-wt-social-icon-color'] = $icon_color;
		}

		if ( ! empty( $icon_hover_color ) ) {
			$styles['--qwfw-wt-social-icon-hover-color'] = $icon_hover_color;
		}

		if ( ! empty( $icon_size ) ) {
			if ( qode_wishlist_for_woocommerce_string_ends_with_allowed_units( $icon_size ) ) {
				$styles['--qwfw-wt-social-size'] = $icon_size;
			} else {
				$styles['--qwfw-wt-social-size'] = (int) $icon_size . 'px';
			}
		}

		if ( ! empty( $styles ) ) {
			$style .= qode_wishlist_for_woocommerce_dynamic_style( '.qwfw-social-share', $styles );
		}

		$social_networks = qode_wishlist_for_woocommerce_social_networks_list();
		foreach ( $social_networks as $network => $params ) {
			$icon_styles = array();

			$fill_color   = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_fill_color' );
			$stroke_color = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_stroke_color' );

			if ( ! empty( $fill_color ) ) {
				$icon_styles['fill'] = $fill_color;
			}

			if ( ! empty( $stroke_color ) ) {
				$icon_styles['stroke'] = $stroke_color;
			}

			if ( ! empty( $icon_styles ) ) {
				$style .= qode_wishlist_for_woocommerce_dynamic_style( '.qwfw-social-share .qwfw-m-social-item.qwfw--' . esc_attr( $network ) . ' svg', $icon_styles );
			}

			$icon_hover_styles = array();

			$fill_hover_color   = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_fill_hover_color' );
			$stroke_hover_color = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_stroke_hover_color' );

			if ( ! empty( $fill_hover_color ) ) {
				$icon_hover_styles['fill'] = $fill_hover_color;
			}

			if ( ! empty( $stroke_hover_color ) ) {
				$icon_hover_styles['stroke'] = $stroke_hover_color;
			}

			if ( ! empty( $icon_hover_styles ) ) {
				$style .= qode_wishlist_for_woocommerce_dynamic_style( '.qwfw-social-share .qwfw-m-social-item.qwfw--' . esc_attr( $network ) . ' a:hover svg', $icon_hover_styles );
			}
		}

		return $style;
	}

	add_filter( 'qode_wishlist_for_woocommerce_filter_add_inline_style', 'qode_wishlist_for_woocommerce_set_social_share_styles' );
}
