<?php
/*
Plugin Name: Nuttergood Core
Plugin URI: http://localhost/nutterlyGood
Description: Core functionality, shortcodes, and theme modules for Nuttergood
Author: Nutterly Good
Author URI: http://localhost/nutterlyGood
Version: 1.0
Text Domain: greenpath-core
*/
if ( ! class_exists( 'GreenPathCore' ) ) {
	class GreenPathCore {
		private static $instance;

		public function __construct() {
			$this->require_core();

			add_filter( 'qode_framework_filter_register_admin_options', array( $this, 'create_core_options' ) );

			add_action( 'qode_framework_action_before_options_init_' . GREENPATH_CORE_OPTIONS_NAME, array( $this, 'init_core_options' ) );

			add_action( 'qode_framework_action_populate_meta_box', array( $this, 'init_core_meta_boxes' ) );

			// Register plugin assets
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

			// Include plugin assets
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

			// Make plugin available for translation
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ), 15 ); // permission 15 is set in order to be after the qode-framework initialization

			// Add plugin's body classes
			add_filter( 'body_class', array( $this, 'add_body_classes' ) );

			// Hook to include additional modules when plugin loaded
			do_action( 'greenpath_core_action_plugin_loaded' );
		}

		/**
		 * @return GreenPathCore
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function require_core() {
			require_once dirname( __FILE__ ) . '/constants.php';
			require_once GREENPATH_CORE_ABS_PATH . '/helpers/helper.php';

			// Hook to include additional files before modules inclusion
			do_action( 'greenpath_core_action_before_include_modules' );

			foreach ( glob( GREENPATH_CORE_INC_PATH . '/*/include.php' ) as $module ) {
				include_once $module;
			}

			// Hook to include additional files after modules inclusion
			do_action( 'greenpath_core_action_after_include_modules' );
		}

		function create_core_options( $options ) {
			$greenpath_core_options_admin = new QodeFrameworkOptionsAdmin(
				GREENPATH_CORE_MENU_NAME,
				GREENPATH_CORE_OPTIONS_NAME,
				array(
					'label' => esc_html__( 'Nuttergood Core Options', 'greenpath-core' ),
					'code'  => GreenPathCore_Dashboard::get_instance()->get_code(),
				)
			);

			$options[] = $greenpath_core_options_admin;

			return $options;
		}

		function init_core_options() {
			$qode_framework = qode_framework_get_framework_root();

			if ( ! empty( $qode_framework ) ) {
				$page = $qode_framework->add_options_page(
					array(
						'scope'       => GREENPATH_CORE_OPTIONS_NAME,
						'type'        => 'admin',
						'slug'        => 'general',
						'title'       => esc_html__( 'General', 'greenpath-core' ),
						'description' => esc_html__( 'Global Theme Options', 'greenpath-core' ),
						'icon'        => 'fa fa-cog',
					)
				);

				// Hook to include additional options after default options
				do_action( 'greenpath_core_action_default_options_init', $page );
			}
		}

		function init_core_meta_boxes() {
			do_action( 'greenpath_core_action_default_meta_boxes_init' );
		}

		function register_scripts() {

			// Register 3rd party plugins style
			wp_register_style( 'magnific-popup', GREENPATH_CORE_URL_PATH . 'assets/plugins/magnific-popup/magnific-popup.css' );

			// Register 3rd party plugins script
			wp_register_script( 'jquery-magnific-popup', GREENPATH_CORE_URL_PATH . 'assets/plugins/magnific-popup/jquery.magnific-popup.min.js', array( 'jquery' ), false, true );
			wp_register_script( 'bloodhound', GREENPATH_CORE_URL_PATH . 'assets/plugins/bloodhound/bloodhound.min.js', array( 'jquery' ), false, true );
			wp_register_script( 'typeahead', GREENPATH_CORE_URL_PATH . 'assets/plugins/typeahead/typeahead.bundle.min.js', array( 'jquery' ), false, true );

			// Hook to include additional registered scripts
			do_action( 'greenpath_core_action_registered_scripts' );
		}

		function enqueue_assets() {
			// CSS and JS dependency variables
			$style_dependency_array  = apply_filters( 'greenpath_core_filter_style_dependencies', array( 'greenpath-main' ) );
			$script_dependency_array = apply_filters( 'greenpath_core_filter_script_dependencies', array( 'greenpath-main-js' ) );

			// Hook to include additional scripts before plugin's main style
			do_action( 'greenpath_core_action_before_main_css' );

			// Enqueue plugin's main style
			wp_enqueue_style( 'greenpath-core-style', GREENPATH_CORE_URL_PATH . 'assets/css/greenpath-core.min.css', $style_dependency_array );

			// Enqueue plugin's 3rd party scripts
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'modernizr', GREENPATH_CORE_URL_PATH . 'assets/plugins/modernizr/modernizr.js', array( 'jquery' ), false, true );
			wp_enqueue_script( 'gsap', GREENPATH_CORE_URL_PATH . 'assets/plugins/gsap/gsap.min.js', array( 'jquery' ), false, true );
			wp_enqueue_script( 'scrollTrigger', GREENPATH_CORE_URL_PATH . 'assets/plugins/gsap/ScrollTrigger.min.js', array( 'jquery' ), false, true );
			wp_enqueue_script( 'parallax-scroll', GREENPATH_CORE_URL_PATH . 'assets/plugins/parallax-scroll/jquery.parallax-scroll.js', array( 'jquery' ), false, true );

			// Hook to include additional scripts before plugin's main script
			do_action( 'greenpath_core_action_before_main_js' );

			// Enqueue plugin's main script
			wp_enqueue_script( 'greenpath-core-script', GREENPATH_CORE_URL_PATH . 'assets/js/greenpath-core.min.js', $script_dependency_array, false, true );
		}

		function load_plugin_textdomain() {
			load_plugin_textdomain( 'greenpath-core', false, GREENPATH_CORE_REL_PATH . '/languages' );
		}

		function add_body_classes( $classes ) {
			$classes[] = 'greenpath-core-' . GREENPATH_CORE_VERSION;

			return $classes;
		}
	}
}

