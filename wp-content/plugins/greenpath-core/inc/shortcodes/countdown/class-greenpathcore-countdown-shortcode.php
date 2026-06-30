<?php

if ( ! function_exists( 'greenpath_core_add_countdown_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function greenpath_core_add_countdown_shortcode( $shortcodes ) {
		$shortcodes[] = 'GreenPathCore_Countdown_Shortcode';

		return $shortcodes;
	}

	add_filter( 'greenpath_core_filter_register_shortcodes', 'greenpath_core_add_countdown_shortcode' );
}

if ( class_exists( 'GreenPathCore_Shortcode' ) ) {
	class GreenPathCore_Countdown_Shortcode extends GreenPathCore_Shortcode {

		public function __construct() {
			$this->set_layouts( apply_filters( 'greenpath_core_filter_countdown_layouts', array() ) );

			parent::__construct();
		}

		public function map_shortcode() {
			$this->set_shortcode_path( GREENPATH_CORE_SHORTCODES_URL_PATH . '/countdown' );
			$this->set_base( 'greenpath_core_countdown' );
			$this->set_name( esc_html__( 'Countdown', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Shortcode that displays countdown with provided parameters', 'greenpath-core' ) );

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
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'date',
					'name'        => 'date',
					'title'       => esc_html__( 'Date', 'greenpath-core' ),
					'description' => esc_html__( 'Enter date in format Y/m/d H:i:s', 'greenpath-core' ), //because of the wpbackery
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'week_label',
					'title'      => esc_html__( 'Week Label', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'week_label_plural',
					'title'      => esc_html__( 'Week Label Plural', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'day_label',
					'title'      => esc_html__( 'Day Label', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'day_label_plural',
					'title'      => esc_html__( 'Day Label Plural', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'hour_label',
					'title'      => esc_html__( 'Hour Label', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'hour_label_plural',
					'title'      => esc_html__( 'Hour Label Plural', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'minute_label',
					'title'      => esc_html__( 'Minute Label', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'minute_label_plural',
					'title'      => esc_html__( 'Minute Label Plural', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'second_label',
					'title'      => esc_html__( 'Second Label', 'greenpath-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'second_label_plural',
					'title'      => esc_html__( 'Second Label Plural', 'greenpath-core' ),
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
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['data_attrs']     = $this->get_data_attrs( $atts );
			$atts['holder_classes'] = $this->get_holder_classes( $atts );

			return greenpath_core_get_template_part( 'shortcodes/countdown', 'variations/' . $atts['layout'] . '/templates/countdown', '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-countdown';
			$holder_classes[] = 'qodef-show--5';

			$holder_classes[] = ! empty( $atts['skin'] ) ? 'qodef-countdown--' . $atts['skin'] : '';
			$holder_classes[] = ! empty( $atts['layout'] ) ? 'qodef-layout--' . $atts['layout'] : '';

			return implode( ' ', $holder_classes );
		}

		private function get_data_attrs( $atts ) {
			$data = array();

			if ( ! empty( $atts['date'] ) ) {
				$date              = $atts['date'];
				$date_formatted    = gmdate( 'Y/m/d H:i:s', strtotime( $date ) );
				$data['data-date'] = $date_formatted;
			}

			$date_formats = array(
				'week'   => array(
					'default' => esc_html__( 'Week', 'greenpath-core' ),
					'plural'  => esc_html__( 'Weeks', 'greenpath-core' ),
				),
				'day'    => array(
					'default' => esc_html__( 'Day', 'greenpath-core' ),
					'plural'  => esc_html__( 'Days', 'greenpath-core' ),
				),
				'hour'   => array(
					'default' => esc_html__( 'Hour', 'greenpath-core' ),
					'plural'  => esc_html__( 'Hours', 'greenpath-core' ),
				),
				'minute' => array(
					'default' => esc_html__( 'Minute', 'greenpath-core' ),
					'plural'  => esc_html__( 'Minutes', 'greenpath-core' ),
				),
				'second' => array(
					'default' => esc_html__( 'Second', 'greenpath-core' ),
					'plural'  => esc_html__( 'Seconds', 'greenpath-core' ),
				),
			);

			foreach ( $date_formats as $key => $value ) {
				if ( ! empty( $atts[$key . '_label'] ) ) {
					$data['data-' . $key . '-label'] = $atts[$key . '_label'];
				} else {
					$data['data-' . $key . '-label'] = $value['default'];
				}

				if ( ! empty( $atts[$key . '_label_plural'] ) ) {
					$data['data-' . $key . '-label-plural'] = $atts[$key . '_label_plural'];
				} else {
					$data['data-' . $key . '-label-plural'] = $value['plural'];
				}
			}

			return $data;
		}
	}
}
