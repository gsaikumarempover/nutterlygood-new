<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$admin = get_user_by( 'login', 'nutterlygood' );
if ( $admin ) {
	wp_set_current_user( $admin->ID );
}

function ng_log( $message ) {
	echo $message . PHP_EOL;
}

function ng_get_or_create_category( $name, $slug, $parent_id = 0 ) {
	$existing = get_term_by( 'slug', $slug, 'product_cat' );
	if ( $existing && ! is_wp_error( $existing ) ) {
		wp_update_term(
			$existing->term_id,
			'product_cat',
			array(
				'name'   => $name,
				'parent' => $parent_id,
			)
		);
		return (int) $existing->term_id;
	}

	$result = wp_insert_term(
		$name,
		'product_cat',
		array(
			'slug'   => $slug,
			'parent' => $parent_id,
		)
	);
	if ( is_wp_error( $result ) ) {
		ng_log( 'Category error for ' . $slug . ': ' . $result->get_error_message() );
		return 0;
	}
	return (int) $result['term_id'];
}

function ng_clear_menu( $menu_id ) {
	$items = wp_get_nav_menu_items( $menu_id );
	if ( $items ) {
		foreach ( $items as $item ) {
			wp_delete_post( $item->ID, true );
		}
	}
}

function ng_add_menu_items( $menu_id, $items, $parent_id = 0 ) {
	$order = 1;
	foreach ( $items as $item ) {
		$menu_item_id = wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => $item['title'],
				'menu-item-url'       => $item['url'],
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'custom',
				'menu-item-parent-id' => $parent_id,
				'menu-item-position'  => $order,
			)
		);
		++$order;
		if ( ! empty( $item['children'] ) && $menu_item_id && ! is_wp_error( $menu_item_id ) ) {
			ng_add_menu_items( $menu_id, $item['children'], (int) $menu_item_id );
		}
	}
}

function ng_assign_product_categories( $product_id, $term_ids ) {
	$term_ids = array_values( array_filter( array_map( 'intval', $term_ids ) ) );
	if ( empty( $term_ids ) ) {
		return;
	}
	wp_set_object_terms( $product_id, $term_ids, 'product_cat', false );
}

function ng_fix_elementor_widget_types( &$elements ) {
	foreach ( $elements as &$element ) {
		if ( ! empty( $element['widgetType'] ) ) {
			$element['widgetType'] = str_replace( 'nutterlygood_core_', 'greenpath_core_', $element['widgetType'] );
		}
		if ( ! empty( $element['elements'] ) ) {
			ng_fix_elementor_widget_types( $element['elements'] );
		}
	}
}

function ng_update_elementor_widget_settings( &$elements, $callback ) {
	foreach ( $elements as &$element ) {
		if ( isset( $element['settings'] ) && is_array( $element['settings'] ) ) {
			$element['settings'] = $callback( $element['settings'], $element );
		}
		if ( ! empty( $element['elements'] ) ) {
			ng_update_elementor_widget_settings( $element['elements'], $callback );
		}
	}
}

// --- WooCommerce currency ---
update_option( 'woocommerce_currency', 'INR' );
update_option( 'woocommerce_currency_pos', 'left' );
update_option( 'woocommerce_price_thousand_sep', ',' );
update_option( 'woocommerce_price_decimal_sep', '.' );
update_option( 'woocommerce_price_num_decimals', '2' );
ng_log( 'WooCommerce currency set to INR.' );

// Fix Elementor widget type names broken by earlier branding replace.
global $wpdb;
$wpdb->query(
	$wpdb->prepare(
		"UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key = '_elementor_data' AND meta_value LIKE %s",
		'nutterlygood_core_',
		'greenpath_core_',
		'%' . $wpdb->esc_like( 'nutterlygood_core_' ) . '%'
	)
);
ng_log( 'Fixed Elementor widget registrations site-wide.' );