if ( ! function_exists( 'greenpath_core_instantiate_plugin' ) ) {
	/**
	 * Function that initialize plugin
	 */
	function greenpath_core_instantiate_plugin() {
		GreenPathCore::get_instance();
	}

	add_action( 'qode_framework_action_load_dependent_plugins', 'greenpath_core_instantiate_plugin' );
}

if ( ! function_exists( 'greenpath_core_activation_trigger' ) ) {
	/**
	 * Function that trigger hooks on plugin activation
	 */
	function greenpath_core_activation_trigger() {
		// Set global plugin option when plugin is activated
		add_option( 'greenpath_core_activated_first_time', 'yes' );

		// Hook to add additional code on plugin activation
		do_action( 'greenpath_core_action_on_activation' );
	}

	register_activation_hook( __FILE__, 'greenpath_core_activation_trigger' );
}

if ( ! function_exists( 'greenpath_core_deactivation_trigger' ) ) {
	/**
	 * Function that trigger hooks on plugin deactivation
	 */
	function greenpath_core_deactivation_trigger() {
		// Remove global plugin option during deactivation
		delete_option( 'greenpath_core_activated_first_time' );

		// Hook to add additional code on plugin deactivation
		do_action( 'greenpath_core_action_on_deactivation' );
	}

	register_deactivation_hook( __FILE__, 'greenpath_core_deactivation_trigger' );
}

if ( ! function_exists( 'greenpath_core_plugins_loaded_option' ) ) {
	/**
	 * Function that update global option that plugin is activated first time
	 */
	function greenpath_core_plugins_loaded_option() {
		if ( 'yes' === get_option( 'greenpath_core_activated_first_time' ) ) {
			update_option( 'greenpath_core_activated_first_time', 'no' );
		}
	}

	add_action( 'plugins_loaded', 'greenpath_core_plugins_loaded_option', 1000 ); //needs to be last, so option can be changed after all actions
}

if ( ! function_exists( 'greenpath_core_check_requirements' ) ) {
	/**
	 * Function that check plugin requirements
	 */
	function greenpath_core_check_requirements() {
		if ( ! defined( 'QODE_FRAMEWORK_VERSION' ) ) {
			add_action( 'admin_notices', 'greenpath_core_admin_notice_content' );
		}
	}

	add_action( 'plugins_loaded', 'greenpath_core_check_requirements' );
}

if ( ! function_exists( 'greenpath_core_admin_notice_content' ) ) {
	/**
	 * Function that display the error message if the requirements are not met
	 */
	function greenpath_core_admin_notice_content() {
		echo sprintf( '<div class="notice notice-error"><p>%s</p></div>', esc_html__( 'Qode Framework plugin is required for Nutterlygood Core plugin to work properly. Please install/activate it first.', 'greenpath-core' ) );

		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
	}
}
