<?php

if ( ! function_exists( 'greenpath_core_add_swapping_image_gallery_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function greenpath_core_add_swapping_image_gallery_shortcode( $shortcodes ) {
		$shortcodes[] = 'GreenPathCore_Swapping_Image_Gallery_Shortcode';

		return $shortcodes;
	}

	add_filter( 'greenpath_core_filter_register_shortcodes', 'greenpath_core_add_swapping_image_gallery_shortcode' );
}

if ( class_exists( 'GreenPathCore_Shortcode' ) ) {
	class GreenPathCore_Swapping_Image_Gallery_Shortcode extends GreenPathCore_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( GREENPATH_CORE_SHORTCODES_URL_PATH . '/swapping-image-gallery' );
			$this->set_base( 'greenpath_core_swapping_image_gallery' );
			$this->set_name( esc_html__( 'Swapping Image Gallery', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Shortcode that displays swapping image gallery element', 'greenpath-core' ) );
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
					'field_type'    => 'text',
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
					'name'       => 'border_radius',
					'title'      => esc_html__( 'Border Radius', 'greenpath-core' ),
					'group'      => esc_html__( 'Layout', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'repeater',
					'name'       => 'children',
					'title'      => esc_html__( 'Child elements', 'greenpath-core' ),
					'items'      => array(
						array(
							'field_type' => 'text',
							'name'       => 'item_title',
							'title'      => esc_html__( 'Title', 'greenpath-core' ),
						),
						array(
							'field_type' => 'image',
							'name'       => 'item_icon',
							'title'      => esc_html__( 'Icon', 'greenpath-core' ),
							'multiple'   => 'no',
						),
						array(
							'field_type' => 'image',
							'name'       => 'item_hover_icon',
							'title'      => esc_html__( 'Hover Icon', 'greenpath-core' ),
							'multiple'   => 'no',
						),
						array(
							'field_type' => 'image',
							'name'       => 'item_image',
							'title'      => esc_html__( 'Image', 'greenpath-core' ),
							'multiple'   => 'no',
						),
					),
				)
			);
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['holder_classes'] = $this->get_holder_classes( $atts );
			$atts['title_styles']   = $this->get_title_styles( $atts );
			$atts['text_styles']    = $this->get_text_styles( $atts );
			$atts['item_styles']    = $this->get_item_styles( $atts );
			$atts['items']          = $this->parse_repeater_items( $atts['children'] );
			$atts['this_shortcode'] = $this;

			return greenpath_core_get_template_part( 'shortcodes/swapping-image-gallery', 'templates/swapping-image-gallery', '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-swapping-image-gallery';

			return implode( ' ', $holder_classes );
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

			if ( ! empty( $atts['text_color'] ) ) {
				$styles[] = 'color: ' . $atts['text_color'];
			}

			return $styles;
		}

		private function get_item_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['border_radius'] ) ) {
				$styles[] = '--qode-swapping-image-gallery-border-radius: ' . $atts['border_radius'];
			}

			return $styles;
		}
	}
}
