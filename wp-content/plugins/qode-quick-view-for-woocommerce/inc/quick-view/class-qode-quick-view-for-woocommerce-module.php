<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Quick_View_For_WooCommerce_Module' ) ) {
	class Qode_Quick_View_For_WooCommerce_Module {
		private static $instance;

		public function __construct() {

			// Make sure the module is enabled by Global options.
			if ( $this->is_enabled() ) {

				// Load "Quick View" template.
				add_action( 'wp_footer', array( $this, 'load_quick_view' ) );

				// Set "Quick View" button position (permission 22 is set to be after the module initialization).
				add_action( 'init', array( $this, 'set_button_loop_position' ), 22 );

				// Include WooCommerce core files during rest api request - mandatory to include default templates hooks.
				$this->include_quick_view_woocommerce_core_files();

				// Prevent redirection to single product page on add to cart action.
				add_filter( 'woocommerce_add_to_cart_form_action', array( $this, 'prevent_redirect_to_single_page' ), 10, 1 );

				// Set inline styles.
				add_filter( 'qode_quick_view_for_woocommerce_filter_add_inline_style', array( $this, 'set_inline_quick_view_styles' ) );
			}
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Quick_View_For_WooCommerce_Module
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function is_enabled() {
			$enable           = 'yes' === qode_quick_view_for_woocommerce_get_option_value( 'admin', 'qode_quick_view_for_woocommerce_enable_quick_view' );
			$enable_on_mobile = 'yes' === qode_quick_view_for_woocommerce_get_option_value( 'admin', 'qode_quick_view_for_woocommerce_enable_quick_view_on_mobile' );
			$is_mobile        = wp_is_mobile();

			return apply_filters( 'qode_quick_view_for_woocommerce_filter_is_enabled', ( ! $is_mobile && $enable ) || ( $is_mobile && $enable_on_mobile ) );
		}

		public function load_quick_view() {
			$this->include_woocommerce_quick_view_scripts();
			$this->include_perfect_scrollbar_quick_view_scripts();

			qode_quick_view_for_woocommerce_template_part( 'quick-view/templates', 'content', '', $this->get_quick_view_default_params() );
		}

		public function get_quick_view_default_params() {
			$params['quick_view_classes']     = $this->set_quick_view_classes();
			$params['quick_view_type']        = $this->get_quick_view_type_option();
			$params['quick_view_type_mobile'] = $this->get_quick_view_type_mobile_option();
			$params['quick_view_page_id']     = $this->get_quick_view_page_id();

			return $params;
		}
		public function set_quick_view_classes() {
			$classes = array( 'qqvfw' );

			return apply_filters( 'qode_quick_view_for_woocommerce_filter_set_quick_view_classes', $classes );
		}

		public function set_button_loop_position() {
			$button_loop_position = apply_filters( 'qode_quick_view_for_woocommerce_filter_button_loop_position_default_value', 'after-add-to-cart' );

			$button_loop_position_map = apply_filters(
				'qode_quick_view_for_woocommerce_filter_quick_view_button_loop_position',
				array(
					'after-add-to-cart' => array(
						// button is hooked with priority 10 - woocommerce_template_loop_add_to_cart.
						'hook'     => 'woocommerce_after_shop_loop_item',
						'priority' => 15,
					),
				),
				$button_loop_position,
				qode_quick_view_for_woocommerce_check_is_block_template_page( 'archive-product' )
			);

			if ( qode_quick_view_for_woocommerce_check_is_block_template_page( 'archive-product' ) ) {
				$this->add_block_button( $button_loop_position );
			} elseif ( isset( $button_loop_position_map[ $button_loop_position ] ) ) {
				$this->add_button_position_logic( $button_loop_position_map[ $button_loop_position ] );
			}
		}

		public function add_button_position_logic( $button_position_map ) {
			$button_position_hook          = $button_position_map['hook'] ?? '';
			$button_position_hook_priority = $button_position_map['priority'] ?? 10;

			if ( is_array( $button_position_hook ) ) {
				foreach ( $button_position_hook as $hook_index => $hook_name ) {
					$hook_priority = $button_position_hook_priority;

					if ( is_array( $button_position_hook_priority ) ) {
						if ( isset( $button_position_hook_priority[ $hook_index ] ) ) {
							$hook_priority = $button_position_hook_priority[ $hook_index ];
						} else {
							$hook_priority = $button_position_hook_priority[0];
						}
					}

					add_action( $hook_name, array( $this, 'add_button' ), intval( $hook_priority ) );
				}
			} else {
				add_action( $button_position_hook, array( $this, 'add_button' ), intval( $button_position_hook_priority ) );
			}
		}

		public function get_button( $item_id = '' ) {

			if ( class_exists( 'Qode_Quick_View_For_WooCommerce_Quick_View_Button_Shortcode' ) ) {

				$shortcode_atts = array(
					'item_id' => $item_id,
				);

				return Qode_Quick_View_For_WooCommerce_Quick_View_Button_Shortcode::call_shortcode( $shortcode_atts );
			} else {
				return '';
			}
		}

		public function add_button() {
			if ( $this->is_enabled() ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo qode_quick_view_for_woocommerce_framework_wp_kses_html( 'html', $this->get_button() );
			}
		}

		public function add_block_button( $position ) {
			switch ( $position ) {
				case 'after-add-to-cart':
					add_filter( 'render_block_woocommerce/product-button', array( $this, 'add_button_after_block' ), 10, 3 );
					break;
				case 'before-add-to-cart':
					add_filter( 'render_block_woocommerce/product-button', array( $this, 'add_button_before_block' ), 10, 3 );
					break;
				case 'after-title':
					add_filter( 'render_block_core/post-title', array( $this, 'add_button_after_block' ), 10, 3 );
					break;
				case 'before-title':
					add_filter( 'render_block_core/post-title', array( $this, 'add_button_before_block' ), 10, 3 );
					break;
				case 'after-rating':
					add_filter( 'render_block_woocommerce/product-rating', array( $this, 'add_button_after_block' ), 10, 3 );
					break;
				case 'after-price':
					add_filter( 'render_block_woocommerce/product-price', array( $this, 'add_button_after_block' ), 10, 3 );
					break;
				case 'on-product-image':
					add_filter( 'render_block_woocommerce/product-image', array( $this, 'add_button_after_block_with_wrapper' ), 10, 3 );
					break;
				case 'before-product-image':
					add_filter( 'render_block_woocommerce/product-image', array( $this, 'add_button_after_block' ), 10, 3 );
					break;
			}
		}

		public function add_button_before_block( $block_content, $parsed_block, $block ) {
			return $this->add_button_in_block( $block_content, $parsed_block, $block );
		}

		public function add_button_after_block( $block_content, $parsed_block, $block ) {
			return $this->add_button_in_block( $block_content, $parsed_block, $block, 'after' );
		}

		public function add_button_after_block_with_wrapper( $block_content, $parsed_block, $block ) {
			return $this->add_button_in_block( $block_content, $parsed_block, $block, 'after', true );
		}

		public function add_button_in_block( $block_content, $parsed_block, $block, $position = 'before', $with_wrapper = false ) {
			global $post;
			global $product;

			$post_id = $block->context['postId'] ?? false;
			$post_id = $post_id ?? isset( $post->ID ) ? $post->ID : false;

			if ( ! empty( $post_id ) ) {
				$product = wc_get_product( absint( $post_id ) );
			}

			if ( empty( $product ) ) {
				return $block_content;
			}

			$button = $this->get_button( $product->get_id() );

			if ( $with_wrapper ) {

				if ( 'after' === $position ) {
					return "<div class='qqvfw-block-wrapper'>$block_content $button</div>";
				} else {
					return "<div class='qqvfw-block-wrapper'>$button $block_content</div>";
				}
			} else {

				if ( 'after' === $position ) {
					return "$block_content $button";
				} else {
					return "$button $block_content";
				}
			}
		}

		public function include_woocommerce_quick_view_scripts() {
			if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {

				do_action( 'qode_quick_view_for_woocommerce_action_before_woocommerce_quick_view_scripts' );

				wp_enqueue_script( 'wc-add-to-cart-variation' );

				if ( apply_filters( 'qode_quick_view_for_woocommerce_filter_is_product_zoom_enabled', true ) ) {

					if ( ! current_theme_supports( 'wc-product-gallery-zoom' ) ) {
						add_theme_support( 'wc-product-gallery-zoom' );
					}

					wp_enqueue_script( 'zoom' );
				}

				if ( apply_filters( 'qode_quick_view_for_woocommerce_filter_is_product_lightbox_enabled', true ) ) {

					if ( ! current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
						add_theme_support( 'wc-product-gallery-lightbox' );
					}

					wp_enqueue_script( 'photoswipe-ui-default' );
					wp_enqueue_style( 'photoswipe-default-skin' );

					if ( false === has_action( 'wp_footer', 'woocommerce_photoswipe' ) ) {
						add_action( 'wp_footer', 'woocommerce_photoswipe', 15 );
					}
				}

				wp_enqueue_script( 'wc-single-product' );

				do_action( 'qode_quick_view_for_woocommerce_action_after_woocommerce_quick_view_scripts' );
			}
		}

		public function include_perfect_scrollbar_quick_view_scripts() {
			// Enqueue CSS styles.
			wp_enqueue_style( 'qqvfw-perfect-scrollbar', QODE_QUICK_VIEW_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/plugins/perfect-scrollbar/perfect-scrollbar.min.css', array(), QODE_QUICK_VIEW_FOR_WOOCOMMERCE_VERSION );
			// Enqueue JS styles.
			wp_enqueue_script( 'qqvfw-perfect-scrollbar', QODE_QUICK_VIEW_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/plugins/perfect-scrollbar/perfect-scrollbar.min.js', array(), '1.5.3', true );
		}

		public function include_quick_view_templates() {
			// Image.
			if ( apply_filters( 'qode_quick_view_for_woocommerce_filter_is_product_image_enabled', true ) ) {

				if ( apply_filters( 'qode_quick_view_for_woocommerce_filter_is_product_sale_flash_enabled', true ) ) {
					// permission 10 is set because woocommerce_show_product_loop_sale_flash hook is added on 10.
					add_action( 'qode_quick_view_for_woocommerce_action_product_image', 'woocommerce_show_product_sale_flash', 10 );
				}

				// permission 20 is set because it is default woocommerce priority.
				add_action( 'qode_quick_view_for_woocommerce_action_product_image', 'woocommerce_show_product_images', 20 );
			}

			// Summary.
			if ( apply_filters( 'qode_quick_view_for_woocommerce_filter_is_product_title_enabled', true ) ) {
				add_action( 'qode_quick_view_for_woocommerce_action_product_summary', 'woocommerce_template_single_title', 5 );
			}

			if ( apply_filters( 'qode_quick_view_for_woocommerce_filter_is_product_price_enabled', true ) ) {
				add_action( 'qode_quick_view_for_woocommerce_action_product_summary', 'woocommerce_template_single_price', 10 );
			}

			if ( apply_filters( 'qode_quick_view_for_woocommerce_filter_is_product_rating_enabled', true ) ) {
				add_action( 'qode_quick_view_for_woocommerce_action_product_summary', 'woocommerce_template_single_rating', 15 );
			}

			if ( apply_filters( 'qode_quick_view_for_woocommerce_filter_is_product_excerpt_enabled', true ) ) {
				add_action( 'qode_quick_view_for_woocommerce_action_product_summary', 'woocommerce_template_single_excerpt', 20 );
			}

			if ( apply_filters( 'qode_quick_view_for_woocommerce_filter_is_product_add_to_cart_button_enabled', true ) ) {
				add_action( 'qode_quick_view_for_woocommerce_action_product_summary', 'woocommerce_template_single_add_to_cart', 25 );
				add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_to_cart_nonce' ), 25 );
			}

			if ( apply_filters( 'qode_quick_view_for_woocommerce_filter_is_product_meta_enabled', true ) ) {
				add_action( 'qode_quick_view_for_woocommerce_action_product_summary', 'woocommerce_template_single_meta', 30 );
			}
		}

		public function is_ajax_request() {
			// phpcs:ignore WordPress.Security.NonceVerification
			return WC()->is_rest_api_request() && isset( $_GET['route'] ) && 'quick-view' === $_GET['route'];
		}

		public function include_quick_view_woocommerce_core_files() {
			if ( $this->is_ajax_request() && defined( 'WC_ABSPATH' ) ) {
				include_once WC_ABSPATH . 'includes/wc-template-hooks.php';
			}
		}

		public function add_to_cart_nonce() {
			wp_nonce_field( 'qode-quick-view-for-woocommerce-create-add-to-cart-nonce', 'qode-quick-view-for-woocommerce-add-to-cart-nonce' );
		}

		public function prevent_redirect_to_single_page( $value ) {
			if ( $this->is_ajax_request() ) {
				$value = '';
			}

			return $value;
		}

		public function get_quick_view_type_option() {
			return apply_filters( 'qode_quick_view_for_woocommerce_filter_set_quick_view_type', 'pop-up' );
		}

		public function get_quick_view_type_mobile_option() {
			return apply_filters( 'qode_quick_view_for_woocommerce_filter_set_quick_view_type_mobile', 'pop-up' );
		}

		public function get_quick_view_page_id() {
			$post_id = get_the_ID();

			if ( ( function_exists( 'is_shop' ) && is_shop() ) || ( function_exists( 'is_product_category' ) && is_product_category() ) || ( function_exists( 'is_product_tag' ) && is_product_tag() ) ) {
				// Get page id from options table.
				$shop_id = get_option( 'woocommerce_shop_page_id' );

				if ( ! empty( $shop_id ) ) {
					$post_id = $shop_id;
				}
			}

			return $post_id;
		}

		public function set_inline_quick_view_styles( $style ) {
			$style .= $this->get_general_style();
			$style .= $this->get_close_button_style();

			return $style;
		}

		public function get_general_style() {
			$styles           = array();
			$style            = '';
			$background_color = qode_quick_view_for_woocommerce_get_option_value( 'admin', 'qode_quick_view_for_woocommerce_background_color' );

			if ( ! empty( $background_color ) ) {
				$styles['--qqvfw-qv-background-color'] = $background_color;
			}

			if ( ! empty( $styles ) ) {
				$style .= qode_quick_view_for_woocommerce_dynamic_style( '.qqvfw', $styles );
			}

			return $style;
		}

		public function get_close_button_style() {
			$styles             = array();
			$style              = '';
			$button_color       = qode_quick_view_for_woocommerce_get_option_value( 'admin', 'qode_quick_view_for_woocommerce_close_icon_color' );
			$button_hover_color = qode_quick_view_for_woocommerce_get_option_value( 'admin', 'qode_quick_view_for_woocommerce_close_icon_hover_color' );

			if ( ! empty( $button_color ) ) {
				$styles['--qqvfw-qv-close-button-color'] = $button_color;
			}

			if ( ! empty( $button_hover_color ) ) {
				$styles['--qqvfw-qv-close-button-hover-color'] = $button_hover_color;
			}

			if ( ! empty( $styles ) ) {
				$style .= qode_quick_view_for_woocommerce_dynamic_style( '.qqvfw .qqvfw-m-close', $styles );
			}

			return $style;
		}
	}
}

if ( ! function_exists( 'qode_quick_view_for_woocommerce_init_quick_view_module' ) ) {
	/**
	 * Init main quick view module instance.
	 */
	function qode_quick_view_for_woocommerce_init_quick_view_module() {
		Qode_Quick_View_For_WooCommerce_Module::get_instance();
	}

	// Permission 15 is set in order to load after option initialization ( init_options method).
	add_action( 'init', 'qode_quick_view_for_woocommerce_init_quick_view_module', 15 );
}
