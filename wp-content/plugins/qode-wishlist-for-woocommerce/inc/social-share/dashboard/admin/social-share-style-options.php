<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_social_share_style_options' ) ) {
	/**
	 * Function that add share options for this module
	 */
	function qode_wishlist_for_woocommerce_social_share_style_options( $page, $styles_tab ) {

		if ( $page ) {
			$social_networks = qode_wishlist_for_woocommerce_social_networks_list();

			$social_section = $styles_tab->add_section_element(
				array(
					'name'        => 'qode_wishlist_for_woocommerce_social_share_style_section',
					'title'       => esc_html__( 'Social Share Style', 'qode-wishlist-for-woocommerce' ),
					'description' => esc_html__( 'Make general stylistic adjustments to social share elements', 'qode-wishlist-for-woocommerce' ),
					'args'        => array(
						'custom_class' => 'qodef-no-indent',
					),
					'dependency'  => array(
						'show' => array(
							'qode_wishlist_for_woocommerce_enable_share' => array(
								'values'        => 'yes',
								'default_value' => 'yes',
							),
						),
					),
				)
			);

			$general_style_row = $social_section->add_row_element(
				array(
					'name' => 'qode_wishlist_for_woocommerce_social_share_general_style_row',
				)
			);

			$general_style_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_social_share_color',
					'title'      => esc_html__( 'Opener', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 3,
						'placeholder' => qode_wishlist_for_woocommerce_is_installed( 'wishlist-premium' ) ? '#000000' : '',
					),
				)
			);

			$general_style_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_social_share_hover_color',
					'title'      => esc_html__( 'Opener Hover', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 3,
						'placeholder' => qode_wishlist_for_woocommerce_is_installed( 'wishlist-premium' ) ? '#ec274f' : '',
					),
				)
			);

			$general_style_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_social_share_icon_color',
					'title'      => esc_html__( 'Social Icon', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 3,
						'placeholder' => '#000000',
					),
				)
			);

			$general_style_row->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qode_wishlist_for_woocommerce_social_share_icon_hover_color',
					'title'      => esc_html__( 'Social Icon Hover', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 3,
						'placeholder' => '#ec274f',
					),
				)
			);

			$general_style_row->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qode_wishlist_for_woocommerce_social_share_size',
					'title'      => esc_html__( 'Social Icon Size', 'qode-wishlist-for-woocommerce' ),
					'args'       => array(
						'col_width'   => 3,
						'placeholder' => '15',
						'suffix'      => 'px',
					),
				)
			);

			foreach ( $social_networks as $network => $params ) {

				$social_item_section = $styles_tab->add_section_element(
					array(
						'name'        => 'qode_wishlist_for_woocommerce_social_share_' . esc_attr( $network ) . '_section',
						// translators: %s - Social Label.
						'title'       => sprintf( esc_html__( '%s Custom Style', 'qode-wishlist-for-woocommerce' ), $params['label'] ),
						// translators: %s - Social Label.
						'description' => sprintf( esc_html__( 'Make custom stylistic adjustments for %s sharing', 'qode-wishlist-for-woocommerce' ), $params['label'] ),
						'args'        => array(
							'custom_class' => 'qodef-no-indent',
						),
						'dependency'  => array(
							'show' => array(
								'qode_wishlist_for_woocommerce_social_share_networks' => array(
									'values'        => $network,
									'default_value' => array_keys( $social_networks ),
								),
							),
						),
					)
				);

				$social_item_section->add_field_element(
					array(
						'field_type'    => 'radio',
						'name'          => 'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_icon',
						// translators: %s - Social Label.
						'title'         => sprintf( esc_html__( '%s Icon', 'qode-wishlist-for-woocommerce' ), $params['label'] ),
						// translators: %s - Social Label.
						'description'   => sprintf( esc_html__( 'Choose an icon type for %s sharing', 'qode-wishlist-for-woocommerce' ), $params['label'] ),
						'options'       => array(
							'predefined'  => esc_html__( 'Predefined', 'qode-wishlist-for-woocommerce' ),
							'custom-icon' => esc_html__( 'Custom Icon', 'qode-wishlist-for-woocommerce' ),
						),
						'default_value' => 'predefined',
					)
				);

				$social_item_section->add_field_element(
					array(
						'field_type'  => 'image',
						'name'        => 'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_custom_icon',
						// translators: %s - Social Label.
						'title'       => sprintf( esc_html__( '%s Custom Icon', 'qode-wishlist-for-woocommerce' ), $params['label'] ),
						'description' => esc_html__( 'Set a custom icon for your social share element', 'qode-wishlist-for-woocommerce' ),
						'dependency'  => array(
							'show' => array(
								'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_icon' => array(
									'values'        => 'custom-icon',
									'default_value' => 'predefined',
								),
							),
						),
					)
				);

				$social_item_row_1 = $social_item_section->add_row_element(
					array(
						'name' => 'qode_wishlist_for_woocommerce_social_share_' . esc_attr( $network ) . '_row_1',
					)
				);

				$social_item_row_1->add_field_element(
					array(
						'field_type' => 'color',
						'name'       => 'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_fill_color',
						'title'      => esc_html__( 'Fill', 'qode-wishlist-for-woocommerce' ),
						'args'       => array(
							'col_width' => 3,
						),
					)
				);

				$social_item_row_1->add_field_element(
					array(
						'field_type' => 'color',
						'name'       => 'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_fill_hover_color',
						'title'      => esc_html__( 'Fill Hover', 'qode-wishlist-for-woocommerce' ),
						'args'       => array(
							'col_width' => 3,
						),
					)
				);

				$social_item_row_1->add_field_element(
					array(
						'field_type' => 'color',
						'name'       => 'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_stroke_color',
						'title'      => esc_html__( 'Stroke', 'qode-wishlist-for-woocommerce' ),
						'args'       => array(
							'col_width' => 3,
						),
					)
				);

				$social_item_row_1->add_field_element(
					array(
						'field_type' => 'color',
						'name'       => 'qode_wishlist_for_woocommerce_' . esc_attr( $network ) . '_stroke_hover_color',
						'title'      => esc_html__( 'Stroke Hover', 'qode-wishlist-for-woocommerce' ),
						'args'       => array(
							'col_width' => 3,
						),
					)
				);
			}

			// Hook to include additional options after module options.
			do_action( 'qode_wishlist_for_woocommerce_action_after_wishlist_page_social_style_options_map', $styles_tab, $social_section );
		}
	}

	add_action( 'qode_wishlist_for_woocommerce_action_after_wishlist_page_style_options_map', 'qode_wishlist_for_woocommerce_social_share_style_options', 15, 2 );
}
