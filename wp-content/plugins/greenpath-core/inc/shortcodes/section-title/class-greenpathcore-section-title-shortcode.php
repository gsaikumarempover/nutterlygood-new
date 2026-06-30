<?php

if ( ! function_exists( 'greenpath_core_add_section_title_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function greenpath_core_add_section_title_shortcode( $shortcodes ) {
		$shortcodes[] = 'GreenPathCore_Section_Title_Shortcode';

		return $shortcodes;
	}

	add_filter( 'greenpath_core_filter_register_shortcodes', 'greenpath_core_add_section_title_shortcode' );
}

if ( class_exists( 'GreenPathCore_Shortcode' ) ) {
	class GreenPathCore_Section_Title_Shortcode extends GreenPathCore_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( GREENPATH_CORE_SHORTCODES_URL_PATH . '/section-title' );
			$this->set_base( 'greenpath_core_section_title' );
			$this->set_name( esc_html__( 'Section Title', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Shortcode that adds section title element', 'greenpath-core' ) );
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'greenpath-core' ),
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
					'default_value' => 'h2',
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
					'name'       => 'link',
					'title'      => esc_html__( 'Title Custom Link', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'target',
					'title'         => esc_html__( 'Custom Link Target', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'link_target' ),
					'default_value' => '_self',
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
					'name'       => 'content_alignment',
					'title'      => esc_html__( 'Content Alignment', 'greenpath-core' ),
					'options'    => array(
						''       => esc_html__( 'Default', 'greenpath-core' ),
						'left'   => esc_html__( 'Left', 'greenpath-core' ),
						'center' => esc_html__( 'Center', 'greenpath-core' ),
						'right'  => esc_html__( 'Right', 'greenpath-core' ),
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'text_font_size',
					'title'      => esc_html__( 'Text Font Size', 'greenpath-core' ),
					'group'      => esc_html__( 'Text Style', 'greenpath-core' ),
					'args'       => array(
						'suffix' => esc_html__( 'px', 'greenpath-core' ),
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'text_line_height',
					'title'      => esc_html__( 'Text Line Height', 'greenpath-core' ),
					'group'      => esc_html__( 'Text Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'text_font_size_1512',
					'title'       => esc_html__( 'Text Font Size', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1512', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1512 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'text_line_height_1512',
					'title'       => esc_html__( 'Text Line Height', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1512', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1512 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'text_font_size_1200',
					'title'       => esc_html__( 'Text Font Size', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1200', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1200 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'text_line_height_1200',
					'title'       => esc_html__( 'Text Line Height', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 1200', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 1200 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'text_font_size_880',
					'title'       => esc_html__( 'Text Font Size', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 880', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 880 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'text_line_height_880',
					'title'       => esc_html__( 'Text Line Height', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 880', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 880 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'text_font_size_680',
					'title'       => esc_html__( 'Text Font Size', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 680', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 680 Style', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'text_line_height_680',
					'title'       => esc_html__( 'Text Line Height', 'greenpath-core' ),
					'description' => esc_html__( 'Set responsive style value for screen size 680', 'greenpath-core' ),
					'group'       => esc_html__( 'Screen Size 680 Style', 'greenpath-core' ),
				)
			);
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['unique_class']   = 'qodef-section-title-' . rand( 0, 1000 );
			$atts['holder_classes'] = $this->get_holder_classes( $atts );
			$atts['title']          = $this->get_modified_title( $atts );
			$atts['title_styles']   = $this->get_title_styles( $atts );
			$atts['text_styles']    = $this->get_text_styles( $atts );
			$atts['this_shortcode'] = $this;
			$this->set_responsive_text_styles( $atts );

			return greenpath_core_get_template_part( 'shortcodes/section-title', 'templates/section-title', '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-section-title';
			$holder_classes[] = $atts['unique_class'];
			$holder_classes[] = ! empty( $atts['content_alignment'] ) ? 'qodef-alignment--' . $atts['content_alignment'] : 'qodef-alignment--left';
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

			if ( ! empty( $atts['text_font_size'] ) ) {
				if ( qode_framework_string_ends_with_typography_units( $atts['text_font_size'] ) ) {
					$styles[] = 'font-size: ' . $atts['text_font_size'];
				} else {
					$styles[] = 'font-size: ' . intval( $atts['text_font_size'] ) . 'px';
				}
			}

			if ( ! empty( $atts['text_line_height'] ) ) {
				if ( qode_framework_string_ends_with_typography_units( $atts['text_line_height'] ) ) {
					$styles[] = 'line-height: ' . $atts['text_line_height'];
				} else {
					$styles[] = 'line-height: ' . intval( $atts['text_line_height'] ) . 'px';
				}
			}

			return $styles;
		}

		private function set_responsive_text_styles( $atts ) {
			$unique_class = '.' . $atts['unique_class'] . ' .qodef-m-text';
			$screen_sizes = array( '1512', '1200', '880', '680' );
			$option_keys  = array( 'font_size', 'line_height' );

			foreach ( $screen_sizes as $screen_size ) {
				$styles = array();

				foreach ( $option_keys as $option_key ) {
					$option_value = $atts[ 'text_' . $option_key . '_' . $screen_size ];
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
