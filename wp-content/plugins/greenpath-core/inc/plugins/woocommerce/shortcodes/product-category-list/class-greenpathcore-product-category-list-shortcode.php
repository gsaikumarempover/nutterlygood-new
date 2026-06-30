<?php

if ( ! function_exists( 'greenpath_core_add_product_category_list_shortcode' ) ) {
	/**
	 * Function that is adding shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes - Array of registered shortcodes
	 *
	 * @return array
	 */
	function greenpath_core_add_product_category_list_shortcode( $shortcodes ) {
		$shortcodes[] = 'GreenPathCore_Product_Category_List_Shortcode';

		return $shortcodes;
	}

	add_filter( 'greenpath_core_filter_register_shortcodes', 'greenpath_core_add_product_category_list_shortcode' );
}

if ( class_exists( 'GreenPathCore_List_Shortcode' ) ) {
	class GreenPathCore_Product_Category_List_Shortcode extends GreenPathCore_List_Shortcode {

		public function __construct() {
			$this->set_post_type( 'product' );
			$this->set_post_type_taxonomy( 'product_cat' );
			$this->set_layouts( apply_filters( 'greenpath_core_filter_product_category_list_layouts', array() ) );
			$this->set_extra_options( apply_filters( 'greenpath_core_filter_product_category_list_extra_options', array() ) );

			parent::__construct();
		}

		public function map_shortcode() {
			$this->set_shortcode_path( GREENPATH_CORE_PLUGINS_URL_PATH . '/woocommerce/shortcodes/product-category-list' );
			$this->set_base( 'greenpath_core_product_category_list' );
			$this->set_name( esc_html__( 'Product Category List', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Shortcode that displays list of product categories', 'greenpath-core' ) );
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'greenpath-core' ),
				)
			);
			$this->map_list_options(
				array(
					'exclude_behavior' => array( 'justified-gallery' ),
					'include_columns'  => array(
						'7'  => esc_html__( 'Seven', 'greenpath-core' ),
						'8'  => esc_html__( 'Eight', 'greenpath-core' ),
						'9'  => esc_html__( 'Nine', 'greenpath-core' ),
						'10' => esc_html__( 'Ten', 'greenpath-core' ),
					),
				)
			);
			$this->map_query_options(
				array(
					'exclude_option' => array( 'additional_params' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'hide_empty',
					'title'      => esc_html__( 'Hide Empty', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'no_yes', false ),
					'group'      => esc_html__( 'Query', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'additional_params',
					'title'      => esc_html__( 'Additional Params', 'greenpath-core' ),
					'options'    => array(
						''     => esc_html__( 'No', 'greenpath-core' ),
						'id'   => esc_html__( 'Taxonomy IDs', 'greenpath-core' ),
						'slug' => esc_html__( 'Taxonomy slugs', 'greenpath-core' ),
					),
					'group'      => esc_html__( 'Query', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'taxonomy_ids',
					'title'       => esc_html__( 'Taxonomy IDs', 'greenpath-core' ),
					'description' => esc_html__( 'Separate taxonomy IDs with commas', 'greenpath-core' ),
					'group'       => esc_html__( 'Query', 'greenpath-core' ),
					'dependency'  => array(
						'show' => array(
							'additional_params' => array(
								'values'        => 'id',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'taxonomy_slugs',
					'title'       => esc_html__( 'Taxonomy Slugs', 'greenpath-core' ),
					'description' => esc_html__( 'Separate taxonomy slugs with commas', 'greenpath-core' ),
					'group'       => esc_html__( 'Query', 'greenpath-core' ),
					'dependency'  => array(
						'show' => array(
							'additional_params' => array(
								'values'        => 'slug',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->map_layout_options( array( 'layouts' => $this->get_layouts() ) );
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_image_width',
					'title'      => esc_html__( 'Custom Image Width', 'greenpath-core' ),
					'group'      => esc_html__( 'Layout', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'use_alternate_image',
					'title'         => esc_html__( 'Use Alternate Image', 'greenpath-core' ),
					'description'   => esc_html__( 'Use alternate image meta instead of default image for Product Categories', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'default_value' => 'no',
					'group'         => esc_html__( 'Layout', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'show_category_title_on_hover',
					'title'         => esc_html__( 'Show Category Title On Hover', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'default_value' => 'yes',
					'group'         => esc_html__( 'Layout', 'greenpath-core' ),
					'dependency'    => array(
						'show' => array(
							'layout' => array(
								'values'        => 'info-on-image',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'title_padding',
					'title'      => esc_html__( 'Title Padding', 'greenpath-core' ),
					'group'      => esc_html__( 'Layout', 'greenpath-core' ),
					'dependency'    => array(
						'show' => array(
							'layout' => array(
								'values'        => 'info-on-image',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->map_slider_options(
				array(
					'dependency' => array(
						'show' => array(
							'behavior' => array(
								'values'        => 'slider',
								'default_value' => '',
							),
						)
					),
					'include_slider_option' => array(
						'slider_auto_height',
					),
				)
			);
		}

		public static function call_shortcode( $params ) {
			$html = qode_framework_call_shortcode( 'greenpath_core_product_category_list', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function render( $options, $content = null ) {
			parent::render( $options );

			$atts = $this->get_atts();

			$atts['post_type']           = $this->get_post_type();
			$atts['holder_classes']      = $this->get_holder_classes( $atts );
			$atts['holder_styles']       = $this->get_holder_styles( $atts );
			$atts['image_holder_styles'] = $this->get_image_holder_styles( $atts );
			$atts['taxonomy_items']      = get_terms( greenpath_core_get_custom_post_type_taxonomy_query_args( $atts, array( 'taxonomy' => $this->get_post_type_taxonomy() ) ) );
			$atts['slider_attr']         = $this->get_slider_data( $atts );

			$atts['this_shortcode'] = $this;

			return greenpath_core_get_template_part( 'plugins/woocommerce/shortcodes/product-category-list', 'templates/content', $atts['behavior'], $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-woo-shortcode';
			$holder_classes[] = 'qodef-woo-product-category-list';
			$holder_classes[] = ! empty( $atts['layout'] ) ? 'qodef-item-layout--' . $atts['layout'] : '';
			$holder_classes[] = ! empty( $atts['show_category_title_on_hover'] ) && 'yes' === $atts['show_category_title_on_hover'] ? 'qodef-title--on-hover' : '';
			$holder_classes[] = ! empty( $atts['use_alternate_image'] ) && 'yes' === $atts['use_alternate_image'] ? 'qodef--alternate-image' : '';

			$list_classes   = $this->get_list_classes( $atts );
			$holder_classes = array_merge( $holder_classes, $list_classes );

			return implode( ' ', $holder_classes );
		}

		private function get_holder_styles( $atts ) {
			$holder_styles = array();

			$list_styles   = $this->get_list_styles( $atts );
			$holder_styles = array_merge( $holder_styles, $list_styles );

			return $holder_styles;
		}

		public function get_image_holder_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['custom_image_width'] ) ) {
				$styles[] = 'max-width: ' . intval( $atts['custom_image_width'] ) . 'px';
			}

			return $styles;
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

			if ( ! empty( $atts['title_padding'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['title_padding'] ) ) {
					$styles[] = 'padding: ' . $atts['title_padding'];
				} else {
					$styles[] = 'padding: ' . intval( $atts['title_padding'] ) . 'px';
				}
			}

			return $styles;
		}

		public function get_item_styles( $atts, $id ) {
			$styles = array();

			$bg_color = get_term_meta( $id, 'qodef_product_category_svg_bg' );

			if ( ! empty( $bg_color ) ) {
				$styles[] = 'background-color: ' . $bg_color[0];
			}

			return $styles;
		}

		public function get_image_dimension( $atts ) {
			$image_dimension = array();

			if ( ! empty( $atts['behavior'] ) && 'masonry' == $atts['behavior'] && ! empty( $atts['masonry_images_proportion'] ) && 'fixed' == $atts['masonry_images_proportion'] ) {
				$image_dimension = greenpath_core_get_custom_image_size_meta( 'taxonomy', 'qodef_product_category_masonry_size', $atts['category_id'] );
			}

			if ( ! empty( $atts['behavior'] ) && in_array( $atts['behavior'], array( 'columns', 'slider' ), true ) ) {
				$image_dimension = array(
					'size'  => $atts['images_proportion'],
					'class' => greenpath_core_get_custom_image_size_class_name( $atts['images_proportion'] ),
				);
			}

			return $image_dimension;
		}
	}
}
