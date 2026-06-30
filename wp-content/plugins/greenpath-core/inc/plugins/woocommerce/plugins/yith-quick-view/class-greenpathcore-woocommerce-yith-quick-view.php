<?php

if ( ! class_exists( 'GreenPathCore_WooCommerce_YITH_Quick_View' ) ) {
	class GreenPathCore_WooCommerce_YITH_Quick_View {
		private static $instance;

		public function __construct() {

			if ( qode_framework_is_installed( 'yith-quick-view' ) ) {
				// Init
				add_action( 'init', array( $this, 'init' ), 15 );
			}
		}

		/**
		 * @return GreenPathCore_WooCommerce_YITH_Quick_View
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function init() {

			// Unset default templates modules
			$this->unset_templates_modules();

			// Add new WooCommerce templates
			$this->add_templates();

			// Change default templates position
			$this->change_templates_position();

			// Override default templates
			$this->override_templates();
		}

		function unset_templates_modules() {

			// Remove Quick View button element on shop pages
			remove_action( 'woocommerce_after_shop_loop_item', array( YITH_WCQV_Frontend(), 'yith_add_quick_view_button' ), 15 );

			// Remove Quick View button element on wishlist page
			remove_action( 'yith_wcwl_table_after_product_name', array( YITH_WCQV_Frontend(), 'add_quick_view_button_wishlist' ), 15 );
		}

		function add_templates() {

			// Add additional tags around product image and content
			add_action( 'yith_wcqv_product_image', 'greenpath_add_product_single_content_holder', 2 ); // permission 2 is set because woocommerce_show_product_sale_flash hook is added on 10
			add_action( 'yith_wcqv_product_image', 'greenpath_add_product_single_content_holder_end', 25 ); // permission 32 is set because woocommerce_show_product_images hook is added on 20

			// Add additional tags around product list item image
			add_action( 'yith_wcqv_product_image', 'greenpath_add_product_single_image_holder', 5 ); // permission 5 is set because woocommerce_show_product_sale_flash hook is added on 10
			add_action( 'yith_wcqv_product_summary', 'greenpath_add_product_single_image_holder_end', 42 ); // permission 42 is set because woocommerce_show_product_images hook is added on 30

			if ( qode_framework_is_installed( 'yith-wishlist' ) || qode_framework_is_installed( 'yith-compare' ) ) {
				// Add YITH buttons wrapper
				add_action( 'yith_wcqv_product_summary', 'greenpath_add_product_single_additional_buttons_holder', 26 );
				add_action( 'yith_wcqv_product_summary', 'greenpath_add_product_single_additional_buttons_holder_end', 29 );
			}

			if( qode_framework_is_installed( 'yith-wishlist' ) ) {
				add_action( 'yith_wcqv_product_summary', 'greenpath_core_get_yith_wishlist_shortcode', 27 );
			}

			if( qode_framework_is_installed( 'yith-compare' ) ) {
				global $yith_woocompare;

				if ( $yith_woocompare->is_frontend() ) {

					add_action(
						'yith_wcqv_product_summary',
						array(
							$yith_woocompare->obj,
							'add_compare_link',
						),
						28
					);
				}
			}

			// Add social share
			if( qode_framework_is_installed( 'theme' ) && 'yes' === greenpath_core_get_post_value_through_levels( 'qodef_woo_enable_social_share' ) ) {
				add_action( 'yith_wcqv_product_summary', 'greenpath_woo_product_render_social_share_html', 35 );
			}
		}

		function change_templates_position() {

			// Add button element for shop pages
			add_action( 'greenpath_action_product_list_item_additional_content', array( YITH_WCQV_Frontend(), 'yith_add_quick_view_button' ) );
			add_action( 'greenpath_core_action_product_list_item_additional_content', array( YITH_WCQV_Frontend(), 'yith_add_quick_view_button' ) );
			add_action( 'greenpath_core_action_product_list_item_quick_view', array( YITH_WCQV_Frontend(), 'yith_add_quick_view_button' ) );
		}

		function override_templates() {

			// Override product title
			remove_action( 'yith_wcqv_product_summary', 'woocommerce_template_single_title', 5 ); // permission 5 is default
			add_action( 'yith_wcqv_product_summary', 'greenpath_core_yith_quick_view_single_title', 5 );
		}
	}

	GreenPathCore_WooCommerce_YITH_Quick_View::get_instance();
}
