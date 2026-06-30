<?php
/*
Plugin Name: QODE Wishlist for WooCommerce
Description: Qode Wishlist for WooCommerce plugin is the ideal toolkit for letting your visitors save & share comprehensive lists with their products of interest.
Author: Qode Interactive
Author URI: https://qodeinteractive.com/
Plugin URI: https://qodeinteractive.com/qode-wishlist-for-woocommerce/
Version: 1.2.8
Requires at least: 6.3
Requires PHP: 7.4
WC requires at least: 7.6
WC tested up to: 10.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: qode-wishlist-for-woocommerce
*/

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Wishlist_For_WooCommerce' ) ) {
	class Qode_Wishlist_For_WooCommerce {
		private static $instance;

		public function __construct() {
			// Set the main plugins constants.
			define( 'QODE_WISHLIST_FOR_WOOCOMMERCE_PLUGIN_BASE_FILE', plugin_basename( __FILE__ ) );

			// Include required files.
			require_once __DIR__ . '/constants.php';

			require_once QODE_WISHLIST_FOR_WOOCOMMERCE_ABS_PATH . '/helpers/helper.php';

			// Include a framework file.
			require_once QODE_WISHLIST_FOR_WOOCOMMERCE_ADMIN_PATH . '/class-qode-wishlist-for-woocommerce-framework.php';

			// Check if WooCommerce is installed.
			if ( function_exists( 'WC' ) ) {
				// Make plugin available for translation.
				add_action( 'init', array( $this, 'load_plugin_text_domain' ) );

				// Add the plugin's body classes.
				add_filter( 'body_class', array( $this, 'add_body_classes' ) );

				// Enqueue plugin's assets.
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_style' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'localize_scripts' ) );

				// Set WooCommerce features.
				add_action( 'before_woocommerce_init', array( $this, 'declare_wc_features_support' ) );

				// Include plugin's modules.
				$this->include_modules();
			}
		}

		/**
		 * Instance of a module class
		 *
		 * @return Qode_Wishlist_For_WooCommerce
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function load_plugin_text_domain() {
			// Make plugin available for translation.
			load_plugin_textdomain( 'qode-wishlist-for-woocommerce', false, QODE_WISHLIST_FOR_WOOCOMMERCE_REL_PATH . '/languages' );
		}

		public function add_body_classes( $classes ) {
			$classes[] = 'qode-wishlist-for-woocommerce-' . QODE_WISHLIST_FOR_WOOCOMMERCE_VERSION;

			if ( wp_is_mobile() ) {
				$classes[] = 'qwfw--touch';
			} else {
				$classes[] = 'qwfw--no-touch';
			}

			return $classes;
		}

		public function enqueue_assets() {
			// Enqueue CSS styles.
			wp_enqueue_style( 'qode-wishlist-for-woocommerce-main', QODE_WISHLIST_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/css/main.min.css', array(), QODE_WISHLIST_FOR_WOOCOMMERCE_VERSION );

			// Enqueue JS scripts.
			wp_enqueue_script( 'qode-wishlist-for-woocommerce-main', QODE_WISHLIST_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/js/main.min.js', array( 'jquery' ), QODE_WISHLIST_FOR_WOOCOMMERCE_VERSION, true );
		}

		public function add_inline_style() {
			$style = apply_filters( 'qode_wishlist_for_woocommerce_filter_add_inline_style', '' );

			if ( ! empty( $style ) ) {
				wp_add_inline_style( 'qode-wishlist-for-woocommerce-main', $style );
			}
		}

		public function localize_scripts() {
			$global = apply_filters(
				'qode_wishlist_for_woocommerce_filter_localize_main_plugin_script',
				array(
					'adminBarHeight' => is_admin_bar_showing() ? ( wp_is_mobile() ? 46 : 32 ) : 0,
				)
			);

			wp_localize_script(
				'qode-wishlist-for-woocommerce-main',
				'qodeWishlistForWooCommerceGlobal',
				$global
			);
		}

		public function declare_wc_features_support() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', QODE_WISHLIST_FOR_WOOCOMMERCE_PLUGIN_BASE_FILE, true );
			}
		}

		public function include_modules() {
			// Hook to include an additional element before module inclusion.
			do_action( 'qode_wishlist_for_woocommerce_action_before_include_modules' );

			foreach ( glob( QODE_WISHLIST_FOR_WOOCOMMERCE_INC_PATH . '/*/include.php' ) as $module ) {
				include_once $module;
			}

			// Hook to include an additional element after module inclusion.
			do_action( 'qode_wishlist_for_woocommerce_action_after_include_modules' );
		}
	}
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_init_plugin' ) ) {
	/**
	 * Function that init plugin activation
	 */
	function qode_wishlist_for_woocommerce_init_plugin() {
		Qode_Wishlist_For_WooCommerce::get_instance();
	}

	add_action( 'plugins_loaded', 'qode_wishlist_for_woocommerce_init_plugin' );
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_activation_trigger' ) ) {
	/**
	 * Function that triggers hooks on plugin activation
	 */
	function qode_wishlist_for_woocommerce_activation_trigger() {
		// Schedule a daily check for guest Wishlist visibility expiration.
		if ( ! wp_next_scheduled( 'qode_wishlist_for_woocommerce_trigger_guests_wishlist_check' ) ) {
			wp_schedule_event( time(), 'daily', 'qode_wishlist_for_woocommerce_trigger_guests_wishlist_check' );
		}

		// Hook to add additional code on plugin activation.
		do_action( 'qode_wishlist_for_woocommerce_action_on_activation' );
	}

	register_activation_hook( __FILE__, 'qode_wishlist_for_woocommerce_activation_trigger' );
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_deactivation_trigger' ) ) {
	/**
	 * Function that triggers hooks on plugin deactivation
	 */
	function qode_wishlist_for_woocommerce_deactivation_trigger() {
		// Clear schedule daily check for guest Wishlist visibility expiration.
		wp_clear_scheduled_hook( 'qode_wishlist_for_woocommerce_trigger_guests_wishlist_check' );

		// Hook to add additional code on plugin deactivation.
		do_action( 'qode_wishlist_for_woocommerce_action_on_deactivation' );
	}

	register_deactivation_hook( __FILE__, 'qode_wishlist_for_woocommerce_deactivation_trigger' );
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_check_requirements' ) ) {
	/**
	 * Function that checks plugin requirements
	 */
	function qode_wishlist_for_woocommerce_check_requirements() {
		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'qode_wishlist_for_woocommerce_admin_notice_content' );
		}
	}

	add_action( 'plugins_loaded', 'qode_wishlist_for_woocommerce_check_requirements' );
}

if ( ! function_exists( 'qode_wishlist_for_woocommerce_admin_notice_content' ) ) {
	/**
	 * Function that displays the error message if the requirements are not met
	 */
	function qode_wishlist_for_woocommerce_admin_notice_content() {
		printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html__( 'WooCommerce plugin is required for QODE Wishlist for WooCommerce plugin to work properly. Please install/activate it first.', 'qode-wishlist-for-woocommerce' ) );
	}
}
