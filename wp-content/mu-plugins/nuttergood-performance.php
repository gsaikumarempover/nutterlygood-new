<?php
/**
 * NutterlyGood performance optimizations.
 *
 * @package NutterlyGood
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load Elementor widgets only when needed (frontend).
 */
final class Nuttergood_Performance {

	/** @var string[]|null */
	private static $required_widgets = null;

	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'defer_heavy_elementor_loaders' ), 60 );
		add_action( 'init', array( __CLASS__, 'frontend_optimizations' ), 1 );
		add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'trim_frontend_assets' ), 999 );
	}

	public static function defer_heavy_elementor_loaders() {
		if ( self::should_load_all_elementor_widgets() ) {
			return;
		}

		remove_action( 'elementor/widgets/register', 'qi_addons_for_elementor_load_elementor_widgets' );
		remove_action( 'elementor/widgets/widgets_registered', 'qi_addons_for_elementor_load_elementor_widgets' );
		remove_action( 'elementor/widgets/register', 'greenpath_core_load_elementor_widgets' );
		remove_action( 'elementor/widgets/widgets_registered', 'greenpath_core_load_elementor_widgets' );
		remove_action( 'elementor/widgets/register', 'qode_wishlist_for_woocommerce_load_elementor_widgets' );
		remove_action( 'elementor/widgets/widgets_registered', 'qode_wishlist_for_woocommerce_load_elementor_widgets' );
		remove_action( 'elementor/widgets/register', 'qode_quick_view_for_woocommerce_load_elementor_widgets' );
		remove_action( 'elementor/widgets/widgets_registered', 'qode_quick_view_for_woocommerce_load_elementor_widgets' );
		remove_action( 'elementor/widgets/register', 'qode_compare_for_woocommerce_load_elementor_widgets' );
		remove_action( 'elementor/widgets/widgets_registered', 'qode_compare_for_woocommerce_load_elementor_widgets' );

		add_action( 'elementor/widgets/register', array( __CLASS__, 'load_required_elementor_widgets' ), 5 );
	}

	public static function load_required_elementor_widgets() {
		$required = self::get_required_widgets();
		if ( empty( $required ) ) {
			return;
		}

		self::load_greenpath_elementor_widgets( $required );

		if ( self::needs_qi_widgets( $required ) ) {
			if ( function_exists( 'qi_addons_for_elementor_load_elementor_widgets' ) ) {
				qi_addons_for_elementor_load_elementor_widgets();
			}
		}
	}

	private static function should_load_all_elementor_widgets() {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return true;
		}

		if ( isset( $_GET['elementor-preview'], $_GET['ver'] ) ) { // phpcs:ignore
			return true;
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}

		return false;
	}

	/**
	 * @return string[]
	 */
	private static function get_required_widgets() {
		if ( null !== self::$required_widgets ) {
			return self::$required_widgets;
		}

		self::$required_widgets = array();

		$post_id = get_queried_object_id();
		if ( ! $post_id ) {
			return self::$required_widgets;
		}

		$data = get_post_meta( $post_id, '_elementor_data', true );
		if ( empty( $data ) ) {
			return self::$required_widgets;
		}

		$nodes = json_decode( $data, true );
		if ( ! is_array( $nodes ) ) {
			return self::$required_widgets;
		}

		$walk = static function ( $elements ) use ( &$walk, &$required ) {
			foreach ( $elements as $element ) {
				if ( ! empty( $element['widgetType'] ) ) {
					$required[] = $element['widgetType'];
				}
				if ( ! empty( $element['elements'] ) ) {
					$walk( $element['elements'] );
				}
			}
		};

		$required = array();
		$walk( $nodes );
		self::$required_widgets = array_values( array_unique( $required ) );

		return self::$required_widgets;
	}

	/**
	 * @param string[] $required Widget type slugs.
	 */
	private static function load_greenpath_elementor_widgets( $required ) {
		if ( ! defined( 'GREENPATH_CORE_SHORTCODES_PATH' ) ) {
			return;
		}

		$needed_slugs = array();
		foreach ( $required as $widget ) {
			if ( 0 === strpos( $widget, 'greenpath_core_' ) ) {
				$needed_slugs[ $widget ] = str_replace( '_', '-', substr( $widget, strlen( 'greenpath_core_' ) ) );
			}
		}

		if ( empty( $needed_slugs ) ) {
			return;
		}

		if ( ! class_exists( 'GreenPathCore_Elementor_Widget_Base' ) ) {
			include_once GREENPATH_CORE_PLUGINS_PATH . '/elementor/class-greenpathcore-elementor-widget-base.php';
		}

		$candidates = array();

		$patterns = array(
			GREENPATH_CORE_SHORTCODES_PATH . '/*/*-elementor.php',
			GREENPATH_CORE_INC_PATH . '/*/shortcodes/*/*-elementor.php',
			GREENPATH_CORE_CPT_PATH . '/*/shortcodes/*/*-elementor.php',
			GREENPATH_CORE_PLUGINS_PATH . '/*/shortcodes/*/*-elementor.php',
			GREENPATH_CORE_PLUGINS_PATH . '/*/post-types/*/shortcodes/*/*-elementor.php',
			GREENPATH_CORE_PLUGINS_PATH . '/*/roles/*/shortcodes/*/*-elementor.php',
		);

		foreach ( $patterns as $pattern ) {
			foreach ( glob( $pattern ) as $file ) {
				$folder = basename( dirname( $file ) );
				$slug   = 'greenpath_core_' . str_replace( '-', '_', $folder );
				if ( isset( $needed_slugs[ $slug ] ) ) {
					$candidates[ $file ] = $file;
				}
			}
		}

		foreach ( $candidates as $file ) {
			include_once $file;
		}
	}

	/**
	 * @param string[] $required Widget type slugs.
	 */
	private static function needs_qi_widgets( $required ) {
		foreach ( $required as $widget ) {
			if ( 0 === strpos( $widget, 'qi_' ) ) {
				return true;
			}
		}
		return false;
	}

	public static function frontend_optimizations() {
		if ( is_admin() ) {
			return;
		}

		add_filter( 'woocommerce_register_widgets', array( __CLASS__, 'disable_heavy_wc_widgets' ) );

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		wp_deregister_script( 'wp-embed' );

		add_filter( 'heartbeat_settings', static function ( $settings ) {
			$settings['interval'] = 60;
			return $settings;
		} );
	}

	/**
	 * @param string[] $widgets Widget class names.
	 * @return string[]
	 */
	public static function disable_heavy_wc_widgets( $widgets ) {
		$skip = array(
			'WC_Widget_Brand_Thumbnails',
			'WC_Widget_Brand_Description',
			'WC_Widget_Brand_Nav',
		);

		return array_values( array_diff( $widgets, $skip ) );
	}

	public static function trim_frontend_assets() {
		if ( is_admin() ) {
			return;
		}

		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );

		if ( ! is_cart() && ! is_checkout() && ! is_account_page() ) {
			wp_dequeue_script( 'wc-cart-fragments' );
		}
	}
}

Nuttergood_Performance::init();