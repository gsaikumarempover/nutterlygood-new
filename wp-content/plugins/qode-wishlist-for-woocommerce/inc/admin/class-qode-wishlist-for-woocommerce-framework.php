<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Wishlist_For_WooCommerce_Framework' ) ) {
	class Qode_Wishlist_For_WooCommerce_Framework {
		private static $instance;

		public function __construct() {
			// Hook to include additional modules before plugin loaded.
			do_action( 'qode_wishlist_for_woocommerce_action_framework_before_framework_plugin_loaded' );

			$this->require_core();

			// Make plugin available for other plugins.
			$this->init_framework_root();

			// Hook to include additional modules when plugin loaded.
			do_action( 'qode_wishlist_for_woocommerce_action_framework_after_framework_plugin_loaded' );
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Wishlist_For_WooCommerce_Framework
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function require_core() {
			require_once QODE_WISHLIST_FOR_WOOCOMMERCE_ADMIN_PATH . '/helpers/include.php';
			require_once QODE_WISHLIST_FOR_WOOCOMMERCE_ADMIN_PATH . '/inc/class-qode-wishlist-for-woocommerce-framework-root.php';
		}

		public function init_framework_root() {
			add_filter( 'qode_wishlist_for_woocommerce_filter_framework_register_admin_options', array( $this, 'create_core_options' ) );

			add_action( 'qode_wishlist_for_woocommerce_action_framework_before_options_init_' . QODE_WISHLIST_FOR_WOOCOMMERCE_OPTIONS_NAME, array( $this, 'init_core_options' ) );

			add_action( 'qode_wishlist_for_woocommerce_action_framework_populate_meta_box', array( $this, 'init_core_meta_boxes' ) );

			$GLOBALS['qode_wishlist_for_woocommerce_framework'] = qode_wishlist_for_woocommerce_framework_get_framework_root();
		}

		public function create_core_options( $options ) {
			$qode_wishlist_for_woocommerce_options_admin = new Qode_Wishlist_For_WooCommerce_Framework_Options_Admin(
				QODE_WISHLIST_FOR_WOOCOMMERCE_MENU_NAME,
				QODE_WISHLIST_FOR_WOOCOMMERCE_OPTIONS_NAME,
				array(
					'label' => esc_html__( 'Wishlist', 'qode-wishlist-for-woocommerce' ),
				)
			);

			$options[] = $qode_wishlist_for_woocommerce_options_admin;

			return $options;
		}

		public function init_core_options() {
			$qode_framework = qode_wishlist_for_woocommerce_framework_get_framework_root();

			if ( ! empty( $qode_framework ) ) {

				if ( apply_filters( 'qode_wishlist_for_woocommerce_filter_enable_global_options', true ) ) {
					$page = $qode_framework->add_options_page(
						array(
							'scope'       => QODE_WISHLIST_FOR_WOOCOMMERCE_OPTIONS_NAME,
							'type'        => 'admin',
							'slug'        => 'general',
							'title'       => esc_html__( 'Global', 'qode-wishlist-for-woocommerce' ),
							'description' => esc_html__( 'Global Plugin Options', 'qode-wishlist-for-woocommerce' ),
						)
					);

					// Hook to include additional options after default options.
					do_action( 'qode_wishlist_for_woocommerce_action_default_options_init', $page );
				}

				// Hook to include additional options.
				do_action( 'qode_wishlist_for_woocommerce_action_core_options_init' );
			}
		}

		public function init_core_meta_boxes() {
			do_action( 'qode_wishlist_for_woocommerce_action_default_meta_boxes_init' );
		}
	}

	Qode_Wishlist_For_WooCommerce_Framework::get_instance();
}