// --- Product categories ---
$dry_fruits = ng_get_or_create_category( 'Dry Fruits', 'dry-fruits' );
$almonds    = ng_get_or_create_category( 'Almonds', 'almonds', $dry_fruits );
$cashews    = ng_get_or_create_category( 'Cashews', 'cashews', $dry_fruits );
$khishmish  = ng_get_or_create_category( 'Khishmish', 'khishmish', $dry_fruits );
$cranberry  = ng_get_or_create_category( 'Cranberry', 'cranberry', $dry_fruits );
$walnuts    = ng_get_or_create_category( 'Walnuts', 'walnuts', $dry_fruits );
$chips      = ng_get_or_create_category( 'Chips', 'chips' );
$mixes      = ng_get_or_create_category( 'Mixes', 'mixes' );
$others     = ng_get_or_create_category( 'Others', 'others' );
$mouth      = ng_get_or_create_category( 'Mouth Freshners', 'mouth-fresheners' );

$category_map = compact( 'dry_fruits', 'almonds', 'cashews', 'khishmish', 'cranberry', 'walnuts', 'chips', 'mixes', 'others', 'mouth' );
ng_log( 'Product categories created/updated.' );

// --- Reassign demo products ---
$products = wc_get_products( array( 'limit' => -1, 'status' => 'publish' ) );
foreach ( $products as $product ) {
	$name = strtolower( $product->get_name() );
	$ids  = array( $dry_fruits );

	if ( preg_match( '/almond/', $name ) ) {
		$ids[] = $almonds;
	} elseif ( preg_match( '/cashew/', $name ) ) {
		$ids[] = $cashews;
	} elseif ( preg_match( '/walnut/', $name ) ) {
		$ids[] = $walnuts;
	} elseif ( preg_match( '/cranberry|raisin|khishmish/', $name ) ) {
		$ids[] = preg_match( '/cranberry/', $name ) ? $cranberry : $khishmish;
	} elseif ( preg_match( '/chip|crisp/', $name ) ) {
		$ids = array( $chips );
	} elseif ( preg_match( '/mix|mixed/', $name ) ) {
		$ids = array( $mixes, $dry_fruits );
	} elseif ( preg_match( '/mouth|freshen|mukhwas|mint/', $name ) ) {
		$ids = array( $mouth );
	} elseif ( preg_match( '/nut|pistachio|peanut/', $name ) ) {
		$ids = array( $dry_fruits, $almonds );
	} elseif ( preg_match( '/snack|bar/', $name ) ) {
		$ids = array( $chips );
	} else {
		$ids = array( $others );
	}

	ng_assign_product_categories( $product->get_id(), $ids );
}
ng_log( 'Reassigned ' . count( $products ) . ' products to Nutterly Good categories.' );

$shop_url         = get_permalink( wc_get_page_id( 'shop' ) );
$dry_fruits_url   = get_term_link( $dry_fruits, 'product_cat' );
$chips_url        = get_term_link( $chips, 'product_cat' );
$mixes_url        = get_term_link( $mixes, 'product_cat' );
$others_url       = get_term_link( $others, 'product_cat' );
$mouth_url        = get_term_link( $mouth, 'product_cat' );
$about_url        = get_permalink( 3431 );
$contact_url      = get_permalink( 3437 );
$home_url         = home_url( '/' );

$main_menu_items = array(
	array( 'title' => 'Home', 'url' => $home_url ),
	array( 'title' => 'Dry Fruits', 'url' => $dry_fruits_url ),
	array( 'title' => 'Chips', 'url' => $chips_url ),
	array( 'title' => 'Mixes', 'url' => $mixes_url ),
	array( 'title' => 'Others', 'url' => $others_url ),
	array( 'title' => 'Mouth Freshners', 'url' => $mouth_url ),
	array( 'title' => 'Shop', 'url' => $shop_url ),
	array( 'title' => 'About Us', 'url' => $about_url ),
	array( 'title' => 'Contact Us', 'url' => $contact_url ),
);

$menu_ids = array( 76, 71, 72, 70 );
foreach ( $menu_ids as $menu_id ) {
	ng_clear_menu( $menu_id );
	ng_add_menu_items( $menu_id, $main_menu_items );
	ng_log( "Rebuilt menu {$menu_id}." );
}

