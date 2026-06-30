<?php

if ( ! function_exists( 'greenpath_core_add_product_list_shortcode' ) ) {
	/**
	 * Function that is adding shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes - Array of registered shortcodes
	 *
	 * @return array
	 */
	function greenpath_core_add_product_list_shortcode( $shortcodes ) {
		$shortcodes[] = 'GreenPathCore_Product_List_Shortcode';

		return $shortcodes;
	}

	add_filter( 'greenpath_core_filter_register_shortcodes', 'greenpath_core_add_product_list_shortcode' );
}

if ( class_exists( 'GreenPathCore_List_Shortcode' ) ) {
	class GreenPathCore_Product_List_Shortcode extends GreenPathCore_List_Shortcode {

		public function __construct() {
			$this->set_post_type( 'product' );
			$this->set_post_type_taxonomy( 'product_cat' );
			$this->set_post_type_additional_taxonomies( array( 'product_tag', 'product_type' ) );
			$this->set_layouts( apply_filters( 'greenpath_core_filter_product_list_layouts', array() ) );
			$this->set_extra_options( apply_filters( 'greenpath_core_filter_product_list_extra_options', array() ) );

			parent::__construct();
		}

		public function map_shortcode() {
			$this->set_shortcode_path( GREENPATH_CORE_PLUGINS_URL_PATH . '/woocommerce/shortcodes/product-list' );
			$this->set_base( 'greenpath_core_product_list' );
			$this->set_name( esc_html__( 'Product List', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Shortcode that displays list of products', 'greenpath-core' ) );
			$this->set_scripts(
				array(
					'jquery-ui-slider' => array(
						'registered' => true,
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'greenpath-core' ),
				)
			);
			$this->map_list_options();
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'slider_type',
					'title'         => esc_html__( 'Slider Type', 'greenpath-core' ),
					'options'       => array(
						''          => esc_html__( 'Default', 'greenpath-core' ),
						'info-left' => esc_html__( 'Info Left', 'greenpath-core' ),
					),
					'default_value' => '',
					'dependency'  => array(
						'show' => array(
							'behavior' => array(
								'values'        => 'slider',
								'default_value' => 'columns',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'text',
					'name'          => 'slider_title',
					'title'         => esc_html__( 'Slider Title', 'greenpath-core' ),
					'dependency'  => array(
						'show' => array(
							'slider_type' => array(
								'values'        => 'info-left',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'text',
					'name'          => 'slider_text',
					'title'         => esc_html__( 'Slider Text', 'greenpath-core' ),
					'dependency'  => array(
						'show' => array(
							'slider_type' => array(
								'values'        => 'info-left',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->map_query_options( array( 'post_type' => $this->get_post_type() ) );
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'filterby',
					'title'         => esc_html__( 'Filter By', 'greenpath-core' ),
					'options'       => array(
						''             => esc_html__( 'Default', 'greenpath-core' ),
						'on_sale'      => esc_html__( 'On Sale', 'greenpath-core' ),
						'featured'     => esc_html__( 'Featured', 'greenpath-core' ),
						'top_rated'    => esc_html__( 'Top Rated', 'greenpath-core' ),
						'best_selling' => esc_html__( 'Best Selling', 'greenpath-core' ),
					),
					'default_value' => '',
					'group'         => esc_html__( 'Query', 'greenpath-core' ),
				)
			);
			$this->map_layout_options( array( 'layouts' => $this->get_layouts() ) );
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'enable_price',
					'title'      => esc_html__( 'Enable Price', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'no_yes', false ),
					'dependency' => array(
						'show' => array(
							'layout' => array(
								'values'        => 'catalogue',
								'default_value' => 'info-below',
							),
						),
					),
					'group'      => esc_html__( 'Layout', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'item_background_color',
					'title'      => esc_html__( 'Item Background Color', 'greenpath-core' ),
					'group'      => esc_html__( 'Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'item_enable_border',
					'title'      => esc_html__( 'Enable Item Border', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'group'      => esc_html__( 'Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'item_border_radius',
					'title'      => esc_html__( 'Item Border Radius', 'greenpath-core' ),
					'group'      => esc_html__( 'Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'enable_wishlist',
					'title'      => esc_html__( 'Enable Wishlist', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'group'      => esc_html__( 'Icons', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'enable_quickview',
					'title'      => esc_html__( 'Enable Quick View', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'group'      => esc_html__( 'Icons', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'enable_compare_product',
					'title'      => esc_html__( 'Enable Compare Product', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'group'      => esc_html__( 'Icons', 'greenpath-core' ),
				)
			);
			$this->map_additional_options( array( 'exclude_filter' => true ) );
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'enable_custom_filter',
					'title'         => esc_html__( 'Enable Filter', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'default_value' => 'no',
					'group'         => esc_html__( 'Filter', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'filter_type',
					'title'         => esc_html__( 'Filter Type', 'greenpath-core' ),
					'options'       => array(
						'simple'   => esc_html__( 'Simple', 'greenpath-core' ),
						'advanced' => esc_html__( 'Advanced', 'greenpath-core' ),
					),
					'default_value' => 'simple',
					'group'         => esc_html__( 'Filter', 'greenpath-core' ),
					'dependency'    => array(
						'show' => array(
							'enable_custom_filter' => array(
								'values'        => 'yes',
								'default_value' => 'no',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'advanced_filter_type',
					'title'         => esc_html__( 'Advanced Filter Type', 'greenpath-core' ),
					'options'       => array(
						'sidebar'   => esc_html__( 'Sidebar', 'greenpath-core' ),
						'top'       => esc_html__( 'Top', 'greenpath-core' ),
						'side-area' => esc_html__( 'Side Area', 'greenpath-core' ),
					),
					'default_value' => 'sidebar',
					'group'         => esc_html__( 'Filter', 'greenpath-core' ),
					'dependency'    => array(
						'show' => array(
							'filter_type' => array(
								'values'        => 'advanced',
								'default_value' => 'simple',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'select',
					'name'        => 'enable_grid_filter',
					'title'       => esc_html__( 'Enable Grid Filter', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'description' => esc_html__( 'Enabling this option will set a predefined grid layout', 'greenpath-core' ),
					'group'       => esc_html__( 'Filter', 'greenpath-core' ),
					'dependency'    => array(
						'show' => array(
							'filter_type' => array(
								'values'        => 'advanced',
								'default_value' => 'simple',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'enable_ordering_filter',
					'title'      => esc_html__( 'Enable Ordering Filter', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'group'      => esc_html__( 'Filter', 'greenpath-core' ),
					'dependency'    => array(
						'show' => array(
							'filter_type' => array(
								'values'        => 'advanced',
								'default_value' => 'simple',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'first_attribute_filter',
					'title'      => esc_html__( 'First Attribute Filter', 'greenpath-core' ),
					'options'    => $this->get_product_attributes(),
					'group'      => esc_html__( 'Filter', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'filter_type' => array(
								'values'        => 'advanced',
								'default_value' => 'simple',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'second_attribute_filter',
					'title'      => esc_html__( 'Second Attribute Filter', 'greenpath-core' ),
					'options'    => $this->get_product_attributes(),
					'group'      => esc_html__( 'Filter', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'filter_type' => array(
								'values'        => 'advanced',
								'default_value' => 'simple',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'sidebar_filter_margin',
					'title'      => esc_html__( 'Sidebar Filter Margin', 'greenpath-core' ),
					'group'      => esc_html__( 'Style', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'filter_type' => array(
								'values'        => 'advanced',
								'default_value' => 'simple',
							),
						),
					),
				)
			);
			$this->map_extra_options();
		}

		public static function call_shortcode( $params ) {
			$html = qode_framework_call_shortcode( 'greenpath_core_product_list', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function load_assets() {
			wp_enqueue_style( 'perfect-scrollbar', GREENPATH_CORE_URL_PATH . 'assets/plugins/perfect-scrollbar/perfect-scrollbar.css', array() );
			wp_enqueue_script( 'perfect-scrollbar', GREENPATH_CORE_URL_PATH . 'assets/plugins/perfect-scrollbar/perfect-scrollbar.jquery.min.js', array( 'jquery' ), false, true );
		}

		public function render( $options, $content = null ) {
			parent::render( $options );

			$atts = $this->get_atts();

			//$this->manage_yith_icons( $atts );

			$atts['post_type']       = $this->get_post_type();
			$atts['taxonomy_filter'] = $this->get_post_type_filter_taxonomy( $atts );

			// Additional query args
			$atts['additional_query_args'] = $this->get_additional_query_args( $atts );

			$atts['unique'] = rand( 0, 1000 );

			$atts['slider_holder_classes'] = $this->get_slider_holder_classes( $atts );
			$atts['holder_classes']        = $this->get_holder_classes( $atts );
			$atts['holder_styles']         = $this->get_holder_styles( $atts );
			$atts['query_result']          = new WP_Query( greenpath_core_get_query_params( $atts ) );
			$atts['slider_attr']           = $this->get_slider_data( $atts, array( 'unique' => $atts['unique'] ) );
			$atts['data_attr']             = greenpath_core_get_pagination_data( GREENPATH_CORE_REL_PATH, 'plugins/woocommerce/shortcodes', 'product-list', 'product', $atts );
			$atts['product_brands']        = get_terms(
				array(
					'taxonomy'   => 'product_brand',
					'hide_empty' => true,
					'orderby'    => 'name',
				)
			);
			if ( isset( $atts['tax'] ) && ! empty( $atts['tax'] ) && 'product_cat' === $atts['tax'] ) {
				$atts['product_categories'] = greenpath_get_filter_items( $atts );
			} else {
				$atts['product_categories'] = get_terms(
					array(
						'taxonomy'   => 'product_cat',
						'hide_empty' => true,
						'orderby'    => 'name',
					)
				);
			}
			$atts['this_shortcode'] = $this;

			return greenpath_core_get_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/content', $atts['behavior'], $atts );
		}

		public function get_additional_query_args( $atts ) {
			$args = parent::get_additional_query_args( $atts );

			if ( ! empty( $atts['filterby'] ) ) {
				switch ( $atts['filterby'] ) {
					case 'on_sale':
						$sale_products         = wc_get_product_ids_on_sale();
						$args['no_found_rows'] = 1;
						$args['post__in']      = array_merge( array( 0 ), $sale_products );

						if ( ! empty( $atts['additional_params'] ) && 'id' === $atts['additional_params'] && ! empty( $atts['post_ids'] ) ) {
							$post_ids          = array_map( 'intval', explode( ',', $atts['post_ids'] ) );
							$new_sale_products = array();

							foreach ( $post_ids as $post_id ) {
								if ( in_array( $post_id, $sale_products, true ) ) {
									$new_sale_products[] = $post_id;
								}
							}

							if ( ! empty( $new_sale_products ) ) {
								$args['post__in'] = $new_sale_products;
							}
						}

						break;
					case 'featured':
						$featured_tax_query   = WC()->query->get_tax_query();
						$featured_tax_query[] = array(
							'taxonomy'         => 'product_visibility',
							'terms'            => 'featured',
							'field'            => 'name',
							'operator'         => 'IN',
							'include_children' => false,
						);

						if ( isset( $args['tax_query'] ) && ! empty( $args['tax_query'] ) ) {
							$args['tax_query'] = array_merge( $args['tax_query'], $featured_tax_query );
						} else {
							$args['tax_query'] = $featured_tax_query;
						}

						break;
					case 'top_rated':
						$args['meta_key'] = '_wc_average_rating';
						$args['order']    = 'DESC';
						$args['orderby']  = 'meta_value_num';
						break;
					case 'best_selling':
						$args['meta_key'] = 'total_sales';
						$args['order']    = 'DESC';
						$args['orderby']  = 'meta_value_num';
						break;
				}
			}

			return $args;
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-woo-shortcode';
			$holder_classes[] = 'qodef-woo-product-list';
			$holder_classes[] = 'advanced' === $atts['filter_type'] && ! empty( $atts['advanced_filter_type'] ) ? 'qodef-filter--advanced' : '';
			$holder_classes[] = 'advanced' === $atts['filter_type'] && ! empty( $atts['advanced_filter_type'] ) ? 'qodef-filter-type--' . $atts['advanced_filter_type'] : '';
			$holder_classes[] = 'no' === $atts['enable_wishlist'] ? 'qodef--no-wishlist' : '';
			$holder_classes[] = 'no' === $atts['enable_quickview'] ? 'qodef--no-quickview' : '';
			$holder_classes[] = 'no' === $atts['enable_compare_product'] ? 'qodef--no-compare' : '';
			$holder_classes[] = 'yes' === $atts['enable_grid_filter'] ? 'qodef-grid-filter--on' : '';
			$holder_classes[] = 'no' === $atts['item_enable_border'] ? 'qodef-item--no-border' : '';

			if ( 'yes' === $atts['enable_custom_filter'] && 'simple' === $atts['filter_type'] ) {
				$holder_classes[] = 'qodef-filter--on';
			}

			if ( 'yes' === $atts['enable_grid_filter'] ) {
				if ( 'horizontal' === $atts['layout'] ) {
					$atts['columns'] = '1';
				}
			}

			$list_classes   = $this->get_list_classes( $atts );
			$holder_classes = array_merge( $holder_classes, $list_classes );

			return implode( ' ', $holder_classes );
		}

		private function get_slider_holder_classes( $atts ) {
			$slider_classes = array();

			$slider_classes[] = 'qodef-product-slider-holder';
			$slider_classes[] = ! empty( $atts['slider_type'] ) ? 'qodef-woo-product-slider--' . $atts['slider_type'] : '';

			return implode( ' ', $slider_classes );
		}

		private function get_holder_styles( $atts ) {
			$holder_styles = array();

			$list_styles = $this->get_list_styles( $atts );

			$holder_styles[] = ! empty( $atts['item_background_color'] ) ? '--qode-product-list-background-color: ' . $atts['item_background_color'] : '';

			if ( ! empty( $atts['item_border_radius'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['item_border_radius'] ) ) {
					$holder_styles[] = '--qode-product-list-border-radius: ' . $atts['item_border_radius'];
					$holder_styles[] = '--qode-product-list-border-radius-mobile: ' . $atts['item_border_radius'];
				} else {
					$holder_styles[] = '--qode-product-list-border-radius: ' . $atts['item_border_radius'] . 'px';
					$holder_styles[] = '--qode-product-list-border-radius-mobile: ' . $atts['item_border_radius'] . 'px';
				}
			}

			if ( ! empty( $atts['sidebar_filter_margin'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['sidebar_filter_margin'] ) ) {
					$holder_styles[] = '--qode-product-list-sidebar-filter-margin: ' . $atts['sidebar_filter_margin'];
				} else {
					$holder_styles[] = '--qode-product-list-sidebar-filter-margin: ' . $atts['sidebar_filter_margin'] . 'px';
				}
			}

			$holder_styles = array_merge( $holder_styles, $list_styles );

			return $holder_styles;
		}

		public function get_item_classes( $atts ) {
			$item_classes      = $this->init_item_classes();
			$list_item_classes = $this->get_list_item_classes( $atts );

			$item_classes = array_merge( $item_classes, $list_item_classes );

			return implode( ' ', $item_classes );
		}

		public function get_title_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['text_transform'] ) ) {
				$styles[] = 'text-transform: ' . $atts['text_transform'];
			}

			return $styles;
		}

		private function get_product_attributes() {
			global $wpdb;

			$attribute_array      = array();
			$attribute_taxonomies = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'woocommerce_attribute_taxonomies order by attribute_name ASC;' );
			$attribute_array['']  = esc_html__( 'None', 'greenpath-core' );

			if ( ! empty( $attribute_taxonomies ) ) {
				foreach ( $attribute_taxonomies as $tax ) {
					$attribute_array[ $tax->attribute_name ] = $tax->attribute_label;
				}
			} else {
				$attribute_array[''] = esc_html__( 'No available attributes', 'greenpath-core' );
			}

			return $attribute_array;
		}

		/*private function manage_yith_icons( $atts ) {
			global $yith_woocompare;

			if ( qode_framework_is_installed( 'yith-wishlist' ) && 'no' === $atts['enable_wishlist'] ) {
				remove_action( 'greenpath_core_action_product_list_item_additional_content', 'greenpath_core_get_yith_wishlist_shortcode' );
			}

			if ( qode_framework_is_installed( 'yith-quick-view' ) && function_exists( 'YITH_WCQV_Frontend' ) && 'no' === $atts['enable_quickview'] ) {
				remove_action( 'greenpath_core_action_product_list_item_additional_content', array( YITH_WCQV_Frontend(), 'yith_add_quick_view_button' ) );
			}

			if ( qode_framework_is_installed( 'yith-compare' ) && 'no' === $atts['enable_compare_product'] ) {
				remove_action(
					'greenpath_core_action_product_list_item_additional_content',
					array(
						$yith_woocompare->obj,
						'add_compare_link',
					),
					17
				);
			}
		}*/
	}
}
