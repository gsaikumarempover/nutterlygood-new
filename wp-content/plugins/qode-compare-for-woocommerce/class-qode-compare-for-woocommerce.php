<?php
/*
Plugin Name: QODE Compare for WooCommerce
Description: Streamline user experience with practical comparison functionality, offering easy product overviews with features and differences highlighted.
Author: Qode Interactive
Author URI: https://qodeinteractive.com/
Plugin URI: https://qodeinteractive.com/qode-compare-for-woocommerce/
Version: 1.0.2
Requires at least: 6.3
Requires PHP: 7.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: qode-compare-for-woocommerce
*/

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Compare_For_WooCommerce' ) ) {
	class Qode_Compare_For_WooCommerce {
		private static $instance;

		public function __construct() {
			// Set the main plugins constants.
			define( 'QODE_COMPARE_FOR_WOOCOMMERCE_PLUGIN_BASE_FILE', plugin_basename( __FILE__ ) );

			// Include required files.
			require_once __DIR__ . '/constants.php';
			require_once QODE_COMPARE_FOR_WOOCOMMERCE_ABS_PATH . '/helpers/helper.php';

			// Include framework file.
			require_once QODE_COMPARE_FOR_WOOCOMMERCE_ADMIN_PATH . '/class-qode-compare-for-woocommerce-framework.php';

			// Check if WooCommerce is installed.
			if ( function_exists( 'WC' ) ) {

				// Make plugin available for translation. Permission 15 is set in order to be after the plugin initialization.
				add_action( 'plugins_loaded', array( $this, 'load_plugin_text_domain' ), 15 );

				// Add plugin's body classes.
				add_filter( 'body_class', array( $this, 'add_body_classes' ) );

				// Enqueue plugin's assets.
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_style' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'localize_scripts' ) );

				// 5 is set to be same permission as Gutenberg plugin have.
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 5 );

				// Include plugin's modules.
				$this->include_modules();

				// Hide default dashboard global options.
				add_filter( 'qode_compare_for_woocommerce_filter_enable_global_options', '__return_false' );
			}
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Compare_For_WooCommerce
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function load_plugin_text_domain() {
			// Make plugin available for translation.
			load_plugin_textdomain( 'qode-compare-for-woocommerce', false, QODE_COMPARE_FOR_WOOCOMMERCE_REL_PATH . '/languages' );
		}

		public function add_body_classes( $classes ) {
			$classes[] = 'qode-compare-for-woocommerce-' . QODE_COMPARE_FOR_WOOCOMMERCE_VERSION;

			if ( wp_is_mobile() ) {
				$classes[] = 'qcfw--touch';
			} else {
				$classes[] = 'qcfw--no-touch';
			}

			return $classes;
		}

		public function enqueue_assets() {
			// Enqueue CSS styles.
			wp_enqueue_style( 'qode-compare-for-woocommerce-main', QODE_COMPARE_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/css/main.min.css', array(), QODE_COMPARE_FOR_WOOCOMMERCE_VERSION );

			// Enqueue JS scripts.
			wp_enqueue_script( 'qode-compare-for-woocommerce-main', QODE_COMPARE_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/js/main.min.js', array( 'jquery' ), QODE_COMPARE_FOR_WOOCOMMERCE_VERSION, true );
		}

		public function add_inline_style() {
			$style = apply_filters( 'qode_compare_for_woocommerce_filter_add_inline_style', '' );

			if ( ! empty( $style ) ) {
				wp_add_inline_style( 'qode-compare-for-woocommerce-main', $style );
			}
		}

		public function enqueue_admin_scripts() {
			// check if page is options page.
			// phpcs:ignore WordPress.Security.NonceVerification
			if ( isset( $_GET['page'] ) && str_contains( sanitize_text_field( wp_unslash( $_GET['page'] ) ), QODE_COMPARE_FOR_WOOCOMMERCE_MENU_NAME ) ) {
				wp_enqueue_script( 'qode-compare-for-woocommerce-admin', QODE_COMPARE_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/js/admin.min.js', array( 'jquery', 'qode-compare-for-woocommerce-framework-script' ), QODE_COMPARE_FOR_WOOCOMMERCE_VERSION, true );
			}
		}

		public function localize_scripts() {
			$button_text          = qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_button_text' );
			$button_text          = ! empty( $button_text ) ? $button_text : esc_html__( 'Add to Compare', 'qode-compare-for-woocommerce' );
			$autopopup            = qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_auto_popup' );
			$removal_confirmation = qode_compare_for_woocommerce_get_option_value( 'admin', 'qode_compare_for_woocommerce_removal_confirmation' );

			$global = apply_filters(
				'qode_compare_for_woocommerce_filter_localize_main_plugin_script',
				array(
					'adminBarHeight'      => is_admin_bar_showing() ? ( wp_is_mobile() ? 46 : 32 ) : 0,
					'autoPopup'           => isset( $autopopup ) && 'no' !== $autopopup,
					'removalConfirmation' => isset( $removal_confirmation ) && 'no' !== $removal_confirmation,
					'buttonText'          => $button_text,
					'addedText'           => apply_filters( 'qode_compare_for_woocommerce_filter_button_added_text', esc_html__( 'Added to Compare', 'qode-compare-for-woocommerce' ) ),
					'modalHTML'           => qode_compare_for_woocommerce_get_template_part( 'compare/templates', 'parts/modal-inner' ),
				)
			);

			wp_localize_script(
				'qode-compare-for-woocommerce-main',
				'qodeCompareForWooCommerceGlobal',
				$global
			);
		}

		public function include_modules() {
			// Hook to include additional element before modules inclusion.
			do_action( 'qode_compare_for_woocommerce_action_before_include_modules' );

			foreach ( glob( QODE_COMPARE_FOR_WOOCOMMERCE_INC_PATH . '/*/include.php' ) as $module ) {
				include_once $module;
			}

			// Hook to include additional element after modules inclusion.
			do_action( 'qode_compare_for_woocommerce_action_after_include_modules' );
		}
	}
}

if ( ! function_exists( 'qode_compare_for_woocommerce_init_plugin' ) ) {
	/**
	 * Function that init plugin activation
	 */
	function qode_compare_for_woocommerce_init_plugin() {
		Qode_Compare_For_WooCommerce::get_instance();
	}

	add_action( 'plugins_loaded', 'qode_compare_for_woocommerce_init_plugin' );
}

if ( ! function_exists( 'qode_compare_for_woocommerce_activation_trigger' ) ) {
	/**
	 * Function that trigger hooks on plugin activation
	 */
	function qode_compare_for_woocommerce_activation_trigger() {
		// Hook to add additional code on plugin activation.
		do_action( 'qode_compare_for_woocommerce_action_on_activation' );
	}

	register_activation_hook( __FILE__, 'qode_compare_for_woocommerce_activation_trigger' );
}

if ( ! function_exists( 'qode_compare_for_woocommerce_deactivation_trigger' ) ) {
	/**
	 * Function that trigger hooks on plugin deactivation
	 */
	function qode_compare_for_woocommerce_deactivation_trigger() {
		// Hook to add additional code on plugin deactivation.
		do_action( 'qode_compare_for_woocommerce_action_on_deactivation' );
	}

	register_deactivation_hook( __FILE__, 'qode_compare_for_woocommerce_deactivation_trigger' );
}

if ( ! function_exists( 'qode_compare_for_woocommerce_check_requirements' ) ) {
	/**
	 * Function that check plugin requirements
	 */
	function qode_compare_for_woocommerce_check_requirements() {
		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'qode_compare_for_woocommerce_admin_notice_content' );
		}
	}

	add_action( 'plugins_loaded', 'qode_compare_for_woocommerce_check_requirements' );
}

if ( ! function_exists( 'qode_compare_for_woocommerce_admin_notice_content' ) ) {
	/**
	 * Function that display the error message if the requirements are not met
	 */
	function qode_compare_for_woocommerce_admin_notice_content() {
		printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html__( 'WooCommerce plugin is required for QODE Compare For WooCommerce Compare plugin to work properly. Please install/activate it first.', 'qode-compare-for-woocommerce' ) );
	}
}
