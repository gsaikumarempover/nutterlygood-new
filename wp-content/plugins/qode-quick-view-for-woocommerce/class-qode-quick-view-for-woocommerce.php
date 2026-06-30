<?php
/*
Plugin Name: QODE Quick View for WooCommerce
Description: QODE Quick View for WooCommerce helps you boost conversions & sales by providing visitors with handy pop-up product previews on product list pages.
Author: Qode Interactive
Author URI: https://qodeinteractive.com/
Plugin URI: https://qodeinteractive.com/qode-quick-view-for-woocommerce/
Version: 1.1.2
Requires at least: 6.3
Requires PHP: 7.4
WC requires at least: 7.6
WC tested up to: 9.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: qode-quick-view-for-woocommerce
*/

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Quick_View_For_WooCommerce' ) ) {
	class Qode_Quick_View_For_WooCommerce {
		private static $instance;

		public function __construct() {
			// Set the main plugins constants.
			define( 'QODE_QUICK_VIEW_FOR_WOOCOMMERCE_PLUGIN_BASE_FILE', plugin_basename( __FILE__ ) );

			// Include required files.
			require_once __DIR__ . '/constants.php';
			require_once QODE_QUICK_VIEW_FOR_WOOCOMMERCE_ABS_PATH . '/helpers/helper.php';

			// Include framework file.
			require_once QODE_QUICK_VIEW_FOR_WOOCOMMERCE_ADMIN_PATH . '/class-qode-quick-view-for-woocommerce-framework.php';

			// Check if WooCommerce is installed.
			if ( function_exists( 'WC' ) ) {

				// Make plugin available for translation.
				// permission 15 is set in order to be after the plugin initialization.
				add_action( 'plugins_loaded', array( $this, 'load_plugin_text_domain' ), 15 );

				// Add plugin's body classes.
				add_filter( 'body_class', array( $this, 'add_body_classes' ) );

				// Enqueue plugin's assets.
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
				// permission 12 is set in order to have the highest priority.
				add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_style' ), 12 );
				add_action( 'wp_enqueue_scripts', array( $this, 'localize_scripts' ) );

				// Set WooCommerce features.
				add_action( 'before_woocommerce_init', array( $this, 'declare_wc_features_support' ) );

				// Include plugin's modules.
				$this->include_modules();
			}
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Quick_View_For_WooCommerce
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function load_plugin_text_domain() {
			// Make plugin available for translation.
			load_plugin_textdomain( 'qode-quick-view-for-woocommerce', false, QODE_QUICK_VIEW_FOR_WOOCOMMERCE_REL_PATH . '/languages' );
		}

		public function add_body_classes( $classes ) {
			$classes[] = 'qode-quick-view-for-woocommerce-' . QODE_QUICK_VIEW_FOR_WOOCOMMERCE_VERSION;

			if ( wp_is_mobile() ) {
				$classes[] = 'qqvfw--touch';
			} else {
				$classes[] = 'qqvfw--no-touch';
			}

			return $classes;
		}

		public function enqueue_assets() {
			// Enqueue CSS styles.
			wp_enqueue_style( 'qode-quick-view-for-woocommerce-main', QODE_QUICK_VIEW_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/css/main.min.css', array(), QODE_QUICK_VIEW_FOR_WOOCOMMERCE_VERSION );

			// Enqueue JS scripts.
			wp_enqueue_script( 'qode-quick-view-for-woocommerce-main', QODE_QUICK_VIEW_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/js/main.min.js', array( 'jquery' ), QODE_QUICK_VIEW_FOR_WOOCOMMERCE_VERSION, true );
		}

		public function add_inline_style() {
			$style = apply_filters( 'qode_quick_view_for_woocommerce_filter_add_inline_style', '' );

			if ( ! empty( $style ) ) {
				wp_add_inline_style( apply_filters( 'qode_quick_view_for_woocommerce_filter_inline_style_handle', 'qode-quick-view-for-woocommerce-main' ), $style );
			}
		}

		public function localize_scripts() {
			$global = apply_filters(
				'qode_quick_view_for_woocommerce_filter_localize_main_plugin_script',
				array(
					'adminBarHeight'       => is_admin_bar_showing() ? ( wp_is_mobile() ? 46 : 32 ) : 0,
					'protectedDataMessage' => esc_html__( 'Something went wrong', 'qode-quick-view-for-woocommerce' ),
					'makeASelectionText'   => esc_html__( 'Please select some product options before adding this product to your cart.', 'qode-quick-view-for-woocommerce' ),
					'unavailableText'      => esc_html__( 'Sorry, this product is unavailable. Please choose a different combination.', 'qode-quick-view-for-woocommerce' ),
					'emptyQuantityText'    => esc_html__( 'Please choose the quantity of items you wish to add to your cart...', 'qode-quick-view-for-woocommerce' ),
					'inStockText'          => esc_html__( 'In stock', 'qode-quick-view-for-woocommerce' ),
					'checkoutUrl'          => esc_url( wc_get_checkout_url() ),
					'arrowLeft'            => qode_quick_view_for_woocommerce_get_svg_icon( 'arrow-left' ),
					'arrowRight'           => qode_quick_view_for_woocommerce_get_svg_icon( 'arrow-right' ),
				)
			);

			wp_localize_script(
				'qode-quick-view-for-woocommerce-main',
				'qodeQuickViewForWooCommerceGlobal',
				$global
			);
		}

		public function declare_wc_features_support() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', QODE_QUICK_VIEW_FOR_WOOCOMMERCE_PLUGIN_BASE_FILE, true );
			}
		}

		public function include_modules() {
			// Hook to include additional element before modules inclusion.
			do_action( 'qode_quick_view_for_woocommerce_action_before_include_modules' );

			foreach ( glob( QODE_QUICK_VIEW_FOR_WOOCOMMERCE_INC_PATH . '/*/include.php' ) as $module ) {
				include_once $module;
			}

			// Hook to include additional element after modules inclusion.
			do_action( 'qode_quick_view_for_woocommerce_action_after_include_modules' );
		}
	}
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_init_plugin' ) ) {
	/**
	 * Function that init plugin activation
	 */
	function qode_quick_view_for_woocommerce_init_plugin() {
		Qode_Quick_View_For_WooCommerce::get_instance();
	}

	add_action( 'plugins_loaded', 'qode_quick_view_for_woocommerce_init_plugin' );
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_activation_trigger' ) ) {
	/**
	 * Function that trigger hooks on plugin activation
	 */
	function qode_quick_view_for_woocommerce_activation_trigger() {
		// Hook to add additional code on plugin activation.
		do_action( 'qode_quick_view_for_woocommerce_action_on_activation' );
	}

	register_activation_hook( __FILE__, 'qode_quick_view_for_woocommerce_activation_trigger' );
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_deactivation_trigger' ) ) {
	/**
	 * Function that trigger hooks on plugin deactivation
	 */
	function qode_quick_view_for_woocommerce_deactivation_trigger() {
		// Hook to add additional code on plugin deactivation.
		do_action( 'qode_quick_view_for_woocommerce_action_on_deactivation' );
	}

	register_deactivation_hook( __FILE__, 'qode_quick_view_for_woocommerce_deactivation_trigger' );
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_check_requirements' ) ) {
	/**
	 * Function that check plugin requirements
	 */
	function qode_quick_view_for_woocommerce_check_requirements() {
		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'qode_quick_view_for_woocommerce_admin_notice_content' );
		}
	}

	add_action( 'plugins_loaded', 'qode_quick_view_for_woocommerce_check_requirements' );
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_admin_notice_content' ) ) {
	/**
	 * Function that display the error message if the requirements are not met
	 */
	function qode_quick_view_for_woocommerce_admin_notice_content() {
		printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html__( 'WooCommerce plugin is required for QODE Quick View for WooCommerce plugin to work properly. Please install/activate it first.', 'qode-quick-view-for-woocommerce' ) );
	}
}
