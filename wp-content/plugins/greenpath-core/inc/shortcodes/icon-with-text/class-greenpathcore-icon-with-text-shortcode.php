<?php

if ( ! function_exists( 'greenpath_core_add_icon_with_text_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function greenpath_core_add_icon_with_text_shortcode( $shortcodes ) {
		$shortcodes[] = 'GreenPathCore_Icon_With_Text_Shortcode';

		return $shortcodes;
	}

	add_filter( 'greenpath_core_filter_register_shortcodes', 'greenpath_core_add_icon_with_text_shortcode' );
}

if ( class_exists( 'GreenPathCore_Shortcode' ) ) {
	class GreenPathCore_Icon_With_Text_Shortcode extends GreenPathCore_Shortcode {

		public function __construct() {
			$this->set_layouts( apply_filters( 'greenpath_core_filter_icon_with_text_layouts', array() ) );

			$options_map   = greenpath_core_get_variations_options_map( $this->get_layouts() );
			$default_value = $options_map['default_value'];

			$this->set_extra_options( apply_filters( 'greenpath_core_filter_icon_with_text_extra_options', array(), $default_value ) );

			parent::__construct();
		}

		public function map_shortcode() {
			$this->set_shortcode_path( GREENPATH_CORE_SHORTCODES_URL_PATH . '/icon-with-text' );
			$this->set_base( 'greenpath_core_icon_with_text' );
			$this->set_name( esc_html__( 'Icon With Text', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Shortcode that adds icon with text element', 'greenpath-core' ) );
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
					'field_type'    => 'text',
					'name'          => 'link',
					'title'         => esc_html__( 'Link', 'greenpath-core' ),
					'default_value' => '',
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'target',
					'title'         => esc_html__( 'Link Target', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'link_target' ),
					'default_value' => '_self',
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'icon_type',
					'title'         => esc_html__( 'Icon Type', 'greenpath-core' ),
					'options'       => array(
						'icon-pack'   => esc_html__( 'Icon Pack', 'greenpath-core' ),
						'custom-icon' => esc_html__( 'Custom Icon', 'greenpath-core' ),
					),
					'default_value' => 'icon-pack',
					'group'         => esc_html__( 'Icon', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'image',
					'name'       => 'custom_icon',
					'title'      => esc_html__( 'Custom Icon', 'greenpath-core' ),
					'group'      => esc_html__( 'Icon', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'icon_type' => array(
								'values'        => 'custom-icon',
								'default_value' => 'icon-pack',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'retina_scaling',
					'title'         => esc_html__( 'Enable Retina Scaling', 'greenpath-core' ),
					'description'   => esc_html__( 'Image uploaded should be two times the height.', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes', false ),
					'default_value' => 'no',
					'group'         => esc_html__( 'Icon', 'greenpath-core' ),
					'dependency'    => array(
						'show' => array(
							'icon_type' => array(
								'values'        => 'custom-icon',
								'default_value' => 'icon-pack',
							),
						),
					),
				)
			);
			$this->import_shortcode_options(
				array(
					'shortcode_base'    => 'greenpath_core_icon',
					'exclude'           => array( 'custom_class', 'link', 'target' ),
					'additional_params' => array(
						'nested_group' => esc_html__( 'Icon', 'greenpath-core' ),
						'dependency'   => array(
							'show' => array(
								'icon_type' => array(
									'values'        => 'icon-pack',
									'default_value' => 'icon-pack',
								),
							),
						),
					),
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
					'default_value' => 'h3',
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
					'field_type' => 'text',
					'name'       => 'icon_margin',
					'title'      => esc_html__( 'Icon Right Margin', 'greenpath-core' ),
					'group'      => esc_html__( 'Icon Style', 'greenpath-core' ),
					'dependency'   => array(
						'show' => array(
							'layout' => array(
								'values'        => 'before-title',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->map_extra_options();
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['holder_classes'] = $this->get_holder_classes( $atts );
			$atts['image_styles']   = $this->get_image_styles( $atts );
			$atts['title_styles']   = $this->get_title_styles( $atts );
			$atts['text_styles']    = $this->get_text_styles( $atts );
			$atts['icon_styles']    = $this->get_icon_styles( $atts );
			$atts['icon_params']    = $this->generate_icon_params( $atts );

			return greenpath_core_get_template_part( 'shortcodes/icon-with-text', 'variations/' . $atts['layout'] . '/templates/' . $atts['layout'], '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-icon-with-text';
			$holder_classes[] = ! empty( $atts['layout'] ) ? 'qodef-layout--' . $atts['layout'] : '';
			$holder_classes[] = ! empty( $atts['icon_type'] ) ? 'qodef--' . $atts['icon_type'] : '';
			$holder_classes[] = ( 'yes' === $atts['retina_scaling'] ) ? 'qodef--retina' : '';

			$holder_classes = apply_filters( 'greenpath_core_filter_icon_with_text_variation_classes', $holder_classes, $atts );

			return implode( ' ', $holder_classes );
		}

		private function get_image_styles( $atts ) {
			$styles = array();

			if ( 'yes' === $atts['retina_scaling'] && ! empty( $atts['custom_icon'] ) ) {
				$image_meta = wp_get_attachment_metadata( $atts['custom_icon'] );

				if ( ! empty( $image_meta['width'] ) ) {
					$styles[] = 'width: ' . round( $image_meta['width'] / 2 ) . 'px';
				}
			}

			return $styles;
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

			return $styles;
		}

		private function get_icon_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['icon_margin'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['icon_margin'] ) ) {
					$styles[] = '--qode-icon-with-text-icon-margin: ' . $atts['icon_margin'];
				} else {
					$styles[] = '--qode-icon-with-text-icon-margin: ' . $atts['icon_margin'] . 'px';
				}
			}

			return $styles;
		}

		private function generate_icon_params( $atts ) {
			$params = $this->populate_imported_shortcode_atts(
				array(
					'shortcode_base' => 'greenpath_core_icon',
					'exclude'        => array( 'custom_class', 'link', 'target' ),
					'atts'           => $atts,
				)
			);

			return $params;
		}
	}
}
