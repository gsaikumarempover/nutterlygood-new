<?php

if ( ! function_exists( 'greenpath_core_add_image_gallery_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function greenpath_core_add_image_gallery_shortcode( $shortcodes ) {
		$shortcodes[] = 'GreenPathCore_Image_Gallery_Shortcode';

		return $shortcodes;
	}

	add_filter( 'greenpath_core_filter_register_shortcodes', 'greenpath_core_add_image_gallery_shortcode' );
}

if ( class_exists( 'GreenPathCore_List_Shortcode' ) ) {
	class GreenPathCore_Image_Gallery_Shortcode extends GreenPathCore_List_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( GREENPATH_CORE_SHORTCODES_URL_PATH . '/image-gallery' );
			$this->set_base( 'greenpath_core_image_gallery' );
			$this->set_name( esc_html__( 'Image Gallery', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Shortcode that adds image gallery element', 'greenpath-core' ) );
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
			$this->set_option(
				array(
					'field_type' => 'image',
					'name'       => 'images',
					'multiple'   => 'yes',
					'title'      => esc_html__( 'Images', 'greenpath-core' ),
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
					'field_type'    => 'select',
					'name'          => 'enable_border',
					'title'         => esc_html__( 'Enable Border', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'default_value' => 'no',
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
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'enable_box_shadow',
					'title'         => esc_html__( 'Enable Box Shadow', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'default_value' => 'no',
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
			$this->map_list_options(
				array(
					'exclude_behavior'      => array( 'justified-gallery' ),
					'exclude_option'        => array( 'images_proportion' ),
					'group'                 => esc_html__( 'Gallery Settings', 'greenpath-core' ),
					'include_slider_option' => array(
						'slider_direction',
						'slider_hidden_slides',
						'slider_mousewheel_navigation',
						'slider_centered_slides',
						'slider_drag_cursor',
					),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'enable_instagram_display',
					'title'         => esc_html__( 'Enable Instagram Display', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'yes_no', false ),
					'default_value' => 'no',
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'enable_scroll',
					'title'         => esc_html__( 'Enable Move On Scroll', 'greenpath-core' ),
					'options'       => greenpath_core_get_select_type_options_pool( 'no_yes', false ),
					'dependency'    => array(
						'show' => array(
							'behavior' => array(
								'values'        => 'columns',
								'default_value' => 'columns'
							)
						),
					),
				)
			);
		}

		public static function call_shortcode( $params ) {
			$html = qode_framework_call_shortcode( 'greenpath_core_image_gallery', $params );
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

			$atts['unique']         = $this->set_navigation_position( $atts );
			$atts['holder_classes'] = $this->get_holder_classes( $atts );
			$atts['holder_styles']  = $this->get_holder_styles( $atts );
			$atts['item_classes']   = $this->get_item_classes( $atts );
			$atts['slider_attr']    = $this->get_slider_data( $atts );
			$atts['images']         = $this->generate_images_params( $atts );

			return greenpath_core_get_template_part( 'shortcodes/image-gallery', 'templates/image-gallery', $atts['behavior'], $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-image-gallery';
			$holder_classes[] = ! empty( $atts['image_action'] ) && 'open-popup' === $atts['image_action'] ? 'qodef-magnific-popup qodef-popup-gallery' : '';
			$holder_classes[] = ! empty( $atts['enable_box_shadow'] ) && 'yes' === $atts['enable_box_shadow'] ? 'qodef-image-gallery--box-shadow' : '';
			$holder_classes[] = ! empty( $atts['enable_instagram_display'] ) && 'yes' === $atts['enable_instagram_display'] ? 'qodef-image-gallery-instagram-style' : '';
			$holder_classes[] = ( 'yes' === $atts['enable_scroll'] ) ? 'qodef-scroll-gallery' : '';

			$list_classes   = $this->get_list_classes( $atts );
			$holder_classes = array_merge( $holder_classes, $list_classes );

			return implode( ' ', $holder_classes );
		}

		private function get_holder_styles( $atts ) {
			$holder_styles = array();

			if ( ! empty( $atts['border_width'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['border_width'] ) ) {
					$holder_styles[] = '--qode-image-gallery-border-width: ' . $atts['border_width'];
				} else {
					$holder_styles[] = '--qode-image-gallery-border-width: ' . $atts['border_width'] . 'px';
				}
			}

			if ( ! empty( $atts['border_style'] ) ) {
				$holder_styles[] = '--qode-image-gallery-border-style: ' . $atts['border_style'];
			}

			if ( ! empty( $atts['border_color'] ) ) {
				$holder_styles[] = '--qode-image-gallery-border-color: ' . $atts['border_color'];
			}

			if ( ! empty( $atts['border_radius'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['border_radius'] ) ) {
					$holder_styles[] = '--qode-image-gallery-border-radius: ' . $atts['border_radius'];
				} else {
					$holder_styles[] = '--qode-image-gallery-border-radius: ' . $atts['border_radius'] . 'px';
				}
			}

			$list_styles   = $this->get_list_styles( $atts );
			$holder_styles = array_merge( $holder_styles, $list_styles );

			return $holder_styles;
		}

		public function get_item_classes( $atts ) {
			$item_classes   = $this->init_item_classes();
			$item_classes[] = 'qodef-image-wrapper';

			$list_item_classes = $this->get_list_item_classes( $atts );

			$item_classes = array_merge( $item_classes, $list_item_classes );

			return implode( ' ', $item_classes );
		}

		private function generate_images_params( $atts ) {
			$image_ids = array();
			$images    = array();
			$i         = 0;

			if ( ! empty( $atts['images'] ) ) {
				$image_ids = explode( ',', $atts['images'] );
			}

			$image_size = $this->generate_image_size( $atts );

			foreach ( $image_ids as $id ) {
				if ( is_array( wp_get_attachment_image_src( $id ) ) ) {
					$image['image_id']   = intval( $id );
					$image_original      = wp_get_attachment_image_src( $id, 'full' );
					$image['url']        = $this->generate_image_url( $id, $atts, $image_original[0] );
					$image['alt']        = get_post_meta( $id, '_wp_attachment_image_alt', true );
					$image['image_size'] = $image_size;

					$images[ $i ] = $image;
					$i ++;
				}
			}

			return $images;
		}

		private function generate_image_size( $atts ) {
			$image_size = trim( $atts['image_size'] );
			preg_match_all( '/\d+/', $image_size, $matches ); /* check if numeral width and height are entered */
			if ( in_array( $image_size, array( 'thumbnail', 'thumb', 'medium', 'large', 'full' ), true ) ) {
				return $image_size;
			} elseif ( ! empty( $matches[0] ) ) {
				return array(
					$matches[0][0],
					$matches[0][1],
				);
			} else {
				return 'full';
			}
		}

		private function generate_image_url( $id, $atts, $default ) {
			if ( 'custom-link' === $atts['image_action'] ) {
				$url = get_post_meta( $id, 'qodef_image_gallery_custom_link', true );
				if ( empty( $url ) ) {
					$url = $default;
				}
			} else {
				$url = $default;
			}

			return $url;
		}

		public function set_navigation_position( $atts ) {
			$unique = '';

			if ( ! empty( $atts['slider_navigation_position'] ) && 'outside' === $atts['slider_navigation_position'] ) {
				$unique = wp_unique_id();
			}

			return $unique;
		}
	}
}
