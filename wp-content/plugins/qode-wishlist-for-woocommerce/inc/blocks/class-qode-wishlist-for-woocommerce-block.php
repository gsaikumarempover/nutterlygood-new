<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Wishlist_For_WooCommerce_Block' ) ) {
	class Qode_Wishlist_For_WooCommerce_Block {
		private $blocks_namespace;
		private $block_name;
		private $block_options = array();
		private $block_editor_script;

		public function __construct() {
			// Set namespace for blocks.
			$this->set_blocks_namespace( 'qode-wishlist-for-woocommerce' );

			// Register block.
			add_action( 'init', array( $this, 'register_block' ) );

			// Loads core block assets only when they are rendered on the page - WordPress 5.8.
			add_filter( 'should_load_separate_core_block_assets', '__return_true' );
		}

		public function get_blocks_namespace() {
			return $this->blocks_namespace;
		}

		public function set_blocks_namespace( $blocks_namespace ) {
			$this->blocks_namespace = $blocks_namespace;
		}

		public function get_block_name() {
			return $this->block_name;
		}

		public function set_block_name( $block_name ) {
			$this->block_name = $block_name;
		}

		public function get_block_options() {
			return $this->block_options;
		}

		public function set_block_options( $block_options ) {
			$this->block_options = $block_options;
		}

		public function get_block_editor_script() {
			return $this->block_editor_script;
		}

		public function set_block_editor_script( $block_editor_script ) {
			$this->block_editor_script = $block_editor_script;
		}

		public function register_block() {
			// Get blocks options.
			$block_options = $this->get_block_options();

			// Set blocks scripts.
			$this->set_blocks_scripts();

			register_block_type(
				$this->get_blocks_namespace() . '/' . $this->get_block_name(),
				array_merge(
					array(
						'editor_script' => $this->get_block_editor_script(),
					),
					$block_options
				)
			);
		}

		public function set_blocks_scripts() {
			$block_name = $this->get_block_name();

			if ( ! empty( $block_name ) ) {
				$block_name             = esc_attr( $block_name );
				$block_dir_path         = QODE_WISHLIST_FOR_WOOCOMMERCE_ASSETS_PATH . '/blocks/' . $block_name;
				$block_url_path         = QODE_WISHLIST_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/blocks/' . $block_name;
				$premium_block_dir_path = '';
				$premium_block_url_path = '';

				if ( qode_wishlist_for_woocommerce_is_installed( 'wishlist-premium' ) ) {
					$premium_block_dir_path = QODE_WISHLIST_FOR_WOOCOMMERCE_PREMIUM_ASSETS_PATH . '/blocks/' . $block_name;
					$premium_block_url_path = QODE_WISHLIST_FOR_WOOCOMMERCE_PREMIUM_ASSETS_URL_PATH . '/blocks/' . $block_name;
				}

				// Check if editor script file exists.
				if ( file_exists( $block_dir_path . '.min.js' ) ) {
					// Set block editor script dependency.
					$editor_script_dependency = array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-i18n', 'wp-api-fetch' );

					if ( ! empty( $premium_block_dir_path ) && file_exists( $premium_block_dir_path . '-extended.min.js' ) ) {
						// phpcs:ignore WordPress.WP.EnqueuedResourceParameters
						wp_register_script(
							'qode-wishlist-for-woocommerce-blocks-' . $block_name . '-extended',
							$premium_block_url_path . '-extended.min.js',
							$editor_script_dependency
						);

						$editor_script_dependency[] = 'qode-wishlist-for-woocommerce-blocks-' . $block_name . '-extended';
					}

					// Register block editor script.
					// phpcs:ignore WordPress.WP.EnqueuedResourceParameters
					wp_register_script( 'qode-wishlist-for-woocommerce-blocks-' . $block_name, $block_url_path . '.min.js', $editor_script_dependency );

					wp_localize_script(
						'qode-wishlist-for-woocommerce-blocks-' . $block_name,
						'qodeWishlistForWooCommerceAdminGlobal',
						array(
							'product_list' => qode_wishlist_for_woocommerce_get_cpt_items(),
						)
					);

					// Set block editor script.
					$this->set_block_editor_script( 'qode-wishlist-for-woocommerce-blocks-' . $block_name );
				}
			}
		}
	}
}
