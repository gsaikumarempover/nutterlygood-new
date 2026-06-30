<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Greenpath_Welcome_Page' ) ) {
	/**
	 * Welcome page theme class
	 */
	class Greenpath_Welcome_Page {
		private static $instance;

		/**
		 * @return Greenpath_Welcome_Page
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function __construct() {
			// theme activation hook
			add_action( 'after_switch_theme', array( $this, 'init_activation_hook' ) );

			// welcome page redirect on theme activation
			add_action( 'admin_init', array( $this, 'welcome_page_redirect' ) );

			// add welcome page into theme options
			add_action( 'admin_menu', array( $this, 'create_welcome_page' ), 12 );

			// enqueue theme welcome page scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		}

		/**
		 * Init hooks on theme activation
		 */
		function init_activation_hook() {
			if ( ! is_network_admin() ) {
				set_transient( '_Greenpath_Welcome_Page_redirect', 1, 30 );
			}
		}

		/**
		 * Redirect to welcome page on theme activation
		 */
		function welcome_page_redirect() {
			// if no activation redirect, bail
			if ( ! get_transient( '_Greenpath_Welcome_Page_redirect' ) ) {
				return;
			}

			// delete the redirect transient
			delete_transient( '_Greenpath_Welcome_Page_redirect' );

			// if activating from network, or bulk, bail
			if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
				return;
			}

			// redirect to welcome page
			wp_safe_redirect( add_query_arg( array( 'page' => 'Greenpath_Welcome_Page' ), esc_url( admin_url( 'themes.php' ) ) ) );
			exit;
		}

		/**
		 * Add welcome page
		 */
		function create_welcome_page() {
			add_theme_page(
				esc_html__( 'About', 'nuttergood' ),
				esc_html__( 'About', 'nuttergood' ),
				'edit_theme_options',
				'Greenpath_Welcome_Page',
				array( $this, 'welcome_page_content' )
			);

			remove_submenu_page( 'themes.php', 'Greenpath_Welcome_Page' );
		}

		/**
		 * Render welcome page content
		 */
		function welcome_page_content() {
			$params = array();

			$theme                       = wp_get_theme();
			$params['theme']             = $theme;
			$params['theme_name']        = esc_html( $theme->get( 'Name' ) );
			$params['theme_description'] = esc_html( $theme->get( 'Description' ) );
			$params['theme_version']     = $theme->get( 'Version' );
			$params['theme_screenshot']  = file_exists( GREENPATH_ROOT_DIR . '/screenshot.png' ) ? GREENPATH_ROOT . '/screenshot.png' : GREENPATH_ROOT . '/screenshot.jpg';

			greenpath_template_part( 'welcome', 'templates/welcome', '', $params );
		}

		/**
		 * Enqueue welcome page scripts
		 *
		 * @param string $hook
		 */
		function enqueue_styles( $hook ) {

			if ( 'appearance_page_Greenpath_Welcome_Page' === $hook ) {
				wp_enqueue_style( 'greenpath-welcome-page-style', GREENPATH_INC_ROOT . '/welcome/assets/admin/css/welcome.min.css' );
			}
		}
	}

	Greenpath_Welcome_Page::get_instance();
}
