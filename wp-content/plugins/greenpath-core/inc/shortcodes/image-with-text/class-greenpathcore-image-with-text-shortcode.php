<?php

if ( ! function_exists( 'greenpath_core_add_image_with_text_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function greenpath_core_add_image_with_text_shortcode( $shortcodes ) {
		$shortcodes[] = 'GreenPathCore_Image_With_Text_Shortcode';

		return $shortcodes;
	}

	add_filter( 'greenpath_core_filter_register_shortcodes', 'greenpath_core_add_image_with_text_shortcode' );
}

if ( class_exists( 'GreenPathCore_Shortcode' ) ) {
	class GreenPathCore_Image_With_Text_Shortcode extends GreenPathCore_Shortcode {

		public function __construct() {
			$this->set_layouts( apply_filters( 'greenpath_core_filter_image_with_text_layouts', array() ) );
			$this->set_extra_options( apply_filters( 'greenpath_core_filter_image_with_text_extra_options', array() ) );

			parent::__construct();
		}

		public function map_shortcode() {
			$this->set_shortcode_path( GREENPATH_CORE_SHORTCODES_URL_PATH . '/image-with-text' );
			$this->set_base( 'greenpath_core_image_with_text' );
			$this->set_name( esc_html__( 'Image With Text', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Shortcode that adds image with text element', 'greenpath-core' ) );
			$this->set_scripts(
				array(
					'jquery-magnific-popup' => array(
						'registered' => true,
					),
				)
			);
			$this->set_necessary_styles(
				array(
					'magnific-popup' => array(
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

			$options_map = greenpath_core_get_variations_options_map( $this->get_layouts() );

			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'layout',
					'title'         => esc_html__( 'Layout', 'greenpath-core' ),
					'options'       => $this->get_layouts(),
					'default_value' => $options_map['default_value'],
					'visibility'    => array( 'map_for_page_builder' => $options_map['visibility'] ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'image',
					'name'       => 'image',
					'title'      => esc_html__( 'Image', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'image_size',
					'title'       => esc_html__( 'Image Size', 'greenpath-core' ),
					'description' => esc_html__( 'For predefined image sizes input thumbnail, medium, large or full. If you wish to set a custom image size, type in the desired image dimensions in pixels (e.g. 400x400).', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'retina_scaling',
					'title'         => esc_html__( 'Enable Retina Scaling', 'greenpath-core' ),
					'description'   => esc_html__( 'Image uploaded should be two times the height.', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes' ),
					'default_value' => '',
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'image_action',
					'title'      => esc_html__( 'Image Action', 'greenpath-core' ),
					'options'    => array(
						''            => esc_html__( 'No Action', 'greenpath-core' ),
						'open-popup'  => esc_html__( 'Open Popup', 'greenpath-core' ),
						'custom-link' => esc_html__( 'Custom Link', 'greenpath-core' ),
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'link',
					'title'      => esc_html__( 'Custom Link', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'image_action' => array(
								'values'        => 'custom-link',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'target',
					'title'         => esc_html__( 'Custom Link Target', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'link_target' ),
					'default_value' => '_self',
					'dependency'    => array(
						'show' => array(
							'image_action' => array(
								'values'        => 'custom-link',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'image',
					'name'       => 'hover_image',
					'title'      => esc_html__( 'Hover Image', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'text',
					'name'          => 'title',
					'title'         => esc_html__( 'Title', 'greenpath-core' ),
					'default_value' => esc_html__( 'Title Text', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'title_tag',
					'title'         => esc_html__( 'Title Tag', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'title_tag' ),
					'default_value' => 'h4',
					'group'         => esc_html__( 'Title Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'title_color',
					'title'      => esc_html__( 'Title Color', 'greenpath-core' ),
					'group'      => esc_html__( 'Title Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'title_margin_top',
					'title'      => esc_html__( 'Title Margin Top', 'greenpath-core' ),
					'group'      => esc_html__( 'Title Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'title_text_align',
					'title'      => esc_html__( 'Title Text Align', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_align' ),
					'group'      => esc_html__( 'Title Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'textarea',
					'name'          => 'text',
					'title'         => esc_html__( 'Text', 'greenpath-core' ),
					'default_value' => esc_html__( 'Contrary to popular belief, Lorem Ipsum is not simply random text.', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'text_color',
					'title'      => esc_html__( 'Text Color', 'greenpath-core' ),
					'group'      => esc_html__( 'Text Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'text_margin_top',
					'title'      => esc_html__( 'Text Margin Top', 'greenpath-core' ),
					'group'      => esc_html__( 'Text Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'text_align',
					'title'      => esc_html__( 'Text Align', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_align' ),
					'group'      => esc_html__( 'Text Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'enable_border',
					'title'         => esc_html__( 'Enable Border', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'default_value' => 'no',
					'group'      => esc_html__( 'Image Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'border_width',
					'title'      => esc_html__( 'Border Width', 'greenpath-core' ),
					'dependency'    => array(
						'show' => array(
							'enable_border' => array(
								'values'        => 'yes',
								'default_value' => '',
							),
						),
					),
					'group'      => esc_html__( 'Image Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'border_style',
					'title'      => esc_html__( 'Border Style', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'border_style', false ),
					'dependency'    => array(
						'show' => array(
							'enable_border' => array(
								'values'        => 'yes',
								'default_value' => '',
							),
						),
					),
					'group'      => esc_html__( 'Image Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'border_color',
					'title'      => esc_html__( 'Border Color', 'greenpath-core' ),
					'dependency'    => array(
						'show' => array(
							'enable_border' => array(
								'values'        => 'yes',
								'default_value' => '',
							),
						),
					),
					'group'      => esc_html__( 'Image Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'border_radius',
					'title'      => esc_html__( 'Border Radius', 'greenpath-core' ),
					'dependency'    => array(
						'show' => array(
							'enable_border' => array(
								'values'        => 'yes',
								'default_value' => '',
							),
						),
					),
					'group'      => esc_html__( 'Image Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'image_alignment',
					'title'      => esc_html__( 'Image Alignment', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_align', true ),
					'group'      => esc_html__( 'Image Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'enable_hover',
					'title'         => esc_html__( 'Enable Predefined Hover', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes', false ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'enable_hover_icon',
					'title'         => esc_html__( 'Enable Predefined Hover Icon', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'dependency'    => array(
						'show' => array(
							'enable_hover' => array(
								'values'        => 'yes',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'enable_appear',
					'title'         => esc_html__( 'Enable Appear', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes', false ),
				)
			);
			$this->map_extra_options();
		}

		public static function call_shortcode( $params ) {
			$html = qode_framework_call_shortcode( 'greenpath_core_image_with_text', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function load_assets() {
			$atts = $this->get_atts();

			if ( isset( $atts['image_action'] ) && 'open-popup' === $atts['image_action'] ) {
				wp_enqueue_style( 'magnific-popup' );
				wp_enqueue_script( 'jquery-magnific-popup' );
			}
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['holder_classes']     = $this->get_holder_classes( $atts );
			$atts['image_styles']       = $this->get_image_styles( $atts );
			$atts['title_styles']       = $this->get_title_styles( $atts );
			$atts['text_styles']        = $this->get_text_styles( $atts );
			$atts['image_params']       = $this->generate_image_params( $atts );
			$atts['hover_image_params'] = $this->generate_hover_image_params( $atts );

			return greenpath_core_get_template_part( 'shortcodes/image-with-text', 'variations/' . $atts['layout'] . '/templates/' . $atts['layout'], '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-image-with-text';
			$holder_classes[] = ! empty( $atts['layout'] ) ? 'qodef-layout--' . $atts['layout'] : '';
			$holder_classes[] = ( 'yes' === $atts['retina_scaling'] ) ? 'qodef--retina' : '';
			$holder_classes[] = ! empty( $atts['image_alignment'] ) ? 'qodef-image-align--' . $atts['image_alignment'] : '';
			$holder_classes[] = ( 'yes' === $atts['enable_appear']) ? 'qodef--has-appear' : '';
			$holder_classes[] = ( 'yes' === $atts['enable_hover']) ? 'qodef--has-hover' : '';

			return implode( ' ', $holder_classes );
		}

		private function get_title_styles( $atts ) {
			$styles = array();

			if ( '' !== $atts['title_margin_top'] ) {
				if ( qode_framework_string_ends_with_space_units( $atts['title_margin_top'] ) ) {
					$styles[] = 'margin-top: ' . $atts['title_margin_top'];
				} else {
					$styles[] = 'margin-top: ' . intval( $atts['title_margin_top'] ) . 'px';
				}
			}

			if ( ! empty( $atts['title_color'] ) ) {
				$styles[] = 'color: ' . $atts['title_color'];
			}

			if ( ! empty( $atts['title_text_align'] ) ) {
				$styles[] = 'text-align: ' . $atts['title_text_align'];
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

			if ( ! empty( $atts['text_color'] ) ) {
				$styles[] = 'color: ' . $atts['text_color'];
			}

			if ( ! empty( $atts['text_align'] ) ) {
				$styles[] = 'text-align: ' . $atts['text_align'];
			}

			return $styles;
		}

		private function get_image_styles( $atts ) {
			$image_styles = array();

			if ( ! empty( $atts['border_width'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['border_width'] ) ) {
					$image_styles[] = '--qode-image-with-text-border-width: ' . $atts['border_width'];
				} else {
					$image_styles[] = '--qode-image-with-text-border-width: ' . $atts['border_width'] . 'px';
				}
			}

			if ( ! empty( $atts['border_style'] ) ) {
				$image_styles[] = '--qode-image-with-text-border-style: ' . $atts['border_style'];
			}

			if ( ! empty( $atts['border_color'] ) ) {
				$image_styles[] = '--qode-image-with-text-border-color: ' . $atts['border_color'];
			}

			if ( ! empty( $atts['border_radius'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['border_radius'] ) ) {
					$image_styles[] = '--qode-image-with-text-border-radius: ' . $atts['border_radius'];
				} else {
					$image_styles[] = '--qode-image-with-text-border-radius: ' . $atts['border_radius'] . 'px';
				}
			}

			return $image_styles;
		}

		private function generate_image_params( $atts ) {
			$image = array();

			if ( ! empty( $atts['image'] ) ) {
				$id = $atts['image'];

				if ( is_array( wp_get_attachment_image_src( $id ) ) ) {
					$image['image_id'] = intval( $id );
					$image_original    = wp_get_attachment_image_src( $id, 'full' );
					$image['url']      = $image_original[0];
					$image['alt']      = get_post_meta( $id, '_wp_attachment_image_alt', true );

					$image_size = trim( $atts['image_size'] );
					preg_match_all( '/\d+/', $image_size, $matches ); /* check if numeral width and height are entered */
					if ( in_array( $image_size, array( 'thumbnail', 'thumb', 'medium', 'large', 'full' ), true ) ) {
						$image['image_size'] = $image_size;
					} elseif ( ! empty( $matches[0] ) ) {
						$image['image_size'] = array(
							$matches[0][0],
							$matches[0][1],
						);
					} else {
						$image['image_size'] = 'full';
					}
				}
			}

			return $image;
		}

		private function generate_hover_image_params( $atts ) {
			$image = array();

			if ( ! empty( $atts['hover_image'] ) ) {
				$id = $atts['hover_image'];

				if ( is_array( wp_get_attachment_image_src( $id ) ) ) {
					$image['image_id'] = intval( $id );
					$image_original    = wp_get_attachment_image_src( $id, 'full' );
					$image['url']      = $image_original[0];
					$image['alt']      = get_post_meta( $id, '_wp_attachment_image_alt', true );

					$image_size = trim( $atts['image_size'] );
					preg_match_all( '/\d+/', $image_size, $matches ); /* check if numeral width and height are entered */
					if ( in_array( $image_size, array( 'thumbnail', 'thumb', 'medium', 'large', 'full' ), true ) ) {
						$image['image_size'] = $image_size;
					} elseif ( ! empty( $matches[0] ) ) {
						$image['image_size'] = array(
							$matches[0][0],
							$matches[0][1],
						);
					} else {
						$image['image_size'] = 'full';
					}
				}
			}

			return $image;
		}
	}
}