$footer_menu_1 = array(
	array( 'title' => 'Home', 'url' => $home_url ),
	array( 'title' => 'Products', 'url' => $shop_url ),
	array( 'title' => 'About Us', 'url' => $about_url ),
	array( 'title' => 'Contact Us', 'url' => $contact_url ),
);
$footer_menu_2 = array(
	array( 'title' => 'Dry Fruits', 'url' => $dry_fruits_url ),
	array( 'title' => 'Chips', 'url' => $chips_url ),
	array( 'title' => 'Mixes', 'url' => $mixes_url ),
	array( 'title' => 'Mouth Freshners', 'url' => $mouth_url ),
);
$footer_menu_3 = array(
	array( 'title' => 'Shop', 'url' => $shop_url ),
	array( 'title' => 'My Account', 'url' => get_permalink( 7248 ) ),
	array( 'title' => 'Cart', 'url' => get_permalink( 7246 ) ),
	array( 'title' => 'Contact', 'url' => $contact_url ),
);
ng_clear_menu( 73 );
ng_add_menu_items( 73, $footer_menu_1 );
ng_clear_menu( 74 );
ng_add_menu_items( 74, $footer_menu_2 );
ng_clear_menu( 75 );
ng_add_menu_items( 75, $footer_menu_3 );
ng_log( 'Footer menus rebuilt.' );

