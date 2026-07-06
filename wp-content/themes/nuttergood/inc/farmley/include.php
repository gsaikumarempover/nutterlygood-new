<?php
/**
 * Farmley-style promo ticker + header polish for Nutterly Good.
 */

if ( defined( 'NG_FARMLEY_INCLUDES_LOADED' ) ) {
	return;
}

define( 'NG_FARMLEY_INCLUDES_LOADED', true );

$ng_farmley_media_library = get_template_directory() . '/inc/farmley/media-library.php';
if ( file_exists( $ng_farmley_media_library ) ) {
	include_once $ng_farmley_media_library;
}
$ng_farmley_performance = get_template_directory() . '/inc/farmley/performance-optimizer.php';
if ( file_exists( $ng_farmley_performance ) ) {
	include_once $ng_farmley_performance;
}
$ng_farmley_contact_info = get_template_directory() . '/inc/farmley/contact-info.php';
if ( file_exists( $ng_farmley_contact_info ) ) {
	include_once $ng_farmley_contact_info;
}
$ng_farmley_email_config = get_template_directory() . '/inc/farmley/email-config.php';
if ( file_exists( $ng_farmley_email_config ) ) {
	include_once $ng_farmley_email_config;
}
$ng_farmley_mail = get_template_directory() . '/inc/farmley/mail.php';
if ( file_exists( $ng_farmley_mail ) ) {
	include_once $ng_farmley_mail;
}
$ng_farmley_account_menu = get_template_directory() . '/inc/farmley/account-menu.php';
if ( file_exists( $ng_farmley_account_menu ) ) {
	include_once $ng_farmley_account_menu;
}
$ng_farmley_checkout_fields = get_template_directory() . '/inc/farmley/checkout-fields.php';
if ( file_exists( $ng_farmley_checkout_fields ) ) {
	include_once $ng_farmley_checkout_fields;
}
$ng_farmley_meta_file = get_template_directory() . '/inc/farmley/product-meta.php';
if ( file_exists( $ng_farmley_meta_file ) ) {
	include_once $ng_farmley_meta_file;
}
$ng_farmley_admin_cleanup = get_template_directory() . '/inc/farmley/admin-cleanup.php';
if ( file_exists( $ng_farmley_admin_cleanup ) ) {
	include_once $ng_farmley_admin_cleanup;
}
$ng_farmley_quick_view = get_template_directory() . '/inc/farmley/quick-view-drawer.php';
if ( file_exists( $ng_farmley_quick_view ) ) {
	include_once $ng_farmley_quick_view;
}
$ng_farmley_footer = get_template_directory() . '/inc/farmley/footer.php';
if ( file_exists( $ng_farmley_footer ) ) {
	include_once $ng_farmley_footer;
}
$ng_farmley_shop = get_template_directory() . '/inc/farmley/shop.php';
if ( file_exists( $ng_farmley_shop ) ) {
	include_once $ng_farmley_shop;
}
$ng_farmley_empty_products = get_template_directory() . '/inc/farmley/empty-products.php';
if ( file_exists( $ng_farmley_empty_products ) ) {
	include_once $ng_farmley_empty_products;
}
$ng_farmley_shop_categories = get_template_directory() . '/inc/farmley/shop-categories.php';
if ( file_exists( $ng_farmley_shop_categories ) ) {
	include_once $ng_farmley_shop_categories;
}
$ng_farmley_shop_catalog_filters = get_template_directory() . '/inc/farmley/shop-catalog-filters.php';
if ( file_exists( $ng_farmley_shop_catalog_filters ) ) {
	include_once $ng_farmley_shop_catalog_filters;
}
$ng_farmley_shop_popular = get_template_directory() . '/inc/farmley/shop-popular-products.php';
if ( file_exists( $ng_farmley_shop_popular ) ) {
	include_once $ng_farmley_shop_popular;
}
$ng_farmley_header = get_template_directory() . '/inc/farmley/header.php';
if ( file_exists( $ng_farmley_header ) ) {
	include_once $ng_farmley_header;
}
$ng_farmley_ecommerce = get_template_directory() . '/inc/farmley/ecommerce-setup.php';
if ( file_exists( $ng_farmley_ecommerce ) ) {
	include_once $ng_farmley_ecommerce;
}
$ng_farmley_otp_auth = get_template_directory() . '/inc/farmley/otp-auth.php';
if ( file_exists( $ng_farmley_otp_auth ) ) {
	include_once $ng_farmley_otp_auth;
}
$ng_farmley_product_wc_attrs = get_template_directory() . '/inc/farmley/product-wc-attributes.php';
if ( file_exists( $ng_farmley_product_wc_attrs ) ) {
	include_once $ng_farmley_product_wc_attrs;
}
$ng_farmley_product_highlights = get_template_directory() . '/inc/farmley/product-highlights.php';
if ( file_exists( $ng_farmley_product_highlights ) ) {
	include_once $ng_farmley_product_highlights;
}
$ng_farmley_product_description = get_template_directory() . '/inc/farmley/product-description-render.php';
if ( file_exists( $ng_farmley_product_description ) ) {
	include_once $ng_farmley_product_description;
}
if ( is_admin() ) {
	$ng_farmley_product_admin = get_template_directory() . '/inc/farmley/product-admin.php';
	if ( file_exists( $ng_farmley_product_admin ) ) {
		include_once $ng_farmley_product_admin;
	}
}
$ng_farmley_single_product = get_template_directory() . '/inc/farmley/single-product.php';
if ( file_exists( $ng_farmley_single_product ) ) {
	include_once $ng_farmley_single_product;
}
$ng_farmley_single_product_summary = get_template_directory() . '/inc/farmley/single-product-summary.php';
if ( file_exists( $ng_farmley_single_product_summary ) ) {
	include_once $ng_farmley_single_product_summary;
}
$ng_farmley_single_product_related = get_template_directory() . '/inc/farmley/single-product-related.php';
if ( file_exists( $ng_farmley_single_product_related ) ) {
	include_once $ng_farmley_single_product_related;
}
$ng_farmley_product_cards = get_template_directory() . '/inc/farmley/product-cards.php';
if ( file_exists( $ng_farmley_product_cards ) ) {
	include_once $ng_farmley_product_cards;
}
$ng_farmley_home_category_carousel = get_template_directory() . '/inc/farmley/home-category-carousel.php';
if ( file_exists( $ng_farmley_home_category_carousel ) ) {
	include_once $ng_farmley_home_category_carousel;
}
$ng_farmley_home_featured_products = get_template_directory() . '/inc/farmley/home-featured-products.php';
if ( file_exists( $ng_farmley_home_featured_products ) ) {
	include_once $ng_farmley_home_featured_products;
}
$ng_farmley_home_premium_snacks = get_template_directory() . '/inc/farmley/home-premium-snacks.php';
if ( file_exists( $ng_farmley_home_premium_snacks ) ) {
	include_once $ng_farmley_home_premium_snacks;
}
$ng_farmley_home_cart_drawer = get_template_directory() . '/inc/farmley/home-cart-drawer.php';
if ( file_exists( $ng_farmley_home_cart_drawer ) ) {
	include_once $ng_farmley_home_cart_drawer;
}
$ng_farmley_home_blog_newsletter = get_template_directory() . '/inc/farmley/home-blog-newsletter.php';
if ( file_exists( $ng_farmley_home_blog_newsletter ) ) {
	include_once $ng_farmley_home_blog_newsletter;
}
$ng_farmley_home_google_reviews = get_template_directory() . '/inc/farmley/home-google-reviews.php';
if ( file_exists( $ng_farmley_home_google_reviews ) ) {
	include_once $ng_farmley_home_google_reviews;
}
$ng_farmley_home_hero_slider = get_template_directory() . '/inc/farmley/home-hero-slider.php';
if ( file_exists( $ng_farmley_home_hero_slider ) ) {
	include_once $ng_farmley_home_hero_slider;
}
$ng_farmley_blog = get_template_directory() . '/inc/farmley/blog.php';
if ( file_exists( $ng_farmley_blog ) ) {
	include_once $ng_farmley_blog;
}
$ng_farmley_product_content = get_template_directory() . '/inc/farmley/product-content.php';
if ( file_exists( $ng_farmley_product_content ) ) {
	include_once $ng_farmley_product_content;
}
$ng_farmley_page_banner = get_template_directory() . '/inc/farmley/page-banner.php';
if ( file_exists( $ng_farmley_page_banner ) ) {
	include_once $ng_farmley_page_banner;
}
$ng_farmley_contact_page = get_template_directory() . '/inc/farmley/contact-page.php';
if ( file_exists( $ng_farmley_contact_page ) ) {
	include_once $ng_farmley_contact_page;
}
$ng_farmley_about_page = get_template_directory() . '/inc/farmley/about-page.php';
if ( file_exists( $ng_farmley_about_page ) ) {
	include_once $ng_farmley_about_page;
}
$ng_farmley_checkout = get_template_directory() . '/inc/farmley/checkout.php';
if ( file_exists( $ng_farmley_checkout ) ) {
	include_once $ng_farmley_checkout;
}
$ng_farmley_checkout_accounts = get_template_directory() . '/inc/farmley/checkout-accounts.php';
if ( file_exists( $ng_farmley_checkout_accounts ) ) {
	include_once $ng_farmley_checkout_accounts;
}
$ng_farmley_checkout_thankyou = get_template_directory() . '/inc/farmley/checkout-thankyou.php';
if ( file_exists( $ng_farmley_checkout_thankyou ) ) {
	include_once $ng_farmley_checkout_thankyou;
}
$ng_farmley_coupons_bootstrap = get_template_directory() . '/inc/farmley/coupons-bootstrap.php';
if ( file_exists( $ng_farmley_coupons_bootstrap ) ) {
	include_once $ng_farmley_coupons_bootstrap;
}
$ng_farmley_side_cart = get_template_directory() . '/inc/farmley/side-cart-experience.php';
if ( file_exists( $ng_farmley_side_cart ) ) {
	include_once $ng_farmley_side_cart;
}
$ng_farmley_newsletter_coupons = get_template_directory() . '/inc/farmley/newsletter-coupons.php';
if ( file_exists( $ng_farmley_newsletter_coupons ) ) {
	include_once $ng_farmley_newsletter_coupons;
}

