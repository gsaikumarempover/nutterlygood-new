<?php
/**
 * Compact modern footer — dedicated light logo for dark green footer.
 */

if ( ! function_exists( 'nuttergood_farmley_get_footer_logo_id' ) ) {
	function nuttergood_farmley_get_footer_logo_id() {
		$logo_id = (int) get_option( 'ng_farmley_footer_logo_id' );
		if ( $logo_id && wp_attachment_is_image( $logo_id ) ) {
			return $logo_id;
		}

		$by_slug = attachment_url_to_postid( nuttergood_farmley_media_url( 'logos', 'Group-25204.png' ) );
		if ( $by_slug ) {
			return (int) $by_slug;
		}

		$matches = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'meta_query'     => array(
					array(
						'key'   => '_ng_farmley_footer_logo_source',
						'value' => 'Group 25204.png',
					),
				),
			)
		);
		if ( ! empty( $matches ) ) {
			return (int) $matches[0]->ID;
		}

		$logo_id = (int) get_theme_mod( 'custom_logo' );
		if ( ! $logo_id && function_exists( 'greenpath_core_get_post_value_through_levels' ) ) {
			$logo_id = (int) greenpath_core_get_post_value_through_levels( 'qodef_logo_main' );
		}

		return $logo_id;
	}
}

if ( ! function_exists( 'nuttergood_farmley_footer_menu_id_by_name' ) ) {
	function nuttergood_farmley_footer_menu_id_by_name( $name, $fallback = 0 ) {
		$menu = get_term_by( 'name', $name, 'nav_menu' );
		if ( $menu && ! is_wp_error( $menu ) ) {
			return (int) $menu->term_id;
		}
		return (int) $fallback;
	}
}

