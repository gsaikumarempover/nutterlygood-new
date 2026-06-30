<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_add_add_to_wishlist_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_add_add_to_wishlist_shortcode( $shortcodes ) {
		$shortcodes[] = 'Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Shortcode';

		return $shortcodes;
	}

	add_filter( 'qode_wishlist_for_woocommerce_filter_register_shortcodes', 'qode_wishlist_for_woocommerce_add_add_to_wishlist_shortcode' );
}

if ( class_exists( 'Qode_Wishlist_For_WooCommerce_Shortcode' ) ) {
	class Qode_Wishlist_For_WooCommerce_Add_To_Wishlist_Shortcode extends Qode_Wishlist_For_WooCommerce_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( QODE_WISHLIST_FOR_WOOCOMMERCE_INC_URL_PATH . '/wishlist/shortcodes/add-to-wishlist' );
			$this->set_base( 'qode_wishlist_for_woocommerce_add_to_wishlist' );
			$this->set_name( esc_html__( 'Add to Wishlist', 'qode-wishlist-for-woocommerce' ) );
			$this->set_description( esc_html__( 'Shortcode that displays add to wishlist button', 'qode-wishlist-for-woocommerce' ) );
			$this->set_category( esc_html__( 'QODE Wishlist for WooCommerce', 'qode-wishlist-for-woocommerce' ) );
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'qode-wishlist-for-woocommerce' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'item_id',
					'title'         => esc_html__( 'Product ID', 'qode-wishlist-for-woocommerce' ),
					'options'       => qode_wishlist_for_woocommerce_get_cpt_items(),
					'default_value' => '',
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'button_behavior',
					'title'         => esc_html__( '"Add to Wishlist" Behavior', 'qode-wishlist-for-woocommerce' ),
					'options'       => array(
						''       => esc_html__( 'Default', 'qode-wishlist-for-woocommerce' ),
						'add'    => esc_html__( 'Show "Add to wishlist" button', 'qode-wishlist-for-woocommerce' ),
						'view'   => esc_html__( 'Show "Browse wishlist" link', 'qode-wishlist-for-woocommerce' ),
						'remove' => esc_html__( 'Show "Remove from list" link', 'qode-wishlist-for-woocommerce' ),
					),
					'default_value' => '',
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'button_type',
					'title'         => esc_html__( '"Add to Wishlist" Type', 'qode-wishlist-for-woocommerce' ),
					'options'       => array(
						''                  => esc_html__( 'Default', 'qode-wishlist-for-woocommerce' ),
						'icon-with-text'    => esc_html__( 'Icon with Text', 'qode-wishlist-for-woocommerce' ),
						'icon'              => esc_html__( 'Only Icon', 'qode-wishlist-for-woocommerce' ),
						'icon-with-tooltip' => esc_html__( 'Icon with Tooltip', 'qode-wishlist-for-woocommerce' ),
						'text'              => esc_html__( 'Only Text', 'qode-wishlist-for-woocommerce' ),
					),
					'default_value' => '',
				)
			);

			// Set additional hook for 3rd party elements.
			do_action( 'qode_wishlist_for_woocommerce_action_add_to_wishlist_shortcode_options', $this );

			$this->set_option(
				array(
					'field_type'    => 'hidden',
					'name'          => 'prevent_click',
					'title'         => esc_html__( 'Prevent Link Behaviors', 'qode-wishlist-for-woocommerce' ),
					'options'       => qode_wishlist_for_woocommerce_get_select_type_options_pool( 'no_yes', false ),
					'default_value' => 'no',
				)
			);
			$this->set_option(
				array(
					'field_type' => 'hidden',
					'name'       => 'single_layout',
					'title'      => esc_html__( 'Is Single Layout', 'qode-wishlist-for-woocommerce' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'hidden',
					'name'       => 'show_on_cart',
					'title'      => esc_html__( 'Is Cart Page', 'qode-wishlist-for-woocommerce' ),
				)
			);
		}

		public static function call_shortcode( $params = array() ) {
			$html = qode_wishlist_for_woocommerce_call_shortcode( 'qode_wishlist_for_woocommerce_add_to_wishlist', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			global $product;
			$item_id = ! empty( $atts['item_id'] ) ? qode_wishlist_for_woocommerce_get_original_product_id( $atts['item_id'] ) : 0;

			if ( empty( $item_id ) && $product instanceof WC_Product ) {
				$item_id = $product->get_id();

				$atts['item_id'] = $item_id;
			} else {
				$product = wc_get_product( $item_id );
			}

			// Check is variable product with selected default variation and set item ID to be variation id.
			if ( $product && 'variable' === $product->get_type() ) {
				// Get Available variations?
				$get_variations       = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
				$available_variations = $get_variations ? $product->get_available_variations() : false;
				$selected_attributes  = $product->get_default_attributes();
				$variation_id         = '';

				if ( ! empty( $available_variations ) && ! empty( $selected_attributes ) ) {
					$formatted_selected_attributes = array();

					foreach ( $selected_attributes as $selected_attribute_key => $selected_attribute_value ) {
						$formatted_selected_attributes[ 'attribute_' . $selected_attribute_key ] = $selected_attribute_value;
					}

					foreach ( $available_variations as $available_variation ) {
						if ( ! empty( $available_variation['attributes'] ) ) {
							$is_variations_same = array_diff( $available_variation['attributes'], $formatted_selected_attributes );

							if ( empty( $is_variations_same ) ) {
								$variation_id = $available_variation['variation_id'];
								break;
							}
						}
					}
				}

				if ( ! empty( $variation_id ) ) {
					$atts['original_item_id'] = $item_id;
					$atts['item_id']          = $variation_id;
				}
			}

			if ( ! apply_filters( 'qode_wishlist_for_woocommerce_filter_show_wishlist_button', true, $atts['item_id'] ) ) {
				return '';
			}

			// Global options.
			$is_single_page          = $this->is_single_product_page( $atts );
			$button_behavior_option  = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_behavior' );
			$button_type_option      = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_type' );
			$button_type_loop_option = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_loop_type' );

			// Set shortcode attributes.
			$atts['is_single'] = $is_single_page;

			$default_button_type = ! $is_single_page && ! empty( $button_type_loop_option ) ? $button_type_loop_option : ( $button_type_option ?? apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_type_default_value', 'icon-with-text' ) );

			// If the item is on the cart page, then use the same layout as on the single product page.
			if ( isset( $atts['show_on_cart'] ) && 'yes' === $atts['show_on_cart'] ) {
				$default_button_type = $button_type_option;
			}

			$atts['button_behavior'] = ! empty( $atts['button_behavior'] ) ? $atts['button_behavior'] : apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_shortcode_button_behavior', $button_behavior_option, $atts );
			$atts['button_type']     = ! empty( $atts['button_type'] ) ? $atts['button_type'] : apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_shortcode_button_type', $default_button_type, $atts );

			$atts = apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_attributes', $atts );

			$atts['wrapper_classes'] = $this->get_wrapper_classes( $atts );
			$atts['holder_classes']  = $this->get_holder_classes( $atts );
			$atts['holder_data']     = $this->get_holder_data( $atts );

			return apply_filters(
				'qode_wishlist_for_woocommerce_filter_add_to_wishlist_button',
				qode_wishlist_for_woocommerce_get_template_part( 'wishlist/shortcodes/add-to-wishlist', 'templates/add-to-wishlist', '', $atts ),
				$atts['item_id'],
				$atts
			);
		}

		private function is_single_product_page( $atts ) {
			$is_single = is_product() && function_exists( 'wc_get_loop_prop' ) && ! in_array( wc_get_loop_prop( 'name' ), array( 'related', 'up-sells' ), true );

			if ( 'yes' === $atts['single_layout'] ) {
				$is_single = true;
			}

			return $is_single;
		}

		private function get_wrapper_classes( $atts ) {
			$wrapper_classes = array( 'qwfw-add-to-wishlist-wrapper' );

			if ( isset( $atts['show_on_cart'] ) && 'yes' === $atts['show_on_cart'] ) {
				$wrapper_classes[] = 'qwfw--cart';
			} else {
				$button_loop_position   = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_button_loop_position' );
				$button_single_position = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_button_single_position' );

				$button_thumbnail_loop_position   = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_thumbnail_loop_position' );
				$button_thumbnail_single_position = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_add_to_wishlist_thumbnail_single_position' );

				$wrapper_classes[] = $atts['is_single'] ? 'qwfw--single' : 'qwfw--loop';

				$button_position = $atts['is_single'] ? $button_single_position : $button_loop_position;
				if ( ! empty( $button_position ) ) {
					$wrapper_classes[] = 'qwfw-position--' . esc_attr( $button_position );
				}

				if ( 'on-thumbnail' === $button_position ) {

					if ( ! $atts['is_single'] && ! empty( $button_thumbnail_loop_position ) ) {
						$wrapper_classes[] = 'qwfw-thumb--' . esc_attr( $button_thumbnail_loop_position );
					} elseif ( $atts['is_single'] && ! empty( $button_thumbnail_single_position ) ) {
						$wrapper_classes[] = 'qwfw-thumb--' . esc_attr( $button_thumbnail_single_position );
					}
				}
			}

			if ( ! empty( $atts['button_type'] ) ) {
				$wrapper_classes[] = 'qwfw-item-type--' . esc_attr( $atts['button_type'] );
			}

			if ( 'yes' === ( $atts['show_count'] ?? '' ) ) {
				$wrapper_classes[] = 'qwfw-count--on';
			}

			return apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_wrapper_classes', $wrapper_classes, $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qwfw-add-to-wishlist';
			$holder_classes[] = 'qwfw-spinner-item';

			if ( ! empty( $atts['button_behavior'] ) ) {
				$holder_classes[] = 'qwfw-behavior--' . esc_attr( $atts['button_behavior'] );
			}

			if ( ! empty( $atts['button_type'] ) ) {
				$holder_classes[] = 'qwfw-type--' . esc_attr( $atts['button_type'] );
			}

			if ( qode_wishlist_for_woocommerce_check_is_wishlist_item_added( $atts['item_id'] ) ) {
				$holder_classes[] = 'qwfw--added';
			}

			if ( $atts['require_login'] ?? false ) {
				$holder_classes[] = 'qwfw--disable';
			}

			return apply_filters( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_holder_classes', $holder_classes, $atts );
		}

		private function get_holder_data( $atts ) {
			$data = array();

			if ( isset( $atts['prevent_click'] ) && 'yes' === $atts['prevent_click'] ) {
				$data['href'] = 'javascript:void(0)';
			} else {
				$data['href'] = esc_url( $this->get_button_link( $atts ) );
			}

			$data['data-item-id']          = absint( $atts['item_id'] );
			$data['data-original-item-id'] = absint( $atts['original_item_id'] ?? $atts['item_id'] );
			$data['aria-label']            = esc_attr( qode_wishlist_for_woocommerce_get_add_to_wishlist_label( $atts ) );

			// Specific data which we used in ajax request.
			$data['data-shortcode-atts'] = wp_json_encode(
				stripslashes_deep(
					array(
						'button_behavior' => $atts['button_behavior'],
						'button_type'     => $atts['button_type'],
						'show_count'      => $atts['show_count'] ?? '',
						'require_login'   => $atts['require_login'] ?? false,
					)
				)
			);

			if ( 'view' !== $atts['button_behavior'] || ! qode_wishlist_for_woocommerce_check_is_wishlist_item_added( $atts['item_id'] ) ) {
				$data['rel'] = 'noopener noreferrer';
			}

			return $data;
		}

		private function get_button_link( $atts ) {
			$wishlist_page_url = qode_wishlist_for_woocommerce_get_wishlist_page_url();
			$link              = '?add_to_wishlist=' . absint( $atts['item_id'] );

			if ( 'view' === $atts['button_behavior'] && qode_wishlist_for_woocommerce_check_is_wishlist_item_added( $atts['item_id'] ) && ! empty( $wishlist_page_url ) ) {
				$tables = qode_wishlist_for_woocommerce_get_wishlist_tables_by_item_id( $atts['item_id'] );

				if ( ! empty( $tables ) ) {
					$tables = array_keys( $tables );

					$link = qode_wishlist_for_woocommerce_get_wishlist_page_url_with_args( array( 'table' => $tables[0] ) );
				} else {
					$link = $wishlist_page_url;
				}
			}

			if ( $atts['require_login'] ?? false ) {
				$link = wc_get_page_permalink( 'myaccount' );
			}

			return $link;
		}
	}
}
