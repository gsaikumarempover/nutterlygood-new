<?php
/*
Plugin Name: Nuttergood Membership
Plugin URI: http://localhost/nutterlyGood
Description: Plugin that adds login functionality and user dashboard page
Author: Nutterly Good
Author URI: http://localhost/nutterlyGood
Version: 1.0
*/
if ( ! class_exists( 'GreenPathMembership' ) ) {
	class GreenPathMembership {
		private static $instance;

		public function __construct() {
			$this->require_core();

			// Include plugin assets
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

			// Localize main js script
			add_action( 'wp_enqueue_scripts', array( $this, 'localize_script' ) );

			// Make plugin available for translation
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ), 15 ); // permission 15 is set in order to be after the qode-framework initialization

			// Add plugin's body classes
			add_filter( 'body_class', array( $this, 'add_body_classes' ) );

			// Hook to include additional modules when plugin loaded
			do_action( 'greenpath_membership_action_plugin_loaded' );
		}

		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function require_core() {
			require_once 'constants.php';
			require_once GREENPATH_MEMBERSHIP_ABS_PATH . '/helpers/helper.php';

			// Hook to include additional files before modules inclusion
			do_action( 'greenpath_membership_action_before_include_modules' );

			foreach ( glob( GREENPATH_MEMBERSHIP_INC_PATH . '/*/include.php' ) as $module ) {
				include_once $module;
			}

			// Hook to include additional files after modules inclusion
			do_action( 'greenpath_membership_action_after_include_modules' );
		}

		function enqueue_assets() {
			// CSS and JS dependency variables
			$style_dependency_array  = apply_filters( 'greenpath_membership_filter_style_dependencies', array() );
			$script_dependency_array = apply_filters( 'greenpath_membership_filter_script_dependencies', array( 'jquery-ui-tabs' ) );

			// Hook to include additional scripts before plugin's main style
			do_action( 'greenpath_membership_action_before_main_css' );

			// Enqueue plugin's main style
			wp_enqueue_style( 'greenpath-membership-style', GREENPATH_MEMBERSHIP_URL_PATH . 'assets/css/greenpath-membership.min.css', $style_dependency_array );

			// Hook to include additional scripts before plugin's main script
			do_action( 'greenpath_membership_action_before_main_js' );

			// Enqueue plugin's main script
			wp_enqueue_script( 'greenpath-membership-script', GREENPATH_MEMBERSHIP_URL_PATH . 'assets/js/greenpath-membership.min.js', $script_dependency_array, false, true );
		}

		function localize_script() {
			$global = apply_filters( 'greenpath_membership_filter_localize_main_js', array() );

			wp_localize_script( 'greenpath-membership-script', 'greenpathMembershipGlobal', $global );
		}

		function load_plugin_textdomain() {
			load_plugin_textdomain( 'greenpath-membership', false, GREENPATH_MEMBERSHIP_REL_PATH . '/languages' );
		}

		function add_body_classes( $classes ) {
			$classes[] = 'greenpath-membership-' . GREENPATH_MEMBERSHIP_VERSION;

			return $classes;
		}
	}
}

if ( ! function_exists( 'greenpath_membership_instantiate_plugin' ) ) {
	/**
	 * Function that initialize plugin
	 */
	function greenpath_membership_instantiate_plugin() {
		GreenPathMembership::get_instance();
	}

	add_action( 'qode_framework_action_load_dependent_plugins', 'greenpath_membership_instantiate_plugin' );
}

if ( ! function_exists( 'greenpath_membership_check_requirements' ) ) {
	/**
	 * Function that check plugin requirements
	 */
	function greenpath_membership_check_requirements() {
		if ( ! defined( 'GREENPATH_CORE_VERSION' ) ) {
			add_action( 'admin_notices', 'greenpath_membership_admin_notice_content' );
		}
	}

	add_action( 'plugins_loaded', 'greenpath_membership_check_requirements', 15 ); // Permission 15 is set because Core plugin is loaded on 10
}

if ( ! function_exists( 'greenpath_membership_admin_notice_content' ) ) {
	/**
	 * Function that display the error message if the requirements are not met
	 */
	function greenpath_membership_admin_notice_content() {
		echo sprintf( '<div class="notice notice-error"><p>%s</p></div>', esc_html__( 'Nutterlygood Core plugin is required for Nutterlygood Membership plugin to work properly. Please install/activate it first.', 'greenpath-membership' ) );

		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
	}
}
