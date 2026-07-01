<?php

if ( ! class_exists( 'Greenpath_Handler' ) ) {
	/**
	 * Main theme class with configuration
	 */
	class Greenpath_Handler {
		private static $instance;

		public function __construct() {

			// Include required files
			require_once get_template_directory() . '/constants.php';
			require_once GREENPATH_ROOT_DIR . '/helpers/helper.php';

			// Include theme's style and inline style
			add_action( 'wp_enqueue_scripts', array( $this, 'include_css_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_style' ) );

			// Include theme's script and localize theme's main js script
			add_action( 'wp_enqueue_scripts', array( $this, 'include_js_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'localize_js_scripts' ) );

			// Include theme's 3rd party plugins styles
			add_action( 'greenpath_action_before_main_css', array( $this, 'include_plugins_styles' ) );

			// Include theme's 3rd party plugins scripts
			add_action( 'greenpath_action_before_main_js', array( $this, 'include_plugins_scripts' ) );

			// Add pingback header
			add_action( 'wp_head', array( $this, 'add_pingback_header' ), 1 );

			// Include theme's skip link
			add_action( 'greenpath_action_after_body_tag_open', array( $this, 'add_skip_link' ), 5 );

			// Include theme's Google fonts
			add_action( 'greenpath_action_before_main_css', array( $this, 'include_google_fonts' ) );

			// Add theme's supports feature
			add_action( 'after_setup_theme', array( $this, 'set_theme_support' ) );

			// Enqueue supplemental block editor styles
			add_action( 'enqueue_block_editor_assets', array( $this, 'editor_customizer_styles' ) );

			// Add theme's body classes
			add_filter( 'body_class', array( $this, 'add_body_classes' ) );

			// Include modules
			add_action( 'after_setup_theme', array( $this, 'include_modules' ) );
		}

		/**
		 * @return Greenpath_Handler
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function include_css_scripts() {
			// CSS dependency variable
			$main_css_dependency = apply_filters( 'greenpath_filter_main_css_dependency', array( 'swiper' ) );

			// Hook to include additional scripts before theme's main style
			do_action( 'greenpath_action_before_main_css' );

			// Enqueue theme's main style
			wp_enqueue_style( 'greenpath-main', GREENPATH_ASSETS_CSS_ROOT . '/main.min.css', $main_css_dependency );

			// QODE product list icons — match GreenPath carousel flex layout
			$qode_product_list_css = GREENPATH_ASSETS_CSS_ROOT_DIR . '/qode-greenpath-product-list.css';
			if ( file_exists( $qode_product_list_css ) ) {
				wp_enqueue_style(
					'nuttergood-qode-product-list',
					GREENPATH_ASSETS_CSS_ROOT . '/qode-greenpath-product-list.css',
					array( 'greenpath-main' ),
					filemtime( $qode_product_list_css )
				);
			}

			$footer_greenpath_css = GREENPATH_ASSETS_CSS_ROOT_DIR . '/footer-greenpath.css';
			if ( file_exists( $footer_greenpath_css ) ) {
				wp_enqueue_style(
					'nuttergood-footer-greenpath',
					GREENPATH_ASSETS_CSS_ROOT . '/footer-greenpath.css',
					array( 'greenpath-main' ),
					filemtime( $footer_greenpath_css )
				);
			}

			// Enqueue theme's style
			wp_enqueue_style( 'greenpath-style', GREENPATH_ROOT . '/style.css' );

			// Hook to include additional scripts after theme's main style
			do_action( 'greenpath_action_after_main_css' );
		}

		function add_inline_style() {
			$style = apply_filters( 'greenpath_filter_add_inline_style', '' );

			if ( ! empty( $style ) ) {
				wp_add_inline_style( 'greenpath-style', $style );
			}
		}

		function include_js_scripts() {
			// JS dependency variable
			$main_js_dependency = apply_filters( 'greenpath_filter_main_js_dependency', array( 'jquery' ) );

			// Hook to include additional scripts before theme's main script
			do_action( 'greenpath_action_before_main_js', $main_js_dependency );

			// Enqueue theme's main script
			wp_enqueue_script( 'greenpath-main-js', GREENPATH_ASSETS_JS_ROOT . '/main.min.js', $main_js_dependency, false, true );

			// Hook to include additional scripts after theme's main script
			do_action( 'greenpath_action_after_main_js' );

			$product_carousel_cart_js = GREENPATH_ASSETS_JS_ROOT_DIR . '/product-carousel-cart.js';
			if ( file_exists( $product_carousel_cart_js ) ) {
				wp_enqueue_script(
					'nuttergood-product-carousel-cart',
					GREENPATH_ASSETS_JS_ROOT . '/product-carousel-cart.js',
					array( 'jquery', 'wc-add-to-cart' ),
					filemtime( $product_carousel_cart_js ),
					true
				);
			}

			$product_list_icons_js = GREENPATH_ASSETS_JS_ROOT_DIR . '/farmley-product-list-icons.js';
			if ( file_exists( $product_list_icons_js ) ) {
				wp_enqueue_script(
					'nuttergood-product-list-icons',
					GREENPATH_ASSETS_JS_ROOT . '/farmley-product-list-icons.js',
					array( 'jquery' ),
					filemtime( $product_list_icons_js ),
					true
				);
			}

			$back_to_top_fix_js = GREENPATH_ASSETS_JS_ROOT_DIR . '/back-to-top-fix.js';
			if ( file_exists( $back_to_top_fix_js ) ) {
				wp_enqueue_script(
					'nuttergood-back-to-top-fix',
					GREENPATH_ASSETS_JS_ROOT . '/back-to-top-fix.js',
					array( 'jquery', 'greenpath-core-script' ),
					filemtime( $back_to_top_fix_js ),
					true
				);
			}

			// Include comment reply script
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}
		}

		function localize_js_scripts() {
			$global = apply_filters(
				'greenpath_filter_localize_main_js',
				array(
					'adminBarHeight' => is_admin_bar_showing() ? 32 : 0,
					'iconArrowLeft'  => greenpath_get_svg_icon( 'slider-arrow-left' ),
					'iconArrowRight' => greenpath_get_svg_icon( 'slider-arrow-right' ),
					'iconClose'      => greenpath_get_svg_icon( 'close' ),
				)
			);

			wp_localize_script(
				'greenpath-main-js',
				'qodefGlobal',
				array(
					'vars' => $global,
				)
			);
		}

		function include_plugins_styles() {

			// Enqueue 3rd party plugins style
			wp_enqueue_style( 'swiper', GREENPATH_ASSETS_ROOT . '/plugins/swiper/swiper.min.css' );
			wp_enqueue_style( 'magnific-popup', GREENPATH_ASSETS_ROOT . '/plugins/magnific-popup/magnific-popup.css' );
		}

		function include_plugins_scripts() {

			// JS dependency variables
			$js_3rd_party_dependency = apply_filters( 'greenpath_filter_js_3rd_party_dependency', 'jquery' );

			// Enqueue 3rd party plugins script
			wp_enqueue_script( 'swiper', GREENPATH_ASSETS_ROOT . '/plugins/swiper/swiper.min.js', array( $js_3rd_party_dependency ), false, true );
			wp_enqueue_script( 'jquery-magnific-popup', GREENPATH_ASSETS_ROOT . '/plugins/magnific-popup/jquery.magnific-popup.min.js', array( $js_3rd_party_dependency ), false, true );
		}

		function add_pingback_header() {
			if ( is_singular() && pings_open( get_queried_object() ) ) { ?>
				<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
				<?php
			}
		}

		function add_skip_link() {
			echo '<a class="skip-link screen-reader-text" href="#qodef-page-content">' . esc_html__( 'Skip to the content', 'nuttergood' ) . '</a>';
		}

		function include_google_fonts() {
			$is_enabled = boolval( apply_filters( 'greenpath_filter_enable_google_fonts', true ) );

			if ( $is_enabled ) {
				$font_subset_array = array(
					'latin-ext',
				);

				$font_weight_array = array(
					'300',
					'400',
					'500',
					'600',
					'700',
				);

				$default_font_family = array(
					'Manrope',
				);

				$font_weight_str = implode( ',', array_unique( apply_filters( 'greenpath_filter_google_fonts_weight_list', $font_weight_array ) ) );
				$font_subset_str = implode( ',', array_unique( apply_filters( 'greenpath_filter_google_fonts_subset_list', $font_subset_array ) ) );
				$fonts_array     = apply_filters( 'greenpath_filter_google_fonts_list', $default_font_family );

				if ( ! empty( $fonts_array ) ) {
					$modified_default_font_family = array();

					foreach ( $fonts_array as $font ) {
						$modified_default_font_family[] = $font . ':' . $font_weight_str;
					}

					$default_font_string = implode( '|', $modified_default_font_family );

					$fonts_full_list_args = array(
						'family'  => urlencode( $default_font_string ),
						'subset'  => urlencode( $font_subset_str ),
						'display' => 'swap',
					);

					$google_fonts_url = add_query_arg( $fonts_full_list_args, 'https://fonts.googleapis.com/css' );
					wp_enqueue_style( 'greenpath-google-fonts', esc_url_raw( $google_fonts_url ), array(), '1.0.0' );
				}
			}
		}

		function set_theme_support() {

			// Make theme available for translation
			load_theme_textdomain( 'nuttergood', GREENPATH_ROOT_DIR . '/languages' );

			// Add support for feed links
			add_theme_support( 'automatic-feed-links' );

			// Add support for title tag
			add_theme_support( 'title-tag' );

			// Add support for post thumbnails
			add_theme_support( 'post-thumbnails' );

			// Add theme support for Custom Logo
			add_theme_support( 'custom-logo' );

			// Add support for full and wide align images.
			add_theme_support( 'align-wide' );

			// Set the default content width
			global $content_width;
			if ( ! isset( $content_width ) ) {
				$content_width = apply_filters( 'greenpath_filter_set_content_width', 1280 );
			}

			// Add support for post formats
			add_theme_support( 'post-formats', array( 'gallery', 'video', 'audio', 'link', 'quote' ) );

			// Add theme support for editor style
			add_editor_style( GREENPATH_ASSETS_CSS_ROOT . '/editor-style.min.css' );
		}

		function editor_customizer_styles() {

			// Include theme's Google fonts for Gutenberg editor
			$this->include_google_fonts();

			// Add editor customizer style
			wp_enqueue_style( 'greenpath-editor-customizer-styles', GREENPATH_ASSETS_CSS_ROOT . '/editor-customizer-style.css' );

			// Add Gutenberg blocks style
			wp_enqueue_style( 'greenpath-gutenberg-blocks-style', GREENPATH_INC_ROOT . '/gutenberg/assets/admin/css/gutenberg-blocks.min.css' );
		}

		function add_body_classes( $classes ) {
			$current_theme = wp_get_theme();
			$theme_name    = esc_attr( str_replace( ' ', '-', strtolower( $current_theme->get( 'Name' ) ) ) );
			$theme_version = esc_attr( $current_theme->get( 'Version' ) );

			// Check is child theme activated
			if ( $current_theme->parent() ) {

				// Add child theme version
				$child_theme_suffix = strpos( $theme_name, 'child' ) === false ? '-child' : '';

				$classes[] = $theme_name . $child_theme_suffix . '-' . $theme_version;

				// Get main theme variables
				$current_theme = $current_theme->parent();
				$theme_name    = esc_attr( str_replace( ' ', '-', strtolower( $current_theme->get( 'Name' ) ) ) );
				$theme_version = esc_attr( $current_theme->get( 'Version' ) );
			}

			if ( $current_theme->exists() ) {
				$classes[] = $theme_name . '-' . $theme_version;
			}

			// Set default grid size value
			$classes['grid_size'] = 'qodef-content-grid-1280';

			return apply_filters( 'greenpath_filter_add_body_classes', $classes );
		}

		function include_modules() {

			// Hook to include additional files before modules inclusion
			do_action( 'greenpath_action_before_include_modules' );

			$farmley = get_template_directory() . '/inc/farmley/include.php';
			if ( file_exists( $farmley ) ) {
				include_once $farmley;
			}

			foreach ( glob( GREENPATH_INC_ROOT_DIR . '/*/include.php' ) as $module ) {
				include_once $module; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			}

			// Hook to include additional files after modules inclusion
			do_action( 'greenpath_action_after_include_modules' );
		}
	}

	Greenpath_Handler::get_instance();
}

