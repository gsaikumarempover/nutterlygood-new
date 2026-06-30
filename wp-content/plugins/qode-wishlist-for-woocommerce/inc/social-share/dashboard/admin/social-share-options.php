<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_add_social_share_options' ) ) {
	/**
	 * Function that add share options for this module
	 */
	function qode_wishlist_for_woocommerce_add_social_share_options( $page, $wishlist_page_features_section ) {

		if ( $page ) {
			$social_networks       = qode_wishlist_for_woocommerce_social_networks_list();
			$social_networks_array = array();
			foreach ( $social_networks as $network => $params ) {
				$social_networks_array[ $network ] = $params['label'];
			}

			$wishlist_page_features_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qode_wishlist_for_woocommerce_enable_share',
					'title'         => esc_html__( 'Enable Wishlist Share', 'qode-wishlist-for-woocommerce' ),
					'description'   => esc_html__( 'Allow users to share their Wishlists on social media', 'qode-wishlist-for-woocommerce' ),
					'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_enable_share_default_value', 'yes' ),
				)
			);

			$social_section = $wishlist_page_features_section->add_section_element(
				array(
					'name'       => 'qode_wishlist_for_woocommerce_social_share_section',
					'dependency' => array(
						'show' => array(
							'qode_wishlist_for_woocommerce_enable_share' => array(
								'values'        => 'yes',
								'default_value' => apply_filters( 'qode_wishlist_for_woocommerce_filter_enable_share_default_value', 'yes' ),
							),
						),
					),
				)
			);

			$social_section->add_field_element(
				array(
					'field_type'    => 'checkbox',
					'name'          => 'qode_wishlist_for_woocommerce_social_share_networks',
					'title'         => esc_html__( 'Select Networks for Wishlist Sharing', 'qode-wishlist-for-woocommerce' ),
					'options'       => $social_networks_array,
					'default_value' => array_keys( $social_networks_array ),
				)
			);

			$social_section->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_facebook_app_id',
					'title'      => esc_html__( 'Facebook APP ID', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'custom_class' => 'qodef-no-indent',
					),
					'dependency' => array(
						'show' => array(
							'qode_wishlist_for_woocommerce_social_share_networks' => array(
								'values'        => 'facebook',
								'default_value' => array_keys( $social_networks_array ),
							),
						),
					),
				)
			);

			$social_section->add_field_element(
				array(
					'field_type'    => 'text',
					'name'          => 'qode_wishlist_for_woocommerce_twitter_text',
					'title'         => esc_html__( 'X Text', 'qode-wishlist-for-woocommerce' ),
					'default_value' => esc_html__( '@QodeWishlist', 'qode-wishlist-for-woocommerce' ),
					'args'          => array(
						'custom_class' => 'qodef-no-indent',
					),
					'dependency'    => array(
						'show' => array(
							'qode_wishlist_for_woocommerce_social_share_networks' => array(
								'values'        => 'twitter',
								'default_value' => array_keys( $social_networks_array ),
							),
						),
					),
				)
			);

			// Hook to include additional options after module options.
			do_action( 'qode_wishlist_for_woocommerce_action_after_wishlist_page_social_options_map', $wishlist_page_features_section, $social_section );
		}
	}

	add_action( 'qode_wishlist_for_woocommerce_action_after_wishlist_page_features_options_map', 'qode_wishlist_for_woocommerce_add_social_share_options', 50, 2 );
}
