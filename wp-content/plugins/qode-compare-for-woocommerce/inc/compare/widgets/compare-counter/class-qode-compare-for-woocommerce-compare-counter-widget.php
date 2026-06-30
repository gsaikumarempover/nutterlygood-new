<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_compare_for_woocommerce_add_compare_counter_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function qode_compare_for_woocommerce_add_compare_counter_widget( $widgets ) {
		$widgets[] = 'Qode_Compare_For_WooCommerce_Compare_Counter_Widget';

		return $widgets;
	}

	add_filter( 'qode_compare_for_woocommerce_filter_register_widgets', 'qode_compare_for_woocommerce_add_compare_counter_widget' );
}

if ( class_exists( 'Qode_Compare_For_WooCommerce_Framework_Widget' ) ) {
	class Qode_Compare_For_WooCommerce_Compare_Counter_Widget extends Qode_Compare_For_WooCommerce_Framework_Widget {

		public function map_widget() {
			$this->set_base( 'qode_compare_for_woocommerce_compare_counter' );
			$this->set_name( esc_html__( 'QODE Compare Counter', 'qode-compare-for-woocommerce' ) );
			$this->set_description( esc_html__( 'Display a text with the count of numbers in comparison', 'qode-compare-for-woocommerce' ) );
			$this->set_widget_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'qode-compare-for-woocommerce' ),
				)
			);
			$this->set_widget_option(
				array(
					'field_type'    => 'select',
					'name'          => 'layout',
					'title'         => esc_html__( 'Layout', 'qode-compare-for-woocommerce' ),
					'options'       => array(
						'icon'            => esc_html__( 'Icon Only', 'qode-compare-for-woocommerce' ),
						'with-icon'       => esc_html__( 'Icon With Text', 'qode-compare-for-woocommerce' ),
						'top-right-count' => esc_html__( 'Textual', 'qode-compare-for-woocommerce' ),
					),
					'default_value' => 'icon',
				)
			);
			$this->set_widget_option(
				array(
					'field_type'  => 'text',
					'name'        => 'widget_margin',
					'title'       => esc_html__( 'Margin', 'qode-compare-for-woocommerce' ),
					'description' => esc_html__( 'Insert margin in format: top right bottom left', 'qode-compare-for-woocommerce' ),
				)
			);
		}

		public function render( $atts ) {
			// Get user compare items.
			$compare_items = qode_compare_for_woocommerce_get_comparison_items_by_table();

			$classes = array( 'qcfw-compare-counter', 'qcfw-m', 'qcfw-widget' );

			if ( ! empty( $atts['custom_class'] ) ) {
				$classes[] = esc_attr( $atts['custom_class'] );
			}

			if ( ! empty( $compare_items ) ) {
				$classes[] = 'qcfw-items--has';
			} else {
				$classes[] = 'qcfw-items--no';
			}

			$layout = ! empty( $atts['layout'] ) ? $atts['layout'] : 'icon';

			if ( ! empty( $layout ) ) {
				$classes[] = 'qcfw-layout--' . $layout;
			}

			$styles = array();

			if ( '' !== $atts['widget_margin'] ) {
				$styles[] = 'margin: ' . esc_attr( $atts['widget_margin'] );
			}
			$params = array(
				'compare_items' => $compare_items,
				'opener_url'    => apply_filters( 'qode_compare_for_woocommerce_filter_widget_compare_button_url', '#' ),
			);
			?>
			<div <?php qode_compare_for_woocommerce_class_attribute( apply_filters( 'qode_compare_for_woocommerce_filter_compare_counter_widget_holder_classes', $classes ) ); ?> <?php qode_compare_for_woocommerce_inline_style( $styles ); ?>>
				<div class="qcfw-m-inner">
					<?php qode_compare_for_woocommerce_template_part( 'compare', 'widgets/compare-counter/templates/content', $layout, $params ); ?>
				</div>
			</div>
			<?php
		}
	}
}
