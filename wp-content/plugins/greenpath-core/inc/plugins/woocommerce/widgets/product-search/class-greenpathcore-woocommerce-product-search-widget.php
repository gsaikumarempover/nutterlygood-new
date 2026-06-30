<?php

if ( ! function_exists( 'greenpath_core_add_woo_product_search_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function greenpath_core_add_woo_product_search_widget( $widgets ) {
		$widgets[] = 'GreenPathCore_WooCommerce_Product_Search_Widget';

		return $widgets;
	}

	add_filter( 'greenpath_core_filter_register_widgets', 'greenpath_core_add_woo_product_search_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class GreenPathCore_WooCommerce_Product_Search_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_base( 'greenpath_core_woo_product_search' );
			$this->set_name( esc_html__( 'Nutterlygood WooCommerce Product Search', 'greenpath-core' ) );
			$this->set_widget_option(
				array(
					'field_type'  => 'text',
					'name'        => 'widget_padding',
					'title'       => esc_html__( 'Widget Padding', 'greenpath-core' ),
					'description' => esc_html__( 'Insert padding in format: top right bottom left', 'greenpath-core' ),
				)
			);
		}

		public function load_assets() {
			wp_enqueue_style( 'perfect-scrollbar' );
			wp_enqueue_script( 'jquery-perfect-scrollbar' );
			wp_enqueue_script( 'bloodhound' );
			wp_enqueue_script( 'typeahead' );
		}

		public function render( $atts ) {
			$styles = array();
			
			?>
			<div class="qodef-widget-product-search-outer" <?php qode_framework_inline_style( $styles ); ?>>
				<div class="qodef-widget-product-search-inner">
					<?php greenpath_core_template_part( 'plugins/woocommerce/widgets/product-search', 'templates/content'); ?>
				</div>
			</div>
			<?php
		}
	}
}

if (!function_exists('greenpath_core_localize_product_list')) {
	function greenpath_core_localize_product_list() {
		
		$product_list = get_posts(array(
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'posts_per_page' => -1
		));
		
		$product_array = array();
		
		if (is_array($product_list) && count($product_list)) {
			$product_atts = array();
			
			foreach ($product_list as $product) {
				
				$_pf = new WC_Product_Factory();
				$_product = $_pf->get_product($product->ID);
				
				//product categories
				$product_cats = get_the_terms($product->ID, 'product_cat');
				$product_cat_slugs = array();
				
				if(!empty($product_cats)) {
					foreach ($product_cats as $product_cat) {
						$product_cat_slugs[] = $product_cat->slug;
					}
				}
				
				//product thumbnail link
				$product_thumb = get_the_post_thumbnail_url($product->ID);
				
				//product price
				$product_price = $_product->get_price_html();
				
				
				//set JS global variable attributes
				$product_atts['ID'] = $product->ID;
				$product_atts['post_title'] = $product->post_title;
				$product_atts['thumb'] = $product_thumb;
				$product_atts['price'] = $product_price;
				$product_atts['product_cat'] = $product_cat_slugs;
				
				
				$product_array[] = $product_atts;
			}
		}
		
		wp_localize_script('greenpath-main-js', 'qodefProductList', array(
			'products'       => $product_array,
		));
	}
	
	add_action('wp_enqueue_scripts', 'greenpath_core_localize_product_list', 11);
}
