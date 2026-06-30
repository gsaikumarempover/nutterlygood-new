<?php

if ( ! function_exists( 'greenpath_core_add_woo_side_area_cart_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function greenpath_core_add_woo_side_area_cart_widget( $widgets ) {
		$widgets[] = 'GreenPathCore_WooCommerce_Side_Area_Cart_Widget';

		return $widgets;
	}

	add_filter( 'greenpath_core_filter_register_widgets', 'greenpath_core_add_woo_side_area_cart_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathCore_WooCommerce_Side_Area_Cart_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_base( 'greenpath_core_woo_side_area_cart' );
			$this->set_name( esc_html__( 'Nutterlygood WooCommerce Side Area Cart', 'greenpath-core' ) );
			$this->set_description( esc_html__( 'Display a shop cart icon with that shows products count that are in the cart', 'greenpath-core' ) );
		}

		public function load_assets() {
			wp_enqueue_style( 'perfect-scrollbar', GREENPATH_CORE_URL_PATH . 'assets/plugins/perfect-scrollbar/perfect-scrollbar.css', array() );
			wp_enqueue_script( 'perfect-scrollbar', GREENPATH_CORE_URL_PATH . 'assets/plugins/perfect-scrollbar/perfect-scrollbar.jquery.min.js', array( 'jquery' ), false, true );
		}

		public function widget( $args, $instance ) {
			// Prevent widgets from loading on WooCommerce cart page
			if ( greenpath_core_is_woo_page( 'cart' ) ) {
				return;
			}
			parent::widget( $args, $instance );
		}

		public function render( $atts ) {
			?>
			<div class="qodef-widget-side-area-cart-inner">
				<?php greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/parts/opener' ); ?>
				<?php greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/content' ); ?>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'greenpath_core_woo_side_area_cart_add_to_cart_fragment' ) ) {
	/**
	 * Function that return|update new cart content for products which are added into the cart
	 *
	 * @param array $fragments
	 *
	 * @return array
	 */
	function greenpath_core_woo_side_area_cart_add_to_cart_fragment( $fragments ) {
		ob_start();
		?>
		<div class="qodef-widget-side-area-cart-inner">
			<?php greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/parts/opener' ); ?>
			<?php greenpath_core_template_part( 'plugins/woocommerce/widgets/side-area-cart', 'templates/content' ); ?>
		</div>
		<?php
		$fragments['.qodef-widget-side-area-cart-inner'] = ob_get_clean();

		return $fragments;
	}

	add_filter( 'woocommerce_add_to_cart_fragments', 'greenpath_core_woo_side_area_cart_add_to_cart_fragment' );
}
