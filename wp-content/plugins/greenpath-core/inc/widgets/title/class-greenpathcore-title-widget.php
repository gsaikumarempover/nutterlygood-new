<?php

if ( ! function_exists( 'greenpath_core_add_title_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function greenpath_core_add_title_widget( $widgets ) {
		$widgets[] = 'GreenPathCore_Title_Widget';

		return $widgets;
	}

	add_filter( 'greenpath_core_filter_register_widgets', 'greenpath_core_add_title_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathCore_Title_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_base( 'greenpath_core_title_widget' );
			$this->set_name( esc_html__( 'Nutterlygood Title', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Add title element into widget areas', 'greenpath-core' ) );
			$this->set_widget_option(
				array(
					'field_type'    => 'text',
					'name'          => 'title',
					'title'         => esc_html__( 'Title', 'greenpath-core' ),
					'default_value' => esc_html__( 'Title', 'greenpath-core' ),
				)
			);
			$this->set_widget_option(
				array(
					'field_type' => 'select',
					'name'       => 'title_tag',
					'title'      => esc_html__( 'Title Tag', 'greenpath-core' ),
					'options'    => greenpath_core_get_select_type_options_pool( 'title_tag' ),
				)
			);
			$this->set_widget_option(
				array(
					'field_type'    => 'select',
					'name'          => 'title_align',
					'title'         => esc_html__( 'Title Align', 'greenpath-core' ),
					'options'       => array(
						'left'   => esc_html__( 'Left', 'greenpath-core' ),
						'center' => esc_html__( 'Center', 'greenpath-core' ),
						'right'  => esc_html__( 'Right', 'greenpath-core' ),
					),
					'default_value' => 'left',
				)
			);
			$this->set_widget_option(
				array(
					'field_type' => 'text',
					'name'       => 'margin_bottom',
					'title'      => esc_html__( 'Bottom Margin', 'greenpath-core' ),
				)
			);
		}

		public function render( $atts ) {
			$title        = $atts['title'];
			$title_tag    = ! empty( $atts['title_tag'] ) ? $atts['title_tag'] : 'h5';
			$title_styles = $this->get_title_styles( $atts );
			?>
			<?php if ( ! empty( $title ) ) : ?>
				<?php echo '<' . greenpath_core_escape_title_tag( $title_tag ); ?> class="qodef-widget-title" <?php qode_framework_inline_style( $title_styles ); ?>>
				<?php echo esc_html( $title ); ?>
				<?php echo '</' . greenpath_core_escape_title_tag( $title_tag ); ?>>
			<?php endif; ?>
			<?php
		}

		public function get_title_styles( $atts ) {
			$styles = array();

			$margin_bottom = $atts['margin_bottom'];
			if ( ! empty( $margin_bottom ) ) {
				if ( qode_framework_string_ends_with_space_units( $margin_bottom ) ) {
					$styles[] = 'margin-bottom: ' . $margin_bottom;
				} else {
					$styles[] = 'margin-bottom: ' . intval( $margin_bottom ) . 'px';
				}
			}

			$title_align = $atts['title_align'];
			if ( ! empty( $title_align ) ) {
				$styles[] = 'text-align: ' . $title_align;
			}

			return implode( ';', $styles );
		}
	}
}
