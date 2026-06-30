<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_add_wishlist_table_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function qode_wishlist_for_woocommerce_add_wishlist_table_shortcode( $shortcodes ) {
		$shortcodes[] = 'Qode_Wishlist_For_WooCommerce_Wishlist_Table_Shortcode';

		return $shortcodes;
	}

	add_filter( 'qode_wishlist_for_woocommerce_filter_register_shortcodes', 'qode_wishlist_for_woocommerce_add_wishlist_table_shortcode' );
}

if ( class_exists( 'Qode_Wishlist_For_WooCommerce_Shortcode' ) ) {
	class Qode_Wishlist_For_WooCommerce_Wishlist_Table_Shortcode extends Qode_Wishlist_For_WooCommerce_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( QODE_WISHLIST_FOR_WOOCOMMERCE_INC_URL_PATH . '/wishlist/shortcodes/wishlist-table' );
			$this->set_base( 'qode_wishlist_for_woocommerce_table' );
			$this->set_name( esc_html__( 'Wishlist Table', 'qode-wishlist-for-woocommerce' ) );
			$this->set_description( esc_html__( 'Shortcode that displays wishlist table with items', 'qode-wishlist-for-woocommerce' ) );
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
					'field_type' => 'text',
					'name'       => 'table_title',
					'title'      => esc_html__( 'Wishlist Table Title', 'qode-wishlist-for-woocommerce' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'table_title_tag',
					'title'         => esc_html__( 'Wishlist Table Title Tag', 'qode-wishlist-for-woocommerce' ),
					'options'       => qode_wishlist_for_woocommerce_get_select_type_options_pool( 'title_tag' ),
					'default_value' => '',
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'products_per_page',
					'title'      => esc_html__( 'Products per Page', 'qode-wishlist-for-woocommerce' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'enable_remove_item',
					'title'      => esc_html__( 'Enable Remove Item Icon', 'qode-wishlist-for-woocommerce' ),
					'options'    => qode_wishlist_for_woocommerce_get_select_type_options_pool( 'yes_no' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'enable_add_to_cart',
					'title'      => esc_html__( 'Enable Add to Cart Button', 'qode-wishlist-for-woocommerce' ),
					'options'    => qode_wishlist_for_woocommerce_get_select_type_options_pool( 'yes_no' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'enable_social_share',
					'title'      => esc_html__( 'Enable Social Share', 'qode-wishlist-for-woocommerce' ),
					'options'    => qode_wishlist_for_woocommerce_get_select_type_options_pool( 'yes_no' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'hidden',
					'name'       => 'enable_quick_table_links',
					'title'      => esc_html__( 'Enable Quick Table Links', 'qode-wishlist-for-woocommerce' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'hidden',
					'name'       => 'forward_items',
					'title'      => esc_html__( 'Wishlist Items', 'qode-wishlist-for-woocommerce' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'hidden',
					'name'       => 'is_print_template',
					'title'      => esc_html__( 'Is Print Template', 'qode-wishlist-for-woocommerce' ),
				)
			);
		}

		public static function call_shortcode( $params = array() ) {
			$html = qode_wishlist_for_woocommerce_call_shortcode( 'qode_wishlist_for_woocommerce_table', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			// Global options.
			$show_table_title_option = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_show_table_title' );
			$table_title_tag_option  = qode_wishlist_for_woocommerce_get_option_value( 'admin', 'qode_wishlist_for_woocommerce_table_title_tag' );

			$get_table_name = qode_wishlist_for_woocommerce_get_http_request_args( false, 'table' );

			$atts['layout']     = apply_filters( 'qode_wishlist_for_woocommerce_filter_wishlist_table_default_layout', 'table' );
			$atts['table_name'] = ! empty( $get_table_name ) ? $get_table_name : 'default';
			$atts['all_items']  = qode_wishlist_for_woocommerce_get_wishlist_items( true );
			$atts['items']      = ! empty( $atts['forward_items'] ) ? json_decode( sanitize_text_field( wp_unslash( $atts['forward_items'] ) ), true ) : qode_wishlist_for_woocommerce_get_wishlist_items_by_table( $atts['table_name'] );

			// Additional check if multi wishlist is active and default wishlist is empty, show items from first filled wishlist.
			if ( apply_filters( 'qode_wishlist_for_woocommerce_filter_is_multi_wishlist_on', false ) && 'yes' !== $atts['is_print_template'] && empty( $atts['items'] ) ) {
				$other_wishlists = qode_wishlist_for_woocommerce_get_cleaned_wishlist_tables( $atts['all_items'] );

				if ( ! empty( $other_wishlists ) ) {
					foreach ( $other_wishlists as $other_wishlist_table_name => $other_wishlist_items ) {

						if ( 'default' !== $other_wishlist_table_name ) {
							$atts['table_name'] = $other_wishlist_table_name;
							$atts['items']      = qode_wishlist_for_woocommerce_get_wishlist_items_by_table( $atts['table_name'] );
							break;
						}
					}
				}
			}

			$atts = apply_filters( 'qode_wishlist_for_woocommerce_filter_wishlist_table_attributes', $atts );

			$atts['holder_classes']    = $this->get_holder_classes( $atts );
			$atts['products_per_page'] = ! empty( $atts['products_per_page'] ) ? esc_attr( $atts['products_per_page'] ) : apply_filters( 'qode_wishlist_for_woocommerce_filter_wishlist_table_default_products_per_page', get_option( 'posts_per_page' ) );
			$atts['table_title']       = 'no' !== $show_table_title_option ? ( '' !== $atts['table_title'] ? $atts['table_title'] : $atts['all_items'][ $atts['table_name'] ]['table_title'] ?? '' ) : '';
			$atts['table_title_tag']   = ! empty( $atts['table_title_tag'] ) ? $atts['table_title_tag'] : ( $table_title_tag_option ?? 'h3' );

			$atts['shortcode_atts'] = qode_wishlist_for_woocommerce_get_wishlist_table_shortcode_atts( $atts['items'], $atts );
			$atts['table_data']     = $this->get_table_data( $atts );

			if ( $atts['shortcode_atts']['products_per_page'] > 0 ) {
				$atts['items'] = array_slice( $atts['items'], 0, $atts['shortcode_atts']['products_per_page'] );
			}

			$atts['visibility'] = qode_wishlist_for_woocommerce_get_wishlist_table_meta( $atts['table_name'], 'visibility', $atts['all_items'] );
			$atts['is_visible'] = ! empty( $atts['items'] ) ? $this->check_table_visibility( $atts ) : true;

			return qode_wishlist_for_woocommerce_get_template_part( 'wishlist/shortcodes/wishlist-table', 'templates/holder', '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();
			$has_token      = qode_wishlist_for_woocommerce_get_http_request_args( true );

			$holder_classes[] = 'qwfw-wishlist-table';

			if ( ! empty( $has_token ) ) {
				$holder_classes[] = 'qwfw--has-token';
			}

			if ( ! empty( $atts['layout'] ) ) {
				$holder_classes[] = 'qwfw-layout--' . esc_attr( $atts['layout'] );
			}

			if ( empty( $atts['items'] ) ) {
				$holder_classes[] = 'qwfw--no-items';
			}

			return apply_filters( 'qode_wishlist_for_woocommerce_filter_wishlist_table_holder_classes', $holder_classes, $atts );
		}

		private function get_table_data( $atts ) {
			$data = array();

			$data['data-token']          = $atts['all_items']['token'] ?? qode_wishlist_for_woocommerce_generate_token();
			$data['data-table']          = esc_attr( $atts['table_name'] );
			$data['data-shortcode-atts'] = wp_json_encode( stripslashes_deep( $atts['shortcode_atts'] ) );

			return $data;
		}

		private function check_table_visibility( $atts ) {
			$visibility = $atts['visibility'] ?? '';
			$is_visible = 'public' === $visibility || 'yes' === $atts['is_print_template'];

			// phpcs:ignore WordPress.Security.NonceVerification
			$is_table_view         = isset( $_GET['view'] ) && ! empty( $_GET['view'] );
			$is_site_administrator = is_multisite() ? current_user_can( 'manage_sites' ) : current_user_can( 'manage_options' );

			// Allows site administrator to manage all wishlists.
			if ( $is_site_administrator ) {
				$is_visible = true;
			} else {

				if (
					'shared' === $visibility &&
					(
						// phpcs:ignore WordPress.Security.NonceVerification
						( $is_table_view && sanitize_text_field( wp_unslash( $_GET['view'] ) ) === $atts['all_items']['token'] ) ||
						( is_user_logged_in() && get_current_user_id() === $atts['all_items']['user'] ) ||
						( ! $is_table_view && ! is_user_logged_in() && 'guest' === $atts['all_items']['user'] )
					)
				) {
					$is_visible = true;
				} elseif (
					'private' === $visibility &&
					(
						( is_user_logged_in() && get_current_user_id() === $atts['all_items']['user'] ) ||
						( ! is_user_logged_in() && 'guest' === $atts['all_items']['user'] )
					)
				) {
					$is_visible = true;
				}
			}

			return $is_visible;
		}
	}
}