if ( ! function_exists( 'nuttergood_farmley_promo_offers' ) ) {
	function nuttergood_farmley_promo_offers() {
		$shop = get_permalink( wc_get_page_id( 'shop' ) );
		return array(
			array(
				'text' => 'Get <strong>8% OFF</strong> on orders above ₹899 — Code <strong>SAVER8</strong>',
				'url'  => $shop,
			),
			array(
				'text' => '<strong>Free delivery</strong> on all orders above ₹2,500',
				'url'  => $shop,
			),
			array(
				'text' => 'Premium handpicked almonds, cashews & trail mixes — <strong>Shop now</strong>',
				'url'  => $shop,
			),
			array(
				'text' => 'New arrivals: crunchy chips & wholesome snack mixes',
				'url'  => $shop,
			),
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_promo_bar' ) ) {
	function nuttergood_farmley_render_promo_bar() {
		if ( is_admin() ) {
			return;
		}
		$offers = nuttergood_farmley_promo_offers();
		if ( empty( $offers ) ) {
			return;
		}
		echo '<div id="ng-farmley-promo" class="ng-farmley-promo" aria-label="Promotional offers">';
		echo '<div class="ng-farmley-promo__track">';
		foreach ( array_merge( $offers, $offers ) as $offer ) {
			printf(
				'<a class="ng-farmley-promo__item" href="%s">%s</a>',
				esc_url( $offer['url'] ),
				wp_kses_post( $offer['text'] )
			);
		}
		echo '</div></div>';
	}
	add_action( 'greenpath_action_before_page_header', 'nuttergood_farmley_render_promo_bar', 4 );
}

if ( ! function_exists( 'nuttergood_farmley_enqueue_assets' ) ) {
	function nuttergood_farmley_enqueue_assets() {
		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-header.css';
		$js  = $dir . '/assets/js/farmley-header.js';
		if ( file_exists( $css ) ) {
			wp_enqueue_style( 'nuttergood-farmley-header', $uri . '/assets/css/farmley-header.css', array( 'greenpath-style' ), filemtime( $css ) );
		}
		if ( file_exists( $js ) ) {
			wp_enqueue_script( 'nuttergood-farmley-header', $uri . '/assets/js/farmley-header.js', array( 'jquery', 'greenpath-main-js' ), filemtime( $js ), true );
			wp_localize_script(
				'nuttergood-farmley-header',
				'ngFarmleyHeader',
				array(
					'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
					'wishlistUrl' => function_exists( 'nuttergood_farmley_get_wishlist_url' ) ? nuttergood_farmley_get_wishlist_url() : '',
				)
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_enqueue_assets', 30 );
}

if ( ! function_exists( 'nuttergood_farmley_wishlist_assets' ) ) {
	function nuttergood_farmley_wishlist_assets() {
		if ( is_admin() ) {
			return;
		}

		$should_load = is_front_page();
		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$should_load = true;
		}

		if ( ! $should_load ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-wishlist.css';
		$js  = $dir . '/assets/js/farmley-wishlist.js';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-wishlist',
				$uri . '/assets/css/farmley-wishlist.css',
				array( 'nuttergood-qode-product-list', 'greenpath-style' ),
				filemtime( $css )
			);
		}

		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'nuttergood-farmley-wishlist',
				$uri . '/assets/js/farmley-wishlist.js',
				array( 'jquery' ),
				filemtime( $js ),
				true
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_wishlist_assets', 37 );
}

if ( ! function_exists( 'nuttergood_farmley_home_filter_assets' ) ) {
	function nuttergood_farmley_home_filter_assets() {
		if ( ! is_front_page() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$js  = $dir . '/assets/js/farmley-home-filter.js';

		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'nuttergood-farmley-home-filter',
				$uri . '/assets/js/farmley-home-filter.js',
				array( 'jquery', 'greenpath-main-js' ),
				filemtime( $js ),
				true
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_home_filter_assets', 38 );
}

if ( ! function_exists( 'nuttergood_farmley_home_assets' ) ) {
	function nuttergood_farmley_home_assets() {
		if ( ! is_front_page() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-home.css';
		$js  = $dir . '/assets/js/farmley-home.js';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-home',
				$uri . '/assets/css/farmley-home.css',
				array( 'nuttergood-farmley-product-cards', 'nuttergood-farmley-header', 'greenpath-core-style' ),
				filemtime( $css )
			);
		}

		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'nuttergood-farmley-home',
				$uri . '/assets/js/farmley-home.js',
				array(),
				filemtime( $js ),
				true
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_home_assets', 39 );
}

if ( ! function_exists( 'nuttergood_farmley_blog_assets' ) ) {
	function nuttergood_farmley_blog_assets() {
		if ( ! function_exists( 'nuttergood_farmley_is_blog_context' ) || ! nuttergood_farmley_is_blog_context() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-blog.css';
		$js  = $dir . '/assets/js/farmley-blog.js';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-blog',
				$uri . '/assets/css/farmley-blog.css',
				array( 'greenpath-style', 'nuttergood-farmley-header' ),
				filemtime( $css )
			);
		}

		if ( file_exists( $js ) && ! is_singular( 'post' ) ) {
			wp_enqueue_script(
				'nuttergood-farmley-blog',
				$uri . '/assets/js/farmley-blog.js',
				array( 'jquery' ),
				filemtime( $js ),
				true
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_blog_assets', 40 );
}

if ( ! function_exists( 'nuttergood_farmley_wishlist_count_ajax' ) ) {
	function nuttergood_farmley_wishlist_count_ajax() {
		$items = function_exists( 'qode_wishlist_for_woocommerce_get_wishlist_items_by_table' )
			? qode_wishlist_for_woocommerce_get_wishlist_items_by_table( 'default' )
			: array();

		wp_send_json_success(
			array(
				'count' => is_array( $items ) ? count( $items ) : 0,
			)
		);
	}
	add_action( 'wp_ajax_ng_farmley_wishlist_count', 'nuttergood_farmley_wishlist_count_ajax' );
	add_action( 'wp_ajax_nopriv_ng_farmley_wishlist_count', 'nuttergood_farmley_wishlist_count_ajax' );
}

if ( ! function_exists( 'nuttergood_farmley_inline_css' ) ) {
	function nuttergood_farmley_inline_css( $style ) {
		$style .= '
#qodef-top-area { display: none !important; }
body.qodef-top-area--enabled { padding-top: 0 !important; }
.ng-farmley-promo + #qodef-page-header { margin-top: 0; }
#qodef-top-area {
	background-color: #FCF4EB !important;
	border-bottom: 1px solid #E8E0D6 !important;
}
#qodef-top-area,
#qodef-top-area a,
#qodef-top-area .widget {
	color: #0C533D !important;
}
#qodef-top-area .qodef-text--main-color {
	color: #B99531 !important;
}
.qodef-header--standard-extended #qodef-page-header .qodef-header-section.qodef--top {
	background: #FFFFFF !important;
	border-bottom: 1px solid #E8E8E8 !important;
}
.qodef-header--standard-extended #qodef-page-header-inner .qodef-header-section.qodef--bottom {
	display: none !important;
}
.qodef-header--standard-extended #qodef-page-header {
	height: auto !important;
}

/* Hero slider: width only — let RS responsive heights (el/gh) control size */
.ng-farmley-hero rs-module-wrap,
.ng-farmley-hero .rev_slider_wrapper,
.ng-farmley-hero rs-module {
	max-width: 100% !important;
	width: 100% !important;
}
';
		return $style;
	}
	add_filter( 'greenpath_filter_add_inline_style', 'nuttergood_farmley_inline_css' );
}

if ( ! function_exists( 'nuttergood_farmley_page_loader_assets' ) ) {
	function nuttergood_farmley_page_loader_assets() {
		if ( is_admin() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/nuttergood-page-loader.css';
		$js  = $dir . '/assets/js/nuttergood-page-loader.js';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-page-loader',
				$uri . '/assets/css/nuttergood-page-loader.css',
				array(),
				filemtime( $css )
			);
		}

		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'nuttergood-page-loader',
				$uri . '/assets/js/nuttergood-page-loader.js',
				array(),
				filemtime( $js ),
				true
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_page_loader_assets', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_render_page_loader' ) ) {
	function nuttergood_farmley_render_page_loader() {
		if ( is_admin() ) {
			return;
		}

		$frames_uri = get_template_directory_uri() . '/assets/images/nuttergood-loader-frames/';
		?>
		<div
			class="ng-page-loader"
			role="status"
			aria-live="polite"
			aria-label="<?php esc_attr_e( 'Loading Nutterly Good', 'nuttergood' ); ?>"
			data-frames-base="<?php echo esc_url( $frames_uri ); ?>"
			data-frame-count="80"
			data-fps="10"
			data-canvas-width="1026"
			data-canvas-height="636"
		>
			<div class="ng-page-loader__inner">
				<div class="ng-page-loader__canvas-wrap">
					<canvas class="ng-page-loader__canvas" width="1026" height="636" aria-hidden="true"></canvas>
				</div>
				<p class="ng-page-loader__text"><?php esc_html_e( 'Loading fresh goodness', 'nuttergood' ); ?></p>
				<div class="ng-page-loader__bar" aria-hidden="true"><span></span></div>
			</div>
		</div>
		<?php
	}
	add_action( 'wp_body_open', 'nuttergood_farmley_render_page_loader', 1 );
}