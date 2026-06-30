<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Wishlist_For_WooCommerce_Privacy' ) ) {
	class Qode_Wishlist_For_WooCommerce_Privacy {
		private static $instance;

		public function __construct() {
			add_action( 'admin_init', array( $this, 'add_privacy_message' ) );
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Wishlist_For_WooCommerce_Privacy
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Adds the privacy policy message on privacy policy page.
		 */
		public function add_privacy_message() {
			if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
				$content = $this->get_privacy_message();

				if ( $content ) {
					$title = apply_filters( 'qode_wishlist_for_woocommerce_filter_privacy_policy_guide_title', _x( 'QODE Wishlist for WooCommerce', 'Privacy Policy Guide Title', 'qode-wishlist-for-woocommerce' ) );

					wp_add_privacy_policy_content( $title, $content );
				}
			}
		}

		/**
		 * Get the privacy policy message.
		 *
		 * @return string
		 */
		public function get_privacy_message() {
			$params = array(
				'sections' => $this->get_sections(),
			);

			return qode_wishlist_for_woocommerce_get_template_part( 'privacy/templates', 'content', '', $params );
		}

		/**
		 * Get the privacy policy sections.
		 *
		 * @return array
		 */
		public function get_sections() {
			return apply_filters(
				'qode_wishlist_for_woocommerce_filter_privacy_policy_content_sections',
				array(
					'general'           => array(
						'tutorial'    => _x( 'This sample language includes the basics around what personal data your store may be collecting, storing and sharing, as well as who may have access to that data. Depending on what settings are enabled and which additional plugins are used, the specific information shared by your store will vary. We recommend consulting with a lawyer when deciding what information to disclose on your privacy policy.', 'Privacy Policy Content', 'qode-wishlist-for-woocommerce' ),
						'description' => '',
					),
					'collect_and_store' => array(
						'title' => _x( 'What we collect and store', 'Privacy Policy Content', 'qode-wishlist-for-woocommerce' ),
					),
					'has_access'        => array(
						'title' => _x( 'Who on our team has access', 'Privacy Policy Content', 'qode-wishlist-for-woocommerce' ),
					),
					'share'             => array(
						'title' => _x( 'What we share with others', 'Privacy Policy Content', 'qode-wishlist-for-woocommerce' ),
					),
					'payments'          => array(
						'title' => _x( 'Payments', 'Privacy Policy Content', 'qode-wishlist-for-woocommerce' ),
					),
				)
			);
		}
	}

	Qode_Wishlist_For_WooCommerce_Privacy::get_instance();
}
