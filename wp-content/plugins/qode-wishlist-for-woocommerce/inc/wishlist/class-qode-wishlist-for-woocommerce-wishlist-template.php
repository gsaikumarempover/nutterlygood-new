<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Wishlist_For_WooCommerce_Wishlist_Template' ) ) {
	class Qode_Wishlist_For_WooCommerce_Wishlist_Template {
		private static $instance;

		public function __construct() {

			// Create page template (Permission 3 is set in order to trigger before options init 5).
			add_action( 'after_setup_theme', array( $this, 'create_page_template' ), 3 );

			// Add a post display state for special WC pages in the page list table.
			add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 10, 2 );

			// Add page template classes.
			add_filter( 'body_class', array( $this, 'add_body_class' ) );
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Wishlist_For_WooCommerce_Wishlist_Template
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function create_page_template() {
			$get_page_id    = get_option( QODE_WISHLIST_FOR_WOOCOMMERCE_PAGE_TEMPLATE );
			$creating_count = (int) get_option( QODE_WISHLIST_FOR_WOOCOMMERCE_PAGE_TEMPLATE . '_count', 0 );

			if ( empty( $get_page_id ) && $creating_count < 3 ) {
				$page_template_args = array(
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'post_name'      => 'wishlist',
					'post_title'     => esc_attr__( 'Wishlist', 'qode-wishlist-for-woocommerce' ),
					'post_content'   => '<!-- wp:shortcode -->[qode_wishlist_for_woocommerce_table]<!-- /wp:shortcode -->',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
				);

				$the_page_id = wp_insert_post( $page_template_args );

				if ( ! is_wp_error( $the_page_id ) ) {
					update_option( QODE_WISHLIST_FOR_WOOCOMMERCE_PAGE_TEMPLATE, $the_page_id );

					flush_rewrite_rules();
				}

				$creating_count++;

				update_option( QODE_WISHLIST_FOR_WOOCOMMERCE_PAGE_TEMPLATE . '_count', $creating_count );
			}
		}

		public function add_display_post_states( $post_states, $post ) {
			$wishlist_page_id = qode_wishlist_for_woocommerce_get_wishlist_page_id();

			if ( $wishlist_page_id === (int) $post->ID ) {
				$post_states['qwfw-wishlist'] = esc_attr__( 'Wishlist Page', 'qode-wishlist-for-woocommerce' );
			}

			return $post_states;
		}

		public function add_body_class( $classes ) {
			$wishlist_page_id = qode_wishlist_for_woocommerce_get_wishlist_page_id();

			if ( ! empty( $wishlist_page_id ) && is_page( $wishlist_page_id ) ) {
				$classes[] = 'woocommerce';
				$classes[] = 'woocommerce-page';
				$classes[] = 'qwfw-wishlist-page';
			}

			return $classes;
		}
	}

	Qode_Wishlist_For_WooCommerce_Wishlist_Template::get_instance();
}
