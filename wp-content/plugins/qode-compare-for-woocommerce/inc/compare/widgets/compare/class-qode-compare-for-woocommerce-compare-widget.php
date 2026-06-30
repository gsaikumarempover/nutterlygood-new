<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_compare_for_woocommerce_add_compare_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function qode_compare_for_woocommerce_add_compare_widget( $widgets ) {
		$widgets[] = 'Qode_Compare_For_WooCommerce_Compare_Widget';

		return $widgets;
	}

	add_filter( 'qode_compare_for_woocommerce_filter_register_widgets', 'qode_compare_for_woocommerce_add_compare_widget' );
}

if ( class_exists( 'Qode_Compare_For_WooCommerce_Framework_Widget' ) ) {
	class Qode_Compare_For_WooCommerce_Compare_Widget extends Qode_Compare_For_WooCommerce_Framework_Widget {

		public function map_widget() {
			$this->set_base( 'qode_compare_for_woocommerce_compare' );
			$this->set_name( esc_html__( 'QODE Compare', 'qode-compare-for-woocommerce' ) );
			$this->set_description( esc_html__( 'Display a compare list that shows all items you added to comparison', 'qode-compare-for-woocommerce' ) );
			$this->set_widget_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'qode-compare-for-woocommerce' ),
				)
			);
			$this->set_widget_option(
				array(
					'field_type'  => 'text',
					'name'        => 'widget_margin',
					'title'       => esc_html__( 'Widget Margin', 'qode-compare-for-woocommerce' ),
					'description' => esc_html__( 'Insert margin in format: top right bottom left', 'qode-compare-for-woocommerce' ),
				)
			);
		}

		public function render( $atts ) {
			// Get user compare items.
			$compare_items = qode_compare_for_woocommerce_get_comparison_items_by_table();

			$classes = array( 'qcfw-compare', 'qcfw-m', 'qcfw-widget' );

			if ( ! empty( $atts['custom_class'] ) ) {
				$classes[] = esc_attr( $atts['custom_class'] );
			}

			if ( ! empty( $compare_items ) ) {
				$classes[] = 'qcfw-items--has';
			} else {
				$classes[] = 'qcfw-items--no';
			}

			$styles = array();

			if ( '' !== $atts['widget_margin'] ) {
				$styles[] = 'margin: ' . esc_attr( $atts['widget_margin'] );
			}
			$params = array(
				'compare_items' => $compare_items,
				'opener_url'    => apply_filters( 'qode_compare_for_woocommerce_filter_widget_compare_button_url', '#' ),
				'show_compare'  => apply_filters( 'qode_compare_for_woocommerce_filter_compare_widget_show_compare_button', true, get_the_ID() ),
			);

			$data                     = array();
			$data['data-widget-atts'] = wp_json_encode( stripslashes_deep( array( 'show_compare' => $params['show_compare'] ) ) );
			?>

			<div <?php qode_compare_for_woocommerce_class_attribute( apply_filters( 'qode_compare_for_woocommerce_filter_compare_widget_holder_classes', $classes ) ); ?> <?php qode_compare_for_woocommerce_get_inline_style( $styles ); ?> <?php qode_compare_for_woocommerce_inline_attrs( $data ); ?>>
				<div class="qcfw-m-inner">
					<?php qode_compare_for_woocommerce_template_part( 'compare', 'widgets/compare/templates/content', '', $params ); ?>
				</div>
			</div>
			<?php
		}
	}
}
