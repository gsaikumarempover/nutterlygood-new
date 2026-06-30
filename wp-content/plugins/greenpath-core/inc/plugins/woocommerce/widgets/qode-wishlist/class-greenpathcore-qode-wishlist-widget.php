<?php

if ( ! function_exists( 'greenpath_core_add_qode_wishlist_widget' ) ) {
	function greenpath_core_add_qode_wishlist_widget( $widgets ) {
		$widgets[] = 'GreenPathCore_Qode_Wishlist_Widget';

		return $widgets;
	}

	add_filter( 'greenpath_core_filter_register_widgets', 'greenpath_core_add_qode_wishlist_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathCore_Qode_Wishlist_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_base( 'greenpath_core_qode_wishlist' );
			$this->set_name( esc_html__( 'Nutterlygood Qode Wishlist', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Add Qode Wishlist counter to widget areas', 'greenpath-core' ) );
			$this->set_widget_option(
				array(
					'field_type'  => 'text',
					'name'        => 'widget_margin',
					'title'       => esc_html__( 'Margin', 'greenpath-core' ),
					'description' => esc_html__( 'Insert margin in format: top right bottom left (e.g. 10px 5px 10px 5px)', 'greenpath-core' ),
				)
			);
			$this->set_widget_option(
				array(
					'field_type' => 'color',
					'name'       => 'icon_color',
					'title'      => esc_html__( 'Icon Color', 'greenpath-core' ),
				)
			);
		}

		public function render( $atts ) {
			$styles      = array();
			$icon_styles = array();

			if ( ! empty( $atts['widget_margin'] ) ) {
				$styles[] = 'margin: ' . $atts['widget_margin'];
			}
			if ( ! empty( $atts['icon_color'] ) ) {
				$icon_styles[] = 'color: ' . $atts['icon_color'];
			}

			$wishlist_url = function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_page_url' )
				? qode_wishlist_for_woocommerce_get_wishlist_page_url()
				: '';

			$items = function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_items_by_table' )
				? qode_wishlist_for_woocommerce_get_wishlist_items_by_table( 'default' )
				: array();

			$number_of_items = is_array( $items ) ? count( $items ) : 0;
			?>

			<div class="qodef-wishlist-widget-holder">
				<div class="qodef-wishlist-inner" <?php qode_framework_inline_style( $styles ); ?>>
					<a href="<?php echo esc_url( $wishlist_url ); ?>" class="qodef-wishlist-widget-link" title="<?php echo esc_attr__( 'View Wishlist', 'greenpath-core' ); ?>">
						<span class="qodef-wishlist-icon-count-holder" <?php qode_framework_inline_style( $icon_styles ); ?>>
							<span class="qodef-wishlist-widget-icon"><?php echo greenpath_core_get_svg_icon( 'heart' ); ?></span>
							<span class="qodef-wishlist-count"><?php echo esc_html( $number_of_items ); ?></span>
						</span>
					</a>
				</div>
			</div>
			<?php
		}
	}
}