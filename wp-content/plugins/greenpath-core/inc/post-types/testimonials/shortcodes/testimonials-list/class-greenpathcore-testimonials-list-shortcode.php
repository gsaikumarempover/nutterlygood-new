<?php

if ( ! function_exists( 'greenpath_core_add_testimonials_list_shortcode' ) ) {
	/**
	 * Function that is adding shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes - Array of registered shortcodes
	 *
	 * @return array
	 */
	function greenpath_core_add_testimonials_list_shortcode( $shortcodes ) {
		$shortcodes[] = 'GreenPathCore_Testimonials_List_Shortcode';

		return $shortcodes;
	}

	add_filter( 'greenpath_core_filter_register_shortcodes', 'greenpath_core_add_testimonials_list_shortcode' );
}

if ( class_exists( 'GreenPathCore_List_Shortcode' ) ) {
	class GreenPathCore_Testimonials_List_Shortcode extends GreenPathCore_List_Shortcode {

		public function __construct() {
			$this->set_post_type( 'testimonials' );
			$this->set_post_type_additional_taxonomies( array( 'testimonials-category' ) );
			$this->set_layouts( apply_filters( 'greenpath_core_filter_testimonials_list_layouts', array() ) );
			$this->set_extra_options( apply_filters( 'greenpath_core_filter_testimonials_list_extra_options', array() ) );

			parent::__construct();
		}

		public function map_shortcode() {
			$this->set_shortcode_path( GREENPATH_CORE_CPT_URL_PATH . '/testimonials/shortcodes/testimonials-list' );
			$this->set_base( 'greenpath_core_testimonials_list' );
			$this->set_name( esc_html__( 'Testimonials List', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Shortcode that displays list of testimonials', 'greenpath-core' ) );
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'greenpath-core' ),
				)
			);
			$this->map_list_options(
				array(
					'exclude_behavior'      => array( 'masonry', 'justified-gallery' ),
					'include_slider_option' => array( 'slider_direction', 'slider_centered_slides' ),
					'exclude_option'        => array( 'images_proportion' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'skin',
					'title'      => esc_html__( 'Skin', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'shortcode_skin' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'static_title',
					'title'      => esc_html__( 'Static Title', 'greenpath-core' ),
					'group'      => esc_html__( 'Layout', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'static_title_tag',
					'title'         => esc_html__( 'Static Title Tag', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'title_tag', false ),
					'default_value' => 'h2',
					'group'         => esc_html__( 'Layout', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'enable_quote',
					'title'         => esc_html__( 'Enable Quote Sign', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'yes_no' ),
					'default_value' => 'yes',
					'dependency'  => array(
						'show' => array(
							'layout' => array(
								'values'        => 'info-boxed',
								'default_value' => '',
							),
						),
					),
					'group'         => esc_html__( 'Layout', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'centered_content',
					'title'         => esc_html__( 'Centered Content', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes', true ),
					'default_value' => '',
					'dependency'  => array(
						'show' => array(
							'layout' => array(
								'values'        => 'info-below',
								'default_value' => '',
							),
						),
					),
					'group'         => esc_html__( 'Layout', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'text_alternate_style',
					'title'         => esc_html__( 'Use Alternate Text Style', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes', true ),
					'default_value' => '',
					'group'         => esc_html__( 'Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'name'        => 'retina_scaling',
					'field_type'  => 'select',
					'title'       => esc_html__( 'Enable Retina Scaling', 'greenpath-core' ),
					'options'     => greenpath_core_get_select_type_options_pool( 'no_yes', false ),
					'description' => esc_html__( 'Uploaded Testimonials Images should be two times the size.', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'title_margin_top',
					'title'      => esc_html__( 'Title Margin Top', 'greenpath-core' ),
					'group'      => esc_html__( 'Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'text_margin_top',
					'title'      => esc_html__( 'Text Margin Top', 'greenpath-core' ),
					'group'      => esc_html__( 'Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'author_margin_top',
					'title'      => esc_html__( 'Author Margin Top', 'greenpath-core' ),
					'group'      => esc_html__( 'Style', 'greenpath-core' ),
				)
			);
			$this->map_query_options( array( 'post_type' => $this->get_post_type() ) );
			$this->map_layout_options( array( 'layouts' => $this->get_layouts() ) );
			$this->map_extra_options();
		}

		public static function call_shortcode( $params ) {
			$html = qode_framework_call_shortcode( 'greenpath_core_testimonials_list', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function render( $options, $content = null ) {
			parent::render( $options );

			$atts = $this->get_atts();

			$atts['post_type'] = $this->get_post_type();

			// Additional query args
			$atts['additional_query_args'] = $this->get_additional_query_args( $atts );

			$atts['unique'] = rand( 0, 1000 );

			$atts['holder_classes'] = $this->get_holder_classes( $atts );
			$atts['holder_styles']  = $this->get_holder_styles( $atts );
			$atts['item_classes']   = $this->get_item_classes( $atts );
			$atts['title_styles']   = $this->get_title_styles( $atts );
			$atts['text_styles']    = $this->get_text_styles( $atts );
			$atts['author_styles']  = $this->get_author_styles( $atts );
			$atts['slider_attr']    = $this->get_slider_data( $atts, array( 'unique' => $atts['unique'] ) );
			$atts['query_result']   = new WP_Query( greenpath_core_get_query_params( $atts ) );

			$atts['this_shortcode'] = $this;

			return greenpath_core_get_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'templates/content', $atts['behavior'], $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-testimonials-list';
			$holder_classes[] = isset( $atts['skin'] ) && ! empty( $atts['skin'] ) ? 'qodef-skin--' . $atts['skin'] : '';
			$holder_classes[] = ! empty( $atts['centered_content'] ) && 'yes' === $atts['centered_content'] ? 'qodef-centered-content' : '';
			$holder_classes[] = ! empty( $atts['text_alternate_style'] ) && 'yes' === $atts['text_alternate_style'] ? 'qodef-text-style--alternate' : '';
			$holder_classes[] = ! empty( $atts['retina_scaling'] ) && 'yes' === $atts['retina_scaling'] ? 'qodef-retina-scaling--' . $atts['retina_scaling'] : '';

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

		private function get_item_classes( $atts ) {
			$item_classes = $this->init_item_classes();

			$list_item_classes = $this->get_list_item_classes( $atts );

			$item_classes = array_merge( $item_classes, $list_item_classes );

			return implode( ' ', $item_classes );
		}

		public function get_title_styles( $atts ) {
			$styles = array();

			if ( '' !== $atts['title_margin_top'] ) {
				if ( qode_framework_string_ends_with_space_units( $atts['title_margin_top'] ) ) {
					$styles[] = 'margin-top: ' . $atts['title_margin_top'];
				} else {
					$styles[] = 'margin-top: ' . intval( $atts['title_margin_top'] ) . 'px';
				}
			}

			return $styles;
		}

		private function get_text_styles( $atts ) {
			$styles = array();

			if ( '' !== $atts['text_margin_top'] ) {
				if ( qode_framework_string_ends_with_space_units( $atts['text_margin_top'] ) ) {
					$styles[] = 'margin-top: ' . $atts['text_margin_top'];
				} else {
					$styles[] = 'margin-top: ' . intval( $atts['text_margin_top'] ) . 'px';
				}
			}

			return $styles;
		}

		private function get_author_styles( $atts ) {
			$styles = array();

			if ( '' !== $atts['author_margin_top'] ) {
				if ( qode_framework_string_ends_with_space_units( $atts['author_margin_top'] ) ) {
					$styles[] = 'margin-top: ' . $atts['author_margin_top'];
				} else {
					$styles[] = 'margin-top: ' . intval( $atts['author_margin_top'] ) . 'px';
				}
			}

			return $styles;
		}
	}
}
