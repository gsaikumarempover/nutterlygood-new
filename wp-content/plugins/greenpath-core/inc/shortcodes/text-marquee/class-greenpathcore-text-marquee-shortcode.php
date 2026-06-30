<?php

if ( ! function_exists( 'greenpath_core_add_text_marquee_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function greenpath_core_add_text_marquee_shortcode( $shortcodes ) {
		$shortcodes[] = 'GreenPathCore_Text_Marquee_Shortcode';

		return $shortcodes;
	}

	add_filter( 'greenpath_core_filter_register_shortcodes', 'greenpath_core_add_text_marquee_shortcode' );
}

if ( class_exists( 'GreenPathCore_Shortcode' ) ) {
	class GreenPathCore_Text_Marquee_Shortcode extends GreenPathCore_Shortcode {

		public function __construct() {
			$this->set_layouts( apply_filters( 'greenpath_core_filter_text_marquee_layouts', array() ) );
			$this->set_extra_options( apply_filters( 'greenpath_core_filter_text_marquee_extra_options', array() ) );

			parent::__construct();
		}

		public function map_shortcode() {
			$this->set_shortcode_path( GREENPATH_CORE_SHORTCODES_URL_PATH . '/text-marquee' );
			$this->set_base( 'greenpath_core_text_marquee' );
			$this->set_name( esc_html__( 'Text Marquee', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Shortcode that adds Text Marquee element', 'greenpath-core' ) );
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
					'field_type' => 'text',
					'name'       => 'text_1',
					'title'      => esc_html__( 'Text 1', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'text_1_color',
					'title'      => esc_html__( 'Text 1 Color', 'greenpath-core' ),
					'group'      => esc_html__( 'Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'text_2',
					'title'      => esc_html__( 'Text 2', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'text_2_color',
					'title'      => esc_html__( 'Text 2 Color', 'greenpath-core' ),
					'group'      => esc_html__( 'Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'text_3',
					'title'      => esc_html__( 'Text 3', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'text_3_color',
					'title'      => esc_html__( 'Text 3 Color', 'greenpath-core' ),
					'group'      => esc_html__( 'Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'separator',
					'title'      => esc_html__( 'Separator', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'separator_color',
					'title'      => esc_html__( 'Separator Color', 'greenpath-core' ),
					'group'      => esc_html__( 'Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'direction',
					'title'         => esc_html__( 'Direction', 'greenpath-core' ),
					'options'       => array(
						'left'  => esc_html__( 'Left', 'greenpath-core' ),
						'right' => esc_html__( 'Right', 'greenpath-core' ),
					),
					'default_value' => 'left',
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'duration',
					'title'       => esc_html__( 'Duration (in seconds)', 'greenpath-core' ),
					'description' => esc_html__( 'Default value is 25s', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'font_size',
					'title'      => esc_html__( 'Font Size', 'greenpath-core' ),
					'group'      => esc_html__( 'Typography Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'line_height',
					'title'      => esc_html__( 'Line Height', 'greenpath-core' ),
					'group'      => esc_html__( 'Typography Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'letter_spacing',
					'title'      => esc_html__( 'Letter Spacing (px or em)', 'greenpath-core' ),
					'group'      => esc_html__( 'Typography Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'font_weight',
					'title'      => esc_html__( 'Font Weight', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'font_weight' ),
					'group'      => esc_html__( 'Typography Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'font_style',
					'title'      => esc_html__( 'Font Style', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'font_style' ),
					'group'      => esc_html__( 'Typography Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'text_transform',
					'title'      => esc_html__( 'Text Transform', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'text_transform' ),
					'group'      => esc_html__( 'Typography Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'separator_margin_top',
					'title'      => esc_html__( 'Separator Vertical Offset', 'greenpath-core' ),
					'group'      => esc_html__( 'Typography Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'font_size_1512',
					'title'       => esc_html__( 'Font Size', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1512', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1512 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'line_height_1512',
					'title'       => esc_html__( 'Line Height', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1512', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1512 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'letter_spacing_1512',
					'title'       => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1512', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1512 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'font_size_1368',
					'title'       => esc_html__( 'Font Size', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1368', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1368 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'line_height_1368',
					'title'       => esc_html__( 'Line Height', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1368', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1368 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'letter_spacing_1368',
					'title'       => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1368', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1368 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'font_size_1200',
					'title'       => esc_html__( 'Font Size', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1200', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1200 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'line_height_1200',
					'title'       => esc_html__( 'Line Height', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1200', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1200 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'letter_spacing_1200',
					'title'       => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1200', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1200 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'font_size_1024',
					'title'       => esc_html__( 'Font Size', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1024', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1024 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'line_height_1024',
					'title'       => esc_html__( 'Line Height', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1024', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1024 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'letter_spacing_1024',
					'title'       => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1024', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1024 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'font_size_880',
					'title'       => esc_html__( 'Font Size', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 880', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 880 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'line_height_880',
					'title'       => esc_html__( 'Line Height', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 880', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 880 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'letter_spacing_880',
					'title'       => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 880', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 880 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'font_size_680',
					'title'       => esc_html__( 'Font Size', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 680', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 680 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'line_height_680',
					'title'       => esc_html__( 'Line Height', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 680', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 680 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'letter_spacing_680',
					'title'       => esc_html__( 'Letter Spacing', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 680', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 680 Style', 'greenpath-core' ),
				)
			);
			$this->map_extra_options();
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['unique_class']           = 'qodef-text-marquee-' . rand( 0, 1000 );
			$atts['holder_classes']         = $this->get_holder_classes( $atts );
			$atts['holder_styles']          = $this->get_holder_styles( $atts );
			$atts['text_styles']            = $this->get_text_global_styles( $atts );
			$atts['text_individual_styles'] = $this->get_text_individual_styles( $atts );
			$this->set_responsive_styles( $atts );

			return greenpath_core_get_template_part( 'shortcodes/text-marquee', 'variations/' . $atts['layout'] . '/templates/' . $atts['layout'], '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-text-marquee';
			$holder_classes[] = $atts['unique_class'];
			$holder_classes[] = ! empty( $atts['layout'] ) ? 'qodef-layout--' . $atts['layout'] : '';
			$holder_classes[] = ! empty( $atts['direction'] ) ? 'qodef-direction--' . $atts['direction'] : 'qodef-direction--left';

			return implode( ' ', $holder_classes );
		}

		private function get_holder_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['duration'] ) ) {
				$styles[] = '--qode-marquee-duration:' . intval( $atts['duration'] ) . 's';
			}

			return $styles;
		}

		private function get_text_global_styles( $atts ) {
			$styles = array();

			$font_size = $atts['font_size'];
			if ( ! empty( $font_size ) ) {
				if ( qode_framework_string_ends_with_typography_units( $font_size ) ) {
					$styles[] = 'font-size: ' . $font_size;
				} else {
					$styles[] = 'font-size: ' . intval( $font_size ) . 'px';
				}
			}

			$line_height = $atts['line_height'];
			if ( ! empty( $line_height ) ) {
				if ( qode_framework_string_ends_with_typography_units( $line_height ) ) {
					$styles[] = 'line-height: ' . $line_height;
				} else {
					$styles[] = 'line-height: ' . intval( $line_height ) . 'px';
				}
			}

			$letter_spacing = $atts['letter_spacing'];
			if ( '' !== $letter_spacing ) {
				if ( qode_framework_string_ends_with_typography_units( $letter_spacing ) ) {
					$styles[] = 'letter-spacing: ' . $letter_spacing;
				} else {
					$styles[] = 'letter-spacing: ' . intval( $letter_spacing ) . 'px';
				}
			}

			if ( ! empty( $atts['font_weight'] ) ) {
				$styles[] = 'font-weight: ' . $atts['font_weight'];
			}

			if ( ! empty( $atts['font_style'] ) ) {
				$styles[] = 'font-style: ' . $atts['font_style'];
			}

			if ( ! empty( $atts['text_transform'] ) ) {
				$styles[] = 'text-transform: ' . $atts['text_transform'];
			}

			return $styles;
		}

		private function get_text_individual_styles( $atts ) {
			$styles              = array();
			$styles['first']     = array();
			$styles['second']    = array();
			$styles['third']     = array();
			$styles['separator'] = array();

			if ( ! empty( $atts['text_1_color'] ) ) {
				$styles['first'][] = 'color:' . $atts['text_1_color'];
			}

			if ( ! empty( $atts['text_2_color'] ) ) {
				$styles['second'][] = 'color:' . $atts['text_2_color'];
			}

			if ( ! empty( $atts['text_3_color'] ) ) {
				$styles['third'][] = 'color:' . $atts['text_3_color'];
			}

			if ( ! empty( $atts['separator_color'] ) ) {
				$styles['separator'][] = 'color:' . $atts['separator_color'];
			}

			if ( ! empty( $atts['separator_margin_top'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['separator_margin_top'], true ) ) {
					$styles['separator'][] = 'top:' . $atts['separator_margin_top'];
				} else {
					$styles['separator'][] = 'top:' . $atts['separator_margin_top'] . 'px';
				}
			}

			return $styles;
		}

		private function set_responsive_styles( $atts ) {
			$unique_class = '.' . $atts['unique_class'] . ' .qodef-m-content';
			$screen_sizes = array( '1512', '1368', '1200', '1024', '880', '680' );
			$option_keys  = array( 'font_size', 'line_height', 'letter_spacing' );

			foreach ( $screen_sizes as $screen_size ) {
				$styles = array();

				foreach ( $option_keys as $option_key ) {
					$option_value = $atts[$option_key . '_' . $screen_size];
					$style_key    = str_replace( '_', '-', $option_key );

					if ( '' !== $option_value ) {
						if ( qode_framework_string_ends_with_typography_units( $option_value ) ) {
							$styles[$style_key] = $option_value . '!important';
						} else {
							$styles[$style_key] = intval( $option_value ) . 'px !important';
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