// --- Homepage Elementor content ---
$page_id = (int) get_option( 'page_on_front' );
$data    = get_post_meta( $page_id, '_elementor_data', true );
if ( $data ) {
	$elements = json_decode( $data, true );
	if ( is_array( $elements ) ) {
		// Replace broken revolution slider hero with branded hero widgets.
		$elements[0] = array(
			'id'       => 'ng-hero',
			'elType'   => 'container',
			'settings' => array(
				'flex_direction'         => 'column',
				'content_width'          => 'full',
				'min_height'             => array(
					'unit'  => 'px',
					'size'  => 620,
					'sizes' => array(),
				),
				'flex_justify_content'   => 'center',
				'flex_align_items'       => 'center',
				'background_background'  => 'classic',
				'background_image'       => array(
					'url' => home_url( '/wp-content/uploads/2023/09/h1-img-2.jpg' ),
					'id'  => 87,
				),
				'background_position'    => 'center center',
				'background_size'        => 'cover',
				'background_overlay_background' => 'classic',
				'background_overlay_color'      => 'rgba(12,83,61,0.55)',
				'padding'                => array(
					'unit'     => 'px',
					'top'      => '120',
					'right'    => '40',
					'bottom'   => '120',
					'left'     => '40',
					'isLinked' => false,
				),
				'qodef_offset_image_items' => array(),
			),
			'elements' => array(
				array(
					'id'         => 'ng-hero-kicker',
					'elType'     => 'widget',
					'widgetType' => 'greenpath_core_section_title',
					'settings'   => array(
						'title'             => 'Flavor & Freshness',
						'text'              => 'Your Everyday Treat of Tasty Goodness.',
						'content_alignment' => 'center',
						'title_tag'         => 'h6',
						'text_margin_top'   => '12px',
					),
					'elements'   => array(),
				),
				array(
					'id'         => 'ng-hero-copy',
					'elType'     => 'widget',
					'widgetType' => 'text-editor',
					'settings'   => array(
						'editor'      => '<p style="text-align:center;color:#fff;max-width:760px;margin:0 auto;">From ancient authentic roots to modern wellness trends, dry fruits and mouth fresheners have always been a part of India\'s rich culinary heritage.</p><h5 style="text-align:center;color:#B99531;margin-top:20px;">Free delivery for orders above 2,500/- INR</h5>',
						'align'       => 'center',
						'text_color'  => '#FFFFFF',
					),
					'elements'   => array(),
				),
				array(
					'id'         => 'ng-hero-btn',
					'elType'     => 'widget',
					'widgetType' => 'greenpath_core_button',
					'settings'   => array(
						'text'          => 'Start Now',
						'link'          => $dry_fruits_url,
						'button_layout' => 'filled',
						'size'          => 'large',
					),
					'elements'   => array(),
				),
			),
			'isInner'  => false,
		);

		ng_update_elementor_widget_settings(
			$elements,
			function ( $settings, $element ) use ( $category_map, $dry_fruits_url, $shop_url ) {
				$widget = isset( $element['widgetType'] ) ? $element['widgetType'] : '';

				if ( 'greenpath_core_section_title' === $widget ) {
					if ( isset( $settings['title'] ) && 'Fresh, Tasty, and Organic' === $settings['title'] ) {
						$settings['title'] = 'Best Products';
						$settings['text']  = "Check out what's new in our company!";
					}
					if ( isset( $settings['title'] ) && 'Featured Products' === $settings['title'] ) {
						$settings['title'] = 'Fresh Finds';
						$settings['text']  = 'Curated, wholesome treasures to energize your day';
					}
					if ( isset( $settings['title'] ) && 'Latest from Our Blog' === $settings['title'] ) {
						$settings['title'] = 'Why Nutterly Good';
						$settings['text']  = 'Wholesome dry fruits and mouth fresheners for everyday goodness';
					}
				}

				if ( 'greenpath_core_product_list' === $widget ) {
					if ( ! empty( $settings['tax_slug'] ) ) {
						$settings['tax']      = 'product_cat';
						$settings['tax_slug'] = isset( $settings['layout'] ) && 'horizontal' === $settings['layout'] ? 'mixes' : 'dry-fruits';
					}
				}

				if ( 'greenpath_core_product_category_list' === $widget ) {
					$settings['taxonomy_slugs'] = 'dry-fruits,chips,mixes,others,mouth-fresheners';
					$settings['taxonomy_ids']   = implode(
						', ',
						array(
							$category_map['dry_fruits'],
							$category_map['chips'],
							$category_map['mixes'],
							$category_map['others'],
							$category_map['mouth'],
						)
					);
				}

				if ( 'greenpath_core_banner' === $widget ) {
					if ( isset( $settings['title'] ) && false !== stripos( $settings['title'], 'Healthy Eating' ) ) {
						$settings['title']      = 'Dry Fruits & Mouth Fresheners';
						$settings['text_field'] = 'Nutterly Good';
						if ( isset( $settings['link'] ) ) {
							$settings['link']['url'] = $dry_fruits_url;
						}
					}
					if ( isset( $settings['title'] ) && false !== stripos( $settings['title'], 'Organic Superfood' ) ) {
						$settings['title']      = 'Premium Quality';
						$settings['text_field'] = 'Handpicked Goodness';
						if ( isset( $settings['link'] ) ) {
							$settings['link']['url'] = $shop_url;
						}
					}
				}

				if ( 'greenpath_core_custom_font' === $widget && isset( $settings['title'] ) && false !== stripos( $settings['title'], 'Premium Organic Nuts' ) ) {
					$settings['title'] = 'Dry Fruits Collection';
					$settings['color'] = '#0C533D';
				}

				if ( 'text-editor' === $widget && isset( $settings['editor'] ) && false !== stripos( $settings['editor'], '100% Natural' ) ) {
					$settings['editor'] = '<p style="text-align:center;">100% Natural &amp; Wholesome</p>';
				}

				return $settings;
			}
		);

		ng_fix_elementor_widget_types( $elements );
		update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
		delete_post_meta( $page_id, '_elementor_css' );
		ng_log( 'Homepage Elementor content updated (page ' . $page_id . ').' );
	}
}

// --- Page titles ---
wp_update_post(
	array(
		'ID'         => $page_id,
		'post_title' => 'Home',
	)
);

if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

wp_cache_flush();
ng_log( 'Nutterly Good site alignment complete.' );
ng_log( 'Shop: ' . $shop_url );
ng_log( 'Dry Fruits: ' . $dry_fruits_url );