if ( ! function_exists( 'nuttergood_filter_body_classes' ) ) {
	/**
	 * Replace legacy greenpath body classes with nuttergood branding.
	 */
	function nuttergood_filter_body_classes( $classes ) {
		return array_map(
			static function ( $class ) {
				return str_replace(
					array( 'greenpath-core', 'greenpath-membership' ),
					array( 'nuttergood-core', 'nuttergood-membership' ),
					$class
				);
			},
			$classes
		);
	}

	add_filter( 'body_class', 'nuttergood_filter_body_classes' );
}

if ( ! function_exists( 'nuttergood_home_slider_css' ) ) {
	/**
	 * Equal-height slides for homepage product + category carousels.
	 */
	function nuttergood_home_slider_css( $style ) {
		if ( ! is_front_page() ) {
			return $style;
		}

		$style .= '
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981,
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 > .elementor-widget-container,
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 .qodef-product-slider-holder {
	overflow: visible !important;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 .qodef-woo-product-list.qodef-swiper-container li.product {
	background: transparent !important;
	border: 0 !important;
	overflow: visible !important;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 .qodef-woo-product-list.qodef-swiper-container .qodef-e-inner.ng-farmley-card,
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 .qodef-woo-product-list.qodef-swiper-container .qodef-e-inner {
	height: 100% !important;
	min-height: 100%;
	overflow: visible !important;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 .qodef-woo-product-list.qodef-swiper-container .swiper-wrapper {
	align-items: stretch !important;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 .qodef-woo-product-list.qodef-swiper-container .swiper-slide.product,
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 .qodef-woo-product-list.qodef-swiper-container li.product {
	height: auto !important;
	align-self: stretch;
}
@media (max-width: 767px) {
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 .qodef-woo-product-list.qodef-swiper-container .qodef-e-inner.ng-farmley-card,
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 .qodef-woo-product-list.qodef-swiper-container .qodef-e-inner {
	height: auto !important;
	min-height: 0 !important;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 .qodef-woo-product-list.qodef-swiper-container .swiper-wrapper {
	align-items: flex-start !important;
	height: auto !important;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 .qodef-woo-product-list.qodef-swiper-container .swiper-slide.product,
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 .qodef-woo-product-list.qodef-swiper-container li.product {
	align-self: flex-start !important;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-a463981 .qodef-woo-product-list.qodef-swiper-container {
	height: auto !important;
	min-height: 0 !important;
}
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 {
	display: grid !important;
	grid-template-columns: minmax(0, 1.62fr) minmax(0, 1fr);
	grid-template-rows: minmax(0, 1fr) minmax(0, 1fr);
	gap: 30px;
	align-items: stretch;
	width: 100% !important;
	min-height: 420px;
	aspect-ratio: 1624 / 609;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 > .elementor-element-ce75316,
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 > .elementor-element-fae9d0a {
	width: 100% !important;
	max-width: 100% !important;
	flex: none !important;
	align-self: stretch !important;
	min-height: 0;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-ce75316 {
	grid-column: 1;
	grid-row: 1 / -1;
	display: flex !important;
	flex-direction: column !important;
	height: 100%;
	min-height: 0;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-fae9d0a {
	grid-column: 2;
	grid-row: 1 / -1;
	display: flex !important;
	flex-direction: column !important;
	gap: 30px;
	height: 100%;
	min-height: 0;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-192bbec,
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-element-ce75316 > .elementor-widget,
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-element-9f9672e,
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-element-62f9b1e {
	flex: 1 1 0 !important;
	width: 100% !important;
	height: 100% !important;
	min-height: 0 !important;
	margin: 0 !important;
	padding: 0 !important;
	display: flex !important;
	flex-direction: column !important;
	align-self: stretch !important;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-widget-container {
	flex: 1 1 auto !important;
	display: flex !important;
	flex-direction: column !important;
	position: relative;
	width: 100% !important;
	height: 100% !important;
	min-height: 0 !important;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-widget-container > .qodef-shortcode.qodef-banner,
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .qodef-banner {
	display: block !important;
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	width: 100%;
	height: 100%;
	overflow: hidden;
	border-radius: 20px;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .qodef-banner .qodef-m-image {
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	width: 100%;
	height: 100%;
	display: block;
	overflow: hidden;
	border-radius: 20px;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .qodef-banner .qodef-m-image img {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	object-fit: cover;
	object-position: center center;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .qodef-banner .qodef-m-content {
	border-radius: 20px;
}
@media (max-width: 1024px) {
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 {
		display: flex !important;
		flex-direction: column;
		gap: 20px;
		aspect-ratio: auto !important;
		min-height: 0 !important;
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-ce75316,
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-fae9d0a {
		width: 100% !important;
		height: auto !important;
		grid-column: auto;
		grid-row: auto;
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-ce75316 {
		display: flex !important;
		flex-direction: column !important;
		flex: 0 0 auto !important;
		height: auto !important;
		min-height: 0 !important;
		grid-column: unset !important;
		grid-row: unset !important;
		margin: 0 !important;
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-fae9d0a {
		display: flex !important;
		flex-direction: column !important;
		flex-wrap: nowrap !important;
		align-items: stretch !important;
		gap: 16px !important;
		flex: 0 0 auto !important;
		height: auto !important;
		min-height: 0 !important;
		grid-column: unset !important;
		grid-row: unset !important;
		margin: 0 !important;
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-192bbec {
		flex: none !important;
		width: 100% !important;
		height: auto !important;
		min-height: 0 !important;
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-element-ce75316 > .elementor-widget,
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-element-192bbec,
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-element-9f9672e,
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-element-62f9b1e {
		flex: 0 0 auto !important;
		width: 100% !important;
		height: auto !important;
		min-height: 0 !important;
		align-self: auto !important;
		position: relative !important;
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-element-9f9672e,
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-element-62f9b1e {
		flex: 0 0 auto !important;
		width: 100% !important;
		min-width: 0 !important;
		height: auto !important;
		min-height: 0 !important;
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-widget-container {
		flex: 0 0 auto !important;
		height: auto !important;
		min-height: 0 !important;
		position: relative !important;
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-widget-container > .qodef-shortcode.qodef-banner,
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .qodef-banner {
		position: relative !important;
		top: auto !important;
		right: auto !important;
		bottom: auto !important;
		left: auto !important;
		width: 100% !important;
		height: auto !important;
		min-height: 220px;
		aspect-ratio: 16 / 9;
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-192bbec .qodef-banner {
		min-height: 260px;
		aspect-ratio: 16 / 10;
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-element-9f9672e .qodef-banner,
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .elementor-element-62f9b1e .qodef-banner {
		min-height: 220px;
		aspect-ratio: 16 / 10;
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .qodef-banner .qodef-m-image {
		position: absolute !important;
		top: 0 !important;
		right: 0 !important;
		bottom: 0 !important;
		left: 0 !important;
		width: 100% !important;
		height: 100% !important;
		min-height: 100% !important;
		aspect-ratio: auto !important;
		display: block !important;
		overflow: hidden;
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .qodef-banner .qodef-m-image img {
		position: absolute !important;
		top: 0 !important;
		left: 0 !important;
		width: 100% !important;
		height: 100% !important;
		object-fit: cover !important;
		object-position: 78% center !important;
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .qodef-banner::before {
		content: "";
		position: absolute;
		inset: 0;
		z-index: 1;
		border-radius: inherit;
		pointer-events: none;
		background: linear-gradient(90deg, rgba(252, 244, 235, 0.94) 0%, rgba(252, 244, 235, 0.82) 32%, rgba(252, 244, 235, 0.45) 52%, rgba(252, 244, 235, 0.08) 72%, transparent 100%);
	}
	.page-id-' . (int) get_option( 'page_on_front' ) . ' .elementor-element-064fe67 .qodef-banner .qodef-m-content {
		position: absolute !important;
		inset: 0 !important;
		width: 100% !important;
		height: 100% !important;
		z-index: 2 !important;
	}
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .qodef-woo-product-list.qodef-filter--on {
	position: relative;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .qodef-woo-product-list.qodef-filter--on .qodef-m-filter {
	margin-bottom: 28px;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .qodef-woo-product-list.qodef-filter--on.qodef--filter-loading .qodef-filter-pagination-spinner {
	visibility: visible !important;
	z-index: 20;
	position: absolute;
	bottom: auto !important;
	width: 32px;
	height: 32px;
	animation: qode-rotate 0.85s linear infinite;
}
.page-id-' . (int) get_option( 'page_on_front' ) . ' .qodef-woo-product-list.qodef-filter--on.qodef--filter-loading .qodef-grid-inner {
	opacity: 0.35;
	transition: opacity 0.2s ease;
	min-height: 200px;
}
rs-module rs-slide[style*="visibility: hidden"] rs-layer,
rs-module rs-slide[style*="visibility:hidden"] rs-layer {
	opacity: 0 !important;
	visibility: hidden !important;
	pointer-events: none !important;
}
.ng-farmley-hero rs-slide rs-layer {
	text-align: center !important;
}
';

		return $style;
	}

	add_filter( 'greenpath_filter_add_inline_style', 'nuttergood_home_slider_css' );
}

if ( ! function_exists( 'nuttergood_ensure_category_svgs' ) ) {
	/**
	 * Assign GreenPath-style SVG clipart to product categories when missing.
	 */
	function nuttergood_ensure_category_svgs() {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			return;
		}

		$icons_file = WP_CONTENT_DIR . '/../nutterly-category-icons.json';
		if ( ! file_exists( $icons_file ) ) {
			return;
		}

		$icons = json_decode( file_get_contents( $icons_file ), true );
		if ( ! is_array( $icons ) ) {
			return;
		}

		foreach ( $icons as $slug => $info ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( ! $term || is_wp_error( $term ) || empty( $info['svg'] ) ) {
				continue;
			}

			$existing = get_term_meta( $term->term_id, 'qodef_product_category_alternate_svg', true );
			if ( ! empty( $existing ) ) {
				continue;
			}

			update_term_meta( $term->term_id, 'qodef_product_category_alternate_svg', $info['svg'] );

			if ( ! empty( $info['bg'] ) ) {
				update_term_meta( $term->term_id, 'qodef_product_category_svg_bg', $info['bg'] );
			}
		}
	}

	add_action( 'init', 'nuttergood_ensure_category_svgs', 20 );
}
