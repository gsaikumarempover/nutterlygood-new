<?php
/**
 * Page title banners — image header on all inner pages.
 */

if ( ! function_exists( 'nuttergood_farmley_custom_page_inner_classes' ) ) {
	function nuttergood_farmley_custom_page_inner_classes( $classes ) {
		$is_contact = function_exists( 'nuttergood_farmley_is_contact_page' ) && nuttergood_farmley_is_contact_page();
		$is_about   = function_exists( 'nuttergood_farmley_is_about_page' ) && nuttergood_farmley_is_about_page();

		if ( $is_contact || $is_about ) {
			return 'qodef-content-full-width';
		}

		return $classes;
	}
	add_filter( 'greenpath_filter_page_inner_classes', 'nuttergood_farmley_custom_page_inner_classes' );
}

if ( ! function_exists( 'nuttergood_farmley_should_show_page_banner' ) ) {
	function nuttergood_farmley_should_show_page_banner() {
		if ( is_front_page() ) {
			return false;
		}

		if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
			return false;
		}

		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			return false;
		}

		if ( is_page( 'signup' ) ) {
			return false;
		}

		if ( function_exists( 'is_product' ) && is_product() ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'nuttergood_farmley_enable_page_banner' ) ) {
	function nuttergood_farmley_enable_page_banner( $enabled ) {
		if ( function_exists( 'nuttergood_farmley_is_contact_page' ) && nuttergood_farmley_is_contact_page() ) {
			return false;
		}

		if ( ! nuttergood_farmley_should_show_page_banner() ) {
			return false;
		}

		return true;
	}
	add_filter( 'greenpath_filter_enable_page_title', 'nuttergood_farmley_enable_page_banner', 99 );
}

if ( ! function_exists( 'nuttergood_farmley_get_page_banner_map' ) ) {
	/**
	 * Slug => relative path under wp-content/uploads.
	 *
	 * @return array<string, string>
	 */
	function nuttergood_farmley_get_page_banner_map() {
		$base = 'ng-media/banners';
		return array(
			'shop'                 => $base . '/banner-shop.jpg',
			'about-us'             => $base . '/banner-about.jpg',
			'contact'              => $base . '/banner-contact.jpg',
			'privacy-policy'       => $base . '/banner-policy.jpg',
			'terms-and-conditions' => $base . '/banner-policy.jpg',
			'refund-policy'        => $base . '/banner-policy.jpg',
			'wishlist'             => $base . '/banner-shop.jpg',
			'default'              => $base . '/banner-default.jpg',
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_resolve_banner_attachment_id' ) ) {
	/**
	 * @param string $relative Relative uploads path.
	 */
	function nuttergood_farmley_resolve_banner_attachment_id( $relative ) {
		$filename = basename( $relative );
		$existing = get_posts(
			array(
				'post_type'      => 'attachment',
				'posts_per_page' => 1,
				'post_status'    => 'inherit',
				'meta_key'       => '_ng_banner_source',
				'meta_value'     => $relative,
			)
		);

		if ( ! empty( $existing[0] ) ) {
			return (int) $existing[0]->ID;
		}

		$abs = WP_CONTENT_DIR . '/uploads/' . ltrim( $relative, '/' );
		if ( ! file_exists( $abs ) ) {
			return 0;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$upload = wp_upload_bits( $filename, null, file_get_contents( $abs ) );
		if ( ! empty( $upload['error'] ) ) {
			return 0;
		}

		$attachment = array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);
		$attach_id = wp_insert_attachment( $attachment, $upload['file'] );
		if ( is_wp_error( $attach_id ) ) {
			return 0;
		}

		$meta = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
		wp_update_attachment_metadata( $attach_id, $meta );
		update_post_meta( $attach_id, '_ng_banner_source', $relative );

		return (int) $attach_id;
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_current_banner_attachment_id' ) ) {
	function nuttergood_farmley_get_current_banner_attachment_id() {
		$map  = nuttergood_farmley_get_page_banner_map();
		$slug = 'default';

		if ( function_exists( 'is_shop' ) && is_shop() ) {
			$slug = 'shop';
		} elseif ( is_page() ) {
			$page_slug = get_post_field( 'post_name', get_queried_object_id() );
			if ( $page_slug && isset( $map[ $page_slug ] ) ) {
				$slug = $page_slug;
			}
		} elseif ( function_exists( 'is_product_category' ) && is_product_category() ) {
			$slug = 'shop';
		} elseif ( function_exists( 'is_product_tag' ) && is_product_tag() ) {
			$slug = 'shop';
		}

		$path = $map[ $slug ] ?? $map['default'];
		return nuttergood_farmley_resolve_banner_attachment_id( $path );
	}
}

if ( ! function_exists( 'nuttergood_farmley_assign_page_banner_meta' ) ) {
	function nuttergood_farmley_assign_page_banner_meta() {
		if ( ! nuttergood_farmley_should_show_page_banner() ) {
			return;
		}

		$object_id = get_queried_object_id();
		if ( $object_id <= 0 ) {
			return;
		}

		$attach_id = nuttergood_farmley_get_current_banner_attachment_id();
		if ( $attach_id <= 0 ) {
			return;
		}

		update_post_meta( $object_id, 'qodef_page_title_background_image', $attach_id );
		update_post_meta( $object_id, 'qodef_page_title_background_image_behavior', '' );
		update_post_meta( $object_id, 'qodef_page_title_height', '220' );
	}
	add_action( 'wp', 'nuttergood_farmley_assign_page_banner_meta', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_page_banner_inline_css' ) ) {
	function nuttergood_farmley_page_banner_inline_css( $style ) {
		if ( ! nuttergood_farmley_should_show_page_banner() ) {
			return $style;
		}

		$attach_id = nuttergood_farmley_get_current_banner_attachment_id();
		$bg_url    = $attach_id ? wp_get_attachment_image_url( $attach_id, 'full' ) : '';

		$style .= '
.qodef-page-title.qodef-m {
	min-height: 220px !important;
	background-color: #0C533D !important;
	background-size: cover !important;
	background-position: center center !important;
	display: flex !important;
	align-items: center !important;
}
.qodef-page-title .qodef-m-inner {
	width: 100%;
}
.qodef-page-title .qodef-m-title,
.qodef-page-title .qodef-m-title .qodef-m-title-text {
	color: #FFFFFF !important;
	font-size: clamp(28px, 4vw, 40px) !important;
	font-weight: 700 !important;
	letter-spacing: -0.02em;
	text-shadow: 0 2px 16px rgba(0,0,0,0.25);
}
#qodef-page-content {
	padding-top: 48px;
}
body.page-template-page-contact #qodef-page-content {
	padding-top: 0 !important;
}
';

		if ( $bg_url ) {
			$style .= '.qodef-page-title.qodef-m { background-image: linear-gradient(90deg, rgba(12,83,61,.86), rgba(12,83,61,.52)), url(' . esc_url( $bg_url ) . ') !important; }';
		}

		return $style;
	}
	add_filter( 'greenpath_filter_add_inline_style', 'nuttergood_farmley_page_banner_inline_css', 25 );
}

if ( ! function_exists( 'nuttergood_farmley_page_banner_assets' ) ) {
	function nuttergood_farmley_page_banner_assets() {
		if ( ! nuttergood_farmley_should_show_page_banner() ) {
			return;
		}

		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-page-banner.css';
		if ( file_exists( $css ) ) {
			wp_enqueue_style( 'nuttergood-farmley-page-banner', $uri . '/assets/css/farmley-page-banner.css', array( 'greenpath-style' ), filemtime( $css ) );
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_page_banner_assets', 34 );
}