if ( ! function_exists( 'nuttergood_farmley_ensure_policy_pages' ) ) {
	function nuttergood_farmley_ensure_policy_pages() {
		if ( get_option( 'ng_farmley_policy_pages_ready' ) ) {
			return;
		}

		$pages = array(
			'privacy-policy'       => array(
				'title'   => 'Privacy Policy',
				'content' => '<p>We respect your privacy. This policy explains how Nutterly Good collects, uses, and protects your personal information when you shop with us.</p>',
			),
			'refund-policy'        => array(
				'title'   => 'Refund Policy',
				'content' => '<p>If you are not satisfied with your order, contact us within 7 days of delivery. Eligible products may be returned or refunded as per our quality standards.</p>',
			),
			'terms-and-conditions' => array(
				'title'   => 'Terms & Conditions',
				'content' => '<p>By using the Nutterly Good website and placing orders, you agree to our terms regarding product information, pricing, shipping, and acceptable use of our services.</p>',
			),
		);

		foreach ( $pages as $slug => $data ) {
			$existing = get_page_by_path( $slug );
			if ( $existing ) {
				continue;
			}

			wp_insert_post(
				array(
					'post_title'   => $data['title'],
					'post_name'    => $slug,
					'post_content' => $data['content'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
				)
			);
		}

		$privacy = get_page_by_path( 'privacy-policy' );
		if ( $privacy && ! get_option( 'wp_page_for_privacy_policy' ) ) {
			update_option( 'wp_page_for_privacy_policy', $privacy->ID );
		}

		update_option( 'ng_farmley_policy_pages_ready', 1, false );
	}
	add_action( 'init', 'nuttergood_farmley_ensure_policy_pages', 12 );
}

if ( ! function_exists( 'nuttergood_farmley_get_policy_links' ) ) {
	function nuttergood_farmley_get_policy_links() {
		$links = array();

		$map = array(
			'privacy-policy'       => __( 'Privacy Policy', 'nuttergood' ),
			'refund-policy'        => __( 'Refund Policy', 'nuttergood' ),
			'terms-and-conditions' => __( 'Terms & Conditions', 'nuttergood' ),
		);

		foreach ( $map as $slug => $label ) {
			$page = get_page_by_path( $slug );
			if ( $page ) {
				$links[] = array(
					'label' => $label,
					'url'   => get_permalink( $page ),
				);
			}
		}

		return $links;
	}
}

if ( ! function_exists( 'nuttergood_farmley_remove_content_bottom' ) ) {
	function nuttergood_farmley_remove_content_bottom() {
		remove_action( 'greenpath_action_page_footer_template', 'greenpath_core_load_page_content_bottom', 5 );
	}
	add_action( 'after_setup_theme', 'nuttergood_farmley_remove_content_bottom', 20 );
}

if ( ! function_exists( 'nuttergood_farmley_disable_widget_footer' ) ) {
	function nuttergood_farmley_disable_widget_footer() {
		return false;
	}
	add_filter( 'greenpath_filter_enable_footer_top_area', 'nuttergood_farmley_disable_widget_footer' );
	add_filter( 'greenpath_filter_enable_footer_bottom_area', 'nuttergood_farmley_disable_widget_footer' );
}

if ( ! function_exists( 'nuttergood_farmley_enable_page_footer' ) ) {
	function nuttergood_farmley_enable_page_footer( $_enabled ) {
		return true;
	}
	add_filter( 'greenpath_filter_enable_page_footer', 'nuttergood_farmley_enable_page_footer' );
}

if ( ! function_exists( 'nuttergood_farmley_footer_holder_class' ) ) {
	function nuttergood_farmley_footer_holder_class( $classes ) {
		$classes[] = 'ng-farmley-footer';
		return $classes;
	}
	add_filter( 'greenpath_filter_footer_holder_classes', 'nuttergood_farmley_footer_holder_class' );
}

if ( ! function_exists( 'nuttergood_farmley_footer_content_template' ) ) {
	function nuttergood_farmley_footer_content_template( $template ) {
		ob_start();
		nuttergood_farmley_render_footer();
		return ob_get_clean();
	}
	add_filter( 'greenpath_filter_footer_content_template', 'nuttergood_farmley_footer_content_template' );
}

if ( ! function_exists( 'nuttergood_farmley_enqueue_footer_assets' ) ) {
	function nuttergood_farmley_enqueue_footer_assets() {
		$dir = get_template_directory();
		$uri = get_template_directory_uri();
		$css = $dir . '/assets/css/farmley-footer.css';

		if ( file_exists( $css ) ) {
			wp_enqueue_style(
				'nuttergood-farmley-footer',
				$uri . '/assets/css/farmley-footer.css',
				array( 'nuttergood-farmley-header' ),
				filemtime( $css )
			);
		}
	}
	add_action( 'wp_enqueue_scripts', 'nuttergood_farmley_enqueue_footer_assets', 35 );
}

if ( ! function_exists( 'nuttergood_farmley_render_footer_nav' ) ) {
	function nuttergood_farmley_render_footer_nav( $menu_id, $title ) {
		if ( ! $menu_id ) {
			return;
		}
		?>
		<div class="ng-farmley-footer__col">
			<h6 class="ng-farmley-footer__title"><?php echo esc_html( $title ); ?></h6>
			<?php
			wp_nav_menu(
				array(
					'menu'        => $menu_id,
					'container'   => false,
					'menu_class'  => 'ng-farmley-footer__menu',
					'depth'       => 1,
					'fallback_cb' => false,
				)
			);
			?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_footer_social_icon' ) ) {
	function nuttergood_farmley_footer_social_icon( $network ) {
		$icons = array(
			'facebook' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 10.176 19" aria-hidden="true"><path fill="currentColor" d="M9.51 10.063 10.038 6.624H6.738V4.393a1.719 1.719 0 0 1 1.939-1.858h1.5V0h-2.663a4.293 4.293 0 0 0-4.514 4.232v2.621H.849v3.439H3.87V19H6.738V10.063Z"/></svg>',
			'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 17 17" aria-hidden="true"><path fill="currentColor" d="M8.316 5.607a4.257 4.257 0 1 0 0 8.514 4.257 4.257 0 0 0 0-8.514Zm0 7.036a2.777 2.777 0 1 1 0-5.554 2.777 2.777 0 0 1 0 5.554Zm5.433-7.21a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm2.824 1.009a4.922 4.922 0 0 0-1.343-3.485 4.954 4.954 0 0 0-3.485-1.343c-1.373-.078-5.488-.078-6.862 0a4.947 4.947 0 0 0-3.513 1.515 4.938 4.938 0 0 0-1.344 3.485c-.078 1.373-.078 5.488 0 6.862a4.922 4.922 0 0 0 1.344 3.485 4.96 4.96 0 0 0 3.484 1.343c1.373.078 5.488.078 6.862 0a4.922 4.922 0 0 0 3.485-1.343 4.954 4.954 0 0 0 1.343-3.485c.078-1.373.078-5.485 0-6.858ZM14.8 14.773a2.806 2.806 0 0 1-1.582 1.581c-1.095.434-3.692.334-4.9.334s-3.811.1-4.9-.334A2.806 2.806 0 0 1 1.833 14.773C1.4 13.679 1.5 11.081 1.5 9.871s-.1-3.811.334-4.9A2.806 2.806 0 0 1 3.416 3.39c1.095-.434 3.692-.334 4.9-.334s3.811-.1 4.9.334A2.806 2.806 0 0 1 14.8 4.902c.434 1.095.334 3.692.334 4.9s.1 3.811-.334 4.971Z"/></svg>',
			'whatsapp' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
		);

		return isset( $icons[ $network ] ) ? $icons[ $network ] : '';
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_footer_bar_policies' ) ) {
	function nuttergood_farmley_render_footer_bar_policies() {
		$links = nuttergood_farmley_get_policy_links();
		if ( empty( $links ) ) {
			return;
		}
		?>
		<nav class="ng-farmley-footer__legal" aria-label="<?php esc_attr_e( 'Legal links', 'nuttergood' ); ?>">
			<?php foreach ( $links as $link ) : ?>
				<a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a>
			<?php endforeach; ?>
		</nav>
		<?php
	}
}

if ( ! function_exists( 'nuttergood_farmley_render_footer' ) ) {
	function nuttergood_farmley_render_footer() {
		$logo_id    = nuttergood_farmley_get_footer_logo_id();
		$home       = home_url( '/' );
		$contact    = function_exists( 'nuttergood_farmley_contact_info' ) ? nuttergood_farmley_contact_info() : array();
		$email      = $contact['email'] ?? 'support@nutterlygood.com';
		$phone      = $contact['phone'] ?? '+91 74162 85566';
		$phone_tel  = $contact['phone_tel'] ?? '+917416285566';
		$address    = $contact['address'] ?? '';
		$menu_quick = nuttergood_farmley_footer_menu_id_by_name( 'Footer Menu 1', 73 );
		$menu_cats  = nuttergood_farmley_footer_menu_id_by_name( 'Footer Menu 2', 74 );
		$menu_shop  = nuttergood_farmley_footer_menu_id_by_name( 'Footer Menu 3', 75 );
		?>
		<div class="ng-farmley-footer__inner">
			<div class="ng-farmley-footer__main">
				<div class="ng-farmley-footer__brand">
					<?php if ( $logo_id ) : ?>
						<a class="ng-farmley-footer__logo" href="<?php echo esc_url( $home ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
							<?php
							echo wp_get_attachment_image(
								$logo_id,
								'medium',
								false,
								array(
									'class'    => 'ng-farmley-footer__logo-img ng-farmley-footer__logo-img--light',
									'alt'      => get_bloginfo( 'name' ),
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							);
							?>
						</a>
					<?php endif; ?>
					<p class="ng-farmley-footer__tagline"><?php esc_html_e( 'Premium handpicked nuts, dry fruits & wholesome snacks.', 'nuttergood' ); ?></p>
					<div class="ng-farmley-footer__contact">
						<?php if ( $address ) : ?>
							<p class="ng-farmley-footer__address"><?php echo esc_html( $address ); ?></p>
						<?php endif; ?>
						<div class="ng-farmley-footer__contact-links">
							<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
							<span class="ng-farmley-footer__sep" aria-hidden="true">·</span>
							<a href="tel:<?php echo esc_attr( $phone_tel ); ?>"><?php echo esc_html( $phone ); ?></a>
						</div>
					</div>
				</div>
				<?php
				nuttergood_farmley_render_footer_nav( $menu_quick, __( 'Quick Links', 'nuttergood' ) );
				nuttergood_farmley_render_footer_nav( $menu_cats, __( 'Shop', 'nuttergood' ) );
				nuttergood_farmley_render_footer_nav( $menu_shop, __( 'Customer Care', 'nuttergood' ) );
				?>
			</div>
			<div class="ng-farmley-footer__bar">
				<div class="ng-farmley-footer__bar-left">
					<div class="ng-farmley-footer__social" aria-label="<?php esc_attr_e( 'Social links', 'nuttergood' ); ?>">
						<a class="ng-farmley-footer__social-link" href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
							<?php echo nuttergood_farmley_footer_social_icon( 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
						<a class="ng-farmley-footer__social-link" href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
							<?php echo nuttergood_farmley_footer_social_icon( 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</div>
					<p class="ng-farmley-footer__copy">
						&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
						<a href="<?php echo esc_url( $home ); ?>"><?php bloginfo( 'name' ); ?></a>.
						<?php esc_html_e( 'All rights reserved.', 'nuttergood' ); ?>
					</p>
				</div>
				<?php nuttergood_farmley_render_footer_bar_policies(); ?>
			</div>
		</div>
		<?php
	}
}