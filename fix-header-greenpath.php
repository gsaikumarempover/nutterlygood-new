<?php
/**
 * Restore GreenPath-style header: top info bar widgets, header icons, categories menu, styling.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

function ng_hlog( $msg ) {
	echo $msg . PHP_EOL;
}

$dot_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="3.372" height="3.372" viewBox="0 0 3.372 3.372"><path d="M2.286-4.686A1.687,1.687,0,0,1,3.972-3,1.687,1.687,0,0,1,2.286-1.314,1.687,1.687,0,0,1,.6-3,1.687,1.687,0,0,1,2.286-4.686Z" transform="translate(-0.6 4.686)" /></svg>';

$user_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="23.5" height="24.499"><path d="M11.679 1.82a3.794 3.794 0 1 0 3.794 3.794 3.8 3.8 0 0 0-3.794-3.794m0-1.57a5.364 5.364 0 1 1-5.364 5.364A5.364 5.364 0 0 1 11.679.25Z" /><path d="M22.433 24.25a.8.8 0 0 1-.813-.738 14.588 14.588 0 0 0-1.49-5.135 8.573 8.573 0 0 0-8.131-4.92 9.3 9.3 0 0 0-8.4 4.443 12.667 12.667 0 0 0-1.72 4.6.8.8 0 0 1-.809.693h-.1a.793.793 0 0 1-.716-.871 13.964 13.964 0 0 1 1.913-5.18 10.948 10.948 0 0 1 9.83-5.252 10.229 10.229 0 0 1 9.613 5.834 15.866 15.866 0 0 1 1.637 5.7.8.8 0 0 1-.767.83.4.4 0 0 1-.047-.004Z" /></svg>';

$deal_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="15.958" height="21"><path fill="#fff" d="m13.438 8.2-.112-.11-.021-.017a6.142 6.142 0 0 1-2.158-5.526L11.437 0 8.955 1.339a8.97 8.97 0 0 0-4.666 7.007L1.5 7.533l-.217.5A13.87 13.87 0 0 0 0 13.736 7.373 7.373 0 0 0 7.382 21h1.194a7.364 7.364 0 0 0 7.382-7.271 6.832 6.832 0 0 0-2.52-5.529Z" /></svg>';

$about_url   = get_permalink( 3431 );
$contact_url = get_permalink( 3437 );
$shop_url    = get_permalink( wc_get_page_id( 'shop' ) );
$account_url = get_permalink( wc_get_page_id( 'myaccount' ) );

// --- Block widgets for top area ---
$blocks = get_option( 'widget_block', array() );
if ( ! isset( $blocks['_multiwidget'] ) ) {
	$blocks['_multiwidget'] = 1;
}

$blocks[13] = array(
	'content' => '<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px;">Info: <a href="tel:+917416285566">+91 74162 85566</a></p>
<!-- /wp:paragraph -->',
);
$blocks[17] = array(
	'content' => '<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px;"><a href="' . esc_url( $about_url ) . '">About</a></p>
<!-- /wp:paragraph -->',
);
$blocks[18] = array(
	'content' => '<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px;"><a href="' . esc_url( $contact_url ) . '">Delivery information</a></p>
<!-- /wp:paragraph -->',
);
$blocks[25] = array(
	'content' => '<!-- wp:paragraph {"fontWeight":"700","className":"qodef-text--main-color"} -->
<p class="qodef-text--main-color" style="font-weight:700;">- Free delivery for orders above ₹2,500 -</p>
<!-- /wp:paragraph -->',
);
$blocks[15] = array(
	'content' => '<!-- wp:paragraph -->
<p><a href="' . esc_url( $contact_url ) . '">FAQ</a></p>
<!-- /wp:paragraph -->',
);
$blocks[16] = array(
	'content' => '<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px;"><a href="' . esc_url( $account_url ) . '">My Account</a></p>
<!-- /wp:paragraph -->',
);
update_option( 'widget_block', $blocks );
ng_hlog( 'Top bar block widgets created (13,15,16,17,18,25).' );

// --- SVG icon widgets ---
$svg_widgets = get_option( 'widget_greenpath_core_svg_icon', array() );
if ( ! isset( $svg_widgets['_multiwidget'] ) ) {
	$svg_widgets['_multiwidget'] = 1;
}

$svg_dot = array(
	'icon'       => $dot_svg,
	'icon_link'  => '',
	'icon_margin' => '0 0 0 0',
);

$svg_widgets[16] = $svg_dot;
$svg_widgets[17] = $svg_dot;
$svg_widgets[18] = $svg_dot;

$svg_widgets[14] = array(
	'icon'              => $user_svg,
	'icon_link'         => $account_url,
	'icon_link_target'  => '_self',
	'icon_margin'       => '0 0 0 0',
	'icon_holder_width' => '24',
	'icon_holder_height' => '24',
);

$svg_widgets[25] = array(
	'icon'              => $deal_svg,
	'icon_link'         => $shop_url,
	'icon_link_target'  => '_self',
	'text'              => 'Best Deal',
	'icon_margin'       => '0 10px 0 0',
	'icon_stroke_color' => '#ffffff00',
	'icon_stroke_hover_color' => '#ffffff00',
);
update_option( 'widget_greenpath_core_svg_icon', $svg_widgets );
ng_hlog( 'Header SVG icon widgets restored (14,16,17,18,25).' );

// --- Sidebar widget assignments ---
$sidebars = get_option( 'sidebars_widgets', array() );
$sidebars['qodef-top-area-left']   = array( 'block-13', 'greenpath_core_svg_icon-16', 'block-17', 'greenpath_core_svg_icon-17', 'block-18' );
$sidebars['qodef-top-area-center'] = array( 'block-25' );
$sidebars['qodef-top-area-right']  = array( 'block-15', 'greenpath_core_svg_icon-18', 'block-16' );
$sidebars['extended-header-one']   = array(
	'greenpath_core_woo_product_search-2',
	'qode_compare_for_woocommerce_compare_counter-8',
	'greenpath_core_svg_icon-14',
	'greenpath_core_qode_wishlist-2',
	'greenpath_core_woo_side_area_cart-2',
);
$sidebars['extended-header-two']   = array( 'greenpath_core_svg_icon-25' );
$sidebars['extended-sticky-widgets'] = array(
	'greenpath_core_search_opener-3',
	'greenpath_core_woo_side_area_cart-4',
);
update_option( 'sidebars_widgets', $sidebars );
ng_hlog( 'Sidebar widget areas assigned (top bar + extended header).' );

// --- Categories dropdown menu (menu id 70) ---
$cat_menu_id = 70;
$existing    = wp_get_nav_menu_items( $cat_menu_id );
if ( $existing ) {
	foreach ( $existing as $item ) {
		wp_delete_post( $item->ID, true );
	}
}

$cat_slugs = array(
	'dry-fruits'       => 'Dry Fruits',
	'almonds'          => 'Almonds',
	'cashews'          => 'Cashews',
	'khishmish'        => 'Khishmish',
	'cranberry'        => 'Cranberry',
	'walnuts'          => 'Walnuts',
	'chips'            => 'Chips',
	'mixes'            => 'Mixes',
	'brittles'         => 'Brittles',
	'mouth-fresheners' => 'Mouth Freshners',
);

$order = 1;
foreach ( $cat_slugs as $slug => $title ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( ! $term ) {
		continue;
	}
	wp_update_nav_menu_item(
		$cat_menu_id,
		0,
		array(
			'menu-item-title'     => $title,
			'menu-item-object'    => 'product_cat',
			'menu-item-object-id' => $term->term_id,
			'menu-item-type'      => 'taxonomy',
			'menu-item-status'    => 'publish',
			'menu-item-position'  => $order++,
		)
	);
}

$locations = get_theme_mod( 'nav_menu_locations', array() );
$locations['extended-dropdown-menu'] = $cat_menu_id;
set_theme_mod( 'nav_menu_locations', $locations );
ng_hlog( 'Categories dropdown menu rebuilt with product categories.' );

// --- Global + homepage header options (GreenPath demo values) ---
$opts = get_option( 'greenpath_core_options', array() );
if ( ! is_array( $opts ) ) {
	$opts = array();
}

$header_opts = array(
	'qodef_header_layout'              => 'standard-extended',
	'qodef_top_area_header'            => 'yes',
	'qodef_top_area_header_in_grid'    => 'yes',
	'qodef_top_area_header_skin'       => 'none',
	'qodef_top_area_header_height'     => '40',
	'qodef_top_area_header_side_padding' => '60',
	'qodef_show_header_widget_areas'   => 'yes',
	'qodef_logo_height'                => '90',
	'qodef_logo_height_mobile'         => '68',
	'qodef_header_custom_widget_area_one' => 'extended-header-one',
	'qodef_header_custom_widget_area_two' => 'extended-header-two',
	'qodef_standard_extended_header_in_grid' => 'yes',
	'qodef_standard_extended_bottom_skin' => 'light',
	'qodef_standard_extended_header_background_color' => '',
	'qodef_standard_extended_header_top_background_color' => '#FFFFFF',
	'qodef_standard_extended_header_bottom_background_color' => '#0C533D',
	'qodef_top_area_header_background_color' => '#FCF4EB',
	'qodef_standard_extended_show_extended_dropdown' => 'yes',
	'qodef_header_scroll_appearance'   => 'sticky',
	'qodef_sticky_header_appearance'   => 'down',
	'qodef_sticky_header_scroll_amount' => '1080',
	'qodef_sticky_header_in_grid'      => 'yes',
	'qodef_sticky_header_custom_widget_area_one' => 'extended-sticky-widgets',
);
$opts = array_merge( $opts, $header_opts );
update_option( 'greenpath_core_options', $opts );

$page_id = (int) get_option( 'page_on_front' );
$page_meta = array_merge(
	$header_opts,
	array(
		'qodef_header_skin'                => 'none',
		'qodef_enable_logo_overflow'       => 'no',
		'qodef_show_header_widget_areas'   => 'yes',
		'qodef_standard_extended_hide_label' => 'no',
		'qodef_content_behind_header'      => 'no',
		'qodef_enable_page_title'          => 'no',
	)
);
foreach ( $page_meta as $key => $val ) {
	update_post_meta( $page_id, $key, $val );
}
ng_hlog( 'Header theme options set (extended layout, green bottom bar, logo 90px).' );

wp_cache_flush();
ng_hlog( '=== Header fix complete. Hard refresh http://localhost/nutterlyGood/ ===' );