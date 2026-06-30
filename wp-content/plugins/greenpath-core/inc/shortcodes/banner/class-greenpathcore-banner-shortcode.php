<?php

if ( ! function_exists( 'greenpath_core_add_banner_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function greenpath_core_add_banner_shortcode( $shortcodes ) {
		$shortcodes[] = 'GreenPathCore_Banner_Shortcode';

		return $shortcodes;
	}

	add_filter( 'greenpath_core_filter_register_shortcodes', 'greenpath_core_add_banner_shortcode' );
}

if ( class_exists( 'GreenPathCore_Shortcode' ) ) {
	class GreenPathCore_Banner_Shortcode extends GreenPathCore_Shortcode {

		public function __construct() {
			$this->set_layouts( apply_filters( 'greenpath_core_filter_banner_layouts', array() ) );
			$this->set_extra_options( apply_filters( 'greenpath_core_filter_banner_extra_options', array() ) );

			parent::__construct();
		}

		public function map_shortcode() {
			$this->set_shortcode_path( GREENPATH_CORE_SHORTCODES_URL_PATH . '/banner' );
			$this->set_base( 'greenpath_core_banner' );
			$this->set_name( esc_html__( 'Banner', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Shortcode that adds banner element', 'greenpath-core' ) );
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
					'field_type'    => 'select',
					'name'          => 'vertical_align',
					'title'         => esc_html__( 'Vertical Align', 'greenpath-core' ),
					'options'       => array(
						'top'    => esc_html__( 'Top', 'greenpath-core' ),
						'center' => esc_html__( 'Center', 'greenpath-core' ),
						'bottom' => esc_html__( 'Bottom', 'greenpath-core' ),
					),
					'default_value' => 'top',
					'dependency'    => array(
						'show' => array(
							'layout' => array(
								'values'        => 'link-button',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'hide_content',
					'title'         => esc_html__( 'Hide Content', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'yes_no' ),
					'default_value' => 'no',
					'dependency'    => array(
						'show' => array(
							'layout' => array(
								'values'        => 'link-overlay',
								'default_value' => '',
							),
						),
					),
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
					'field_type' => 'text',
					'name'       => 'link_url',
					'title'      => esc_html__( 'Link', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'link_target',
					'title'         => esc_html__( 'Link Target', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'link_target' ),
					'default_value' => '_self',
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'content_padding',
					'title'      => esc_html__( 'Content Padding', 'greenpath-core' ),
					'group'      => esc_html__( 'Style', 'greenpath-core' ),
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
					'field_type'  => 'text',
					'name'        => 'line_break_positions',
					'title'       => esc_html__( 'Positions of Line Break', 'greenpath-core' ),
					'description' => esc_html__( 'Enter the positions of the words after which you would like to create a line break. Separate the positions with commas (e.g. if you would like the first, third, and fourth word to have a line break, you would enter "1,3,4")', 'greenpath-core' ),
					'group'       => esc_html__( 'Title Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'disable_title_break_words',
					'title'         => esc_html__( 'Disable Title Line Break', 'greenpath-core' ),
					'description'   => esc_html__( 'Enabling this option will disable title line breaks for screen size 1200 and lower', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes', false ),
					'default_value' => 'no',
					'group'         => esc_html__( 'Title Style', 'greenpath-core' ),
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
					'field_type' => 'text',
					'name'       => 'subtitle',
					'title'      => esc_html__( 'Subtitle', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'subtitle_tag',
					'title'         => esc_html__( 'Subtitle Tag', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'title_tag' ),
					'default_value' => 'h5',
					'group'         => esc_html__( 'Subtitle Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'subtitle_color',
					'title'      => esc_html__( 'Subtitle Color', 'greenpath-core' ),
					'group'      => esc_html__( 'Subtitle Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'subtitle_margin_top',
					'title'      => esc_html__( 'Subtitle Margin Top', 'greenpath-core' ),
					'group'      => esc_html__( 'Subtitle Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'subtitle_predefined_style',
					'title'         => esc_html__( 'Subtitle Predefined Style', 'greenpath-core' ),
					'description'   => esc_html__( 'Overrides the default subtitle style with a predefined font style', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes', false ),
					'default_value' => 'no',
					'group'         => esc_html__( 'Subtitle Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'textarea',
					'name'       => 'text_field',
					'title'      => esc_html__( 'Text', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'text_tag',
					'title'         => esc_html__( 'Text Tag', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'title_tag' ),
					'default_value' => 'p',
					'group'         => esc_html__( 'Text Style', 'greenpath-core' ),
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
					'name'       => 'image_border_radius',
					'title'      => esc_html__( 'Image Border Radius', 'greenpath-core' ),
					'group'      => esc_html__( 'Image Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'image',
					'name'       => 'stamp',
					'title'      => esc_html__( 'Stamp', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'stamp_top_position',
					'title'      => esc_html__( 'Stamp Top Position', 'greenpath-core' ),
					'group'      => esc_html__( 'Stamp Style', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'layout' => array(
								'values'        => 'link-overlay',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'stamp_left_position',
					'title'      => esc_html__( 'Stamp Left Position', 'greenpath-core' ),
					'group'      => esc_html__( 'Stamp Style', 'greenpath-core' ),
					'dependency' => array(
						'show' => array(
							'layout' => array(
								'values'        => 'link-overlay',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->import_shortcode_options(
				array(
					'shortcode_base'    => 'greenpath_core_button',
					'exclude'           => array( 'custom_class', 'link', 'target' ),
					'additional_params' => array(
						'nested_group' => esc_html__( 'Button', 'greenpath-core' ),

					),
					'dependency'        => array(
						'show' => array(
							'layout' => array(
								'values'        => 'link-button',
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'content_padding_680',
					'title'      => esc_html__( 'Content Padding - 680px', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive content padding for screen size 680', 'greenpath-core' ),
					'group'      => esc_html__( 'Screen Size 680 Style', 'greenpath-core' ),
				)
			);

			$this->map_extra_options();
		}

		public static function call_shortcode( $params ) {
			$html = qode_framework_call_shortcode( 'greenpath_core_banner', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['unique_class']   = 'qodef-banner-' . rand( 0, 1000 );
			$atts['holder_classes']  = $this->get_holder_classes( $atts );
			$atts['title']           = $this->get_modified_title( $atts );
			$atts['title_styles']    = $this->get_title_styles( $atts );
			$atts['subtitle_styles'] = $this->get_subtitle_styles( $atts );
			$atts['content_styles']  = $this->get_content_styles( $atts );
			$atts['text_styles']     = $this->get_text_styles( $atts );
			$atts['image_styles']    = $this->get_image_styles( $atts );
			$atts['stamp_styles']    = $this->get_stamp_styles( $atts );
			$atts['button_params']   = $this->generate_button_params( $atts );
			$this->set_responsive_content_styles( $atts );

			return greenpath_core_get_template_part( 'shortcodes/banner', 'variations/' . $atts['layout'] . '/templates/' . $atts['layout'], '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-banner';
			$holder_classes[] = $atts['unique_class'];
			$holder_classes[] = ! empty( $atts['layout'] ) ? 'qodef-layout--' . $atts['layout'] : '';
			$holder_classes[] = ! empty( $atts['vertical_align'] ) ? 'qodef-vertical-layout--' . $atts['vertical_align'] : '';
			$holder_classes[] = ! empty( $atts['subtitle_predefined_style'] ) ? 'qodef-subtitle--predefined' : '';
			$holder_classes[] = 'yes' === $atts['disable_title_break_words'] ? 'qodef-title-break--disabled' : '';

			return implode( ' ', $holder_classes );
		}

		private function get_modified_title( $atts ) {
			$title = $atts['title'];

			if ( ! empty( $title ) && ! empty( $atts['line_break_positions'] ) ) {
				$split_title          = explode( ' ', $title );
				$line_break_positions = explode( ',', str_replace( ' ', '', $atts['line_break_positions'] ) );

				foreach ( $line_break_positions as $position ) {
					$position = intval( $position );
					if ( isset( $split_title[ $position - 1 ] ) && ! empty( $split_title[ $position - 1 ] ) ) {
						$split_title[ $position - 1 ] = $split_title[ $position - 1 ] . '<br />';
					}
				}

				$title = implode( ' ', $split_title );
			}

			return $title;
		}

		private function get_title_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['title_margin_top'] ) ) {
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

		private function get_subtitle_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['subtitle_margin_top'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['subtitle_margin_top'] ) ) {
					$styles[] = 'margin-top: ' . $atts['subtitle_margin_top'];
				} else {
					$styles[] = 'margin-top: ' . intval( $atts['subtitle_margin_top'] ) . 'px';
				}
			}

			if ( ! empty( $atts['subtitle_color'] ) ) {
				$styles[] = 'color: ' . $atts['subtitle_color'];
			}

			return $styles;
		}

		private function get_text_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['text_margin_top'] ) ) {
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

		private function get_content_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['content_padding'] ) ) {
				$styles[] = 'padding: ' . $atts['content_padding'];
			}

			return $styles;
		}

		private function get_image_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['image_border_radius'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['image_border_radius'] ) ) {
					$styles[] = '--qode-banner-image-border-radius: ' . $atts['image_border_radius'];
				} else {
					$styles[] = '--qode-banner-image-border-radius: ' . intval( $atts['image_border_radius'] ) . 'px';
				}
			}

			return $styles;
		}

		private function get_stamp_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['stamp_top_position'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['stamp_top_position'] ) ) {
					$styles[] = '--qode-banner-stamp-top-position: ' . $atts['stamp_top_position'];
				} else {
					$styles[] = '--qode-banner-stamp-top-position: ' . intval( $atts['stamp_top_position'] ) . 'px';
				}
			}

			if ( ! empty( $atts['stamp_left_position'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['stamp_left_position'] ) ) {
					$styles[] = '--qode-banner-stamp-left-position: ' . $atts['stamp_left_position'];
				} else {
					$styles[] = '--qode-banner-stamp-left-position: ' . intval( $atts['stamp_left_position'] ) . 'px';
				}
			}

			return $styles;
		}

		private function generate_button_params( $atts ) {
			$params = $this->populate_imported_shortcode_atts(
				array(
					'shortcode_base' => 'greenpath_core_button',
					'exclude'        => array( 'custom_class', 'link', 'target' ),
					'atts'           => $atts,
				)
			);

			$params['link']   = ! empty( $atts['link_url'] ) ? $atts['link_url'] : '';
			$params['target'] = ! empty( $atts['link_target'] ) ? $atts['link_target'] : '';

			return $params;
		}

		private function set_responsive_content_styles( $atts ) {
			$unique_class = '.' . $atts['unique_class'] . ' .qodef-m-content-inner';
			$screen_sizes = array( '680' );
			$option_keys  = array( 'padding' );

			foreach ( $screen_sizes as $screen_size ) {
				$styles = array();

				foreach ( $option_keys as $option_key ) {
					$option_value = $atts[ 'content_' . $option_key . '_' . $screen_size ];
					$style_key    = str_replace( '_', '-', $option_key );

					if ( '' !== $option_value ) {
						if ( qode_framework_string_ends_with_typography_units( $option_value ) ) {
							$styles[ $style_key ] = $option_value . '!important';
						} else {
							$styles[ $style_key ] = intval( $option_value ) . 'px !important';
						}
					}
				}

				if ( ! empty( $styles ) ) {
					add_filter(
						'greenpath_core_filter_add_responsive_' . $screen_size . '_inline_style_in_footer',
						function ( $style ) use ( $unique_class, $styles ) {
							$style .= qode_framework_dynamic_style( $unique_class, $styles );

							return $style;
						}
					);
				}
			}
		}
	}
}
