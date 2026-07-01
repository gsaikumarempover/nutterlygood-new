<?php
/**
 * Front-end performance — lean homepage scripts, defer non-critical JS, drop bloat.
 */

if ( ! function_exists( 'nuttergood_farmley_is_performance_context' ) ) {
	function nuttergood_farmley_is_performance_context() {
		return ! is_admin() && ! wp_doing_ajax() && ! wp_is_json_request();
	}
}

if ( ! function_exists( 'nuttergood_farmley_dequeue_bloat' ) ) {
	function nuttergood_farmley_dequeue_bloat() {
		if ( ! nuttergood_farmley_is_performance_context() ) {
			return;
		}

		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'global-styles' );

		if ( ! is_user_logged_in() ) {
			wp_dequeue_style( 'dashicons' );
		}

		wp_deregister_script( 'wp-embed' );

		// Compare table — shop/product only.
		if ( ! is_shop() && ! is_product_taxonomy() && ! is_product() ) {
			wp_dequeue_script( 'qode-compare-for-woocommerce-main' );
			wp_dequeue_style( 'qode-compare-for-woocommerce-main' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_dequeue_bloat', 999 );
}

if ( ! function_exists( 'nuttergood_farmley_homepage_dequeue' ) ) {
	function nuttergood_farmley_homepage_dequeue() {
		if ( ! is_front_page() || ! nuttergood_farmley_is_performance_context() ) {
			return;
		}

		wp_dequeue_script( 'jquery-magnific-popup' );
		wp_dequeue_style( 'magnific-popup' );

		wp_dequeue_script( 'isotope' );
		wp_dequeue_script( 'packery' );
		wp_dequeue_script( 'jquery-justified-gallery' );

		// Shiprocket admin scripts not needed on homepage storefront.
		wp_dequeue_script( 'shiprocket-frontend' );
		wp_dequeue_style( 'shiprocket-frontend' );

		if ( ! is_user_logged_in() ) {
			wp_dequeue_script( 'greenpath-membership-login-modal' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_homepage_dequeue', 1000 );
}

if ( ! function_exists( 'nuttergood_farmley_defer_scripts' ) ) {
	/**
	 * @param string $tag    Script tag.
	 * @param string $handle Handle.
	 * @param string $src    Source URL.
	 */
	function nuttergood_farmley_defer_scripts( $tag, $handle, $src ) {
		if ( ! nuttergood_farmley_is_performance_context() ) {
			return $tag;
		}

		$defer = array(
			'greenpath-core-script',
			'nuttergood-farmley-side-cart',
			'nuttergood-farmley-cart-drawer',
			'nuttergood-farmley-home-newsletter',
			'nuttergood-farmley-home-google-reviews',
			'nuttergood-farmley-wishlist',
			'qode-wishlist-for-woocommerce-main',
			'qode-quick-view-for-woocommerce-main',
			'qi-addons-for-elementor',
			'elementor-frontend',
			'elementor-webpack-runtime',
		);

		$async = array(
			'gsap',
		);

		if ( in_array( $handle, $defer, true ) && false === strpos( $tag, 'defer' ) ) {
			return str_replace( ' src', ' defer src', $tag );
		}

		if ( in_array( $handle, $async, true ) && false === strpos( $tag, 'async' ) ) {
			return str_replace( ' src', ' async src', $tag );
		}

		return $tag;
	}
	add_filter( 'script_loader_tag', 'nuttergood_farmley_defer_scripts', 20, 3 );
}

if ( ! function_exists( 'nuttergood_farmley_preconnect_hints' ) ) {
	function nuttergood_farmley_preconnect_hints() {
		if ( ! nuttergood_farmley_is_performance_context() ) {
			return;
		}

		echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />' . "\n";
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />' . "\n";
	}
	add_action( 'wp_head', 'nuttergood_farmley_preconnect_hints', 1 );
}

if ( ! function_exists( 'nuttergood_farmley_preload_lcp' ) ) {
	function nuttergood_farmley_preload_lcp() {
		if ( ! is_front_page() ) {
			return;
		}

		$candidates = array(
			'ng-media/slider/',
			'ng-media/banners/',
			'ng-media/misc/ng-hero-frame12.webp',
		);

		$uploads = WP_CONTENT_DIR . '/uploads/';
		foreach ( $candidates as $rel ) {
			$dir = $uploads . $rel;
			if ( is_file( $dir ) ) {
				echo '<link rel="preload" as="image" type="image/webp" href="' . esc_url( content_url( 'uploads/' . $rel ) ) . '" fetchpriority="high" />' . "\n";
				return;
			}
			if ( is_dir( $dir ) ) {
				$files = glob( $dir . '*.{webp,jpg,jpeg,png}', GLOB_BRACE );
				if ( ! empty( $files[0] ) ) {
					$url = content_url( 'uploads/' . $rel . basename( $files[0] ) );
					$type = str_ends_with( $files[0], '.webp' ) ? 'image/webp' : 'image/jpeg';
					echo '<link rel="preload" as="image" type="' . esc_attr( $type ) . '" href="' . esc_url( $url ) . '" fetchpriority="high" />' . "\n";
					return;
				}
			}
		}
	}
	add_action( 'wp_head', 'nuttergood_farmley_preload_lcp', 2 );
}

if ( ! function_exists( 'nuttergood_farmley_disable_emojis' ) ) {
	function nuttergood_farmley_disable_emojis() {
		if ( ! nuttergood_farmley_is_performance_context() ) {
			return;
		}

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
	}
	add_action( 'init', 'nuttergood_farmley_disable_emojis' );
}

if ( ! function_exists( 'nuttergood_farmley_home_script_strategy' ) ) {
	function nuttergood_farmley_home_script_strategy( $tag, $handle, $src ) {
		if ( ! is_front_page() ) {
			return $tag;
		}

		$defer_home = array(
			'nuttergood-farmley-home-filter',
			'nuttergood-farmley-product-cards',
			'nuttergood-farmley-quick-view',
		);

		if ( in_array( $handle, $defer_home, true ) && false === strpos( $tag, 'defer' ) ) {
			return str_replace( ' src', ' defer src', $tag );
		}

		return $tag;
	}
	add_filter( 'script_loader_tag', 'nuttergood_farmley_home_script_strategy', 25, 3 );
}