<?php
/**
 * Install/activate Qode Compare & Wishlist plugins and fix myaccount URL.
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

function ng_log( $msg ) {
	echo $msg . PHP_EOL;
}

$plugins = array(
	'qode-compare-for-woocommerce' => 'qode-compare-for-woocommerce/class-qode-compare-for-woocommerce.php',
	'qode-wishlist-for-woocommerce' => 'qode-wishlist-for-woocommerce/class-qode-wishlist-for-woocommerce.php',
);

foreach ( $plugins as $slug => $main_file ) {
	$dest = WP_PLUGIN_DIR . '/' . dirname( $main_file );
	if ( ! file_exists( WP_PLUGIN_DIR . '/' . $main_file ) ) {
		ng_log( "Downloading $slug..." );
		$api = plugins_api( 'plugin_information', array( 'slug' => $slug, 'fields' => array( 'sections' => false ) ) );
		if ( is_wp_error( $api ) ) {
			ng_log( "ERROR fetching $slug: " . $api->get_error_message() );
			continue;
		}
		$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$result   = $upgrader->install( $api->download_link );
		if ( is_wp_error( $result ) ) {
			ng_log( "ERROR installing $slug: " . $result->get_error_message() );
			continue;
		}
		ng_log( "Installed $slug." );
	} else {
		ng_log( "$slug already present." );
	}

	if ( ! is_plugin_active( $main_file ) ) {
		$activated = activate_plugin( $main_file );
		if ( is_wp_error( $activated ) ) {
			ng_log( "ERROR activating $slug: " . $activated->get_error_message() );
		} else {
			ng_log( "Activated $slug." );
		}
	} else {
		ng_log( "$slug already active." );
	}
}

// Fix WooCommerce my-account page.
$account_id = (int) wc_get_page_id( 'myaccount' );
if ( $account_id <= 0 || ! get_post( $account_id ) ) {
	$account_id = wp_insert_post(
		array(
			'post_title'   => 'My Account',
			'post_name'    => 'my-account',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '[woocommerce_my_account]',
		)
	);
	if ( $account_id && ! is_wp_error( $account_id ) ) {
		update_option( 'woocommerce_myaccount_page_id', $account_id );
		ng_log( "Created my-account page id $account_id." );
	}
} else {
	$account = get_post( $account_id );
	if ( 'publish' !== $account->post_status ) {
		wp_update_post( array( 'ID' => $account_id, 'post_status' => 'publish' ) );
		ng_log( "Published my-account page id $account_id." );
	}
	if ( empty( $account->post_content ) || false === strpos( $account->post_content, 'woocommerce_my_account' ) ) {
		wp_update_post(
			array(
				'ID'           => $account_id,
				'post_content' => '[woocommerce_my_account]',
			)
		);
		ng_log( "Set my-account shortcode on page $account_id." );
	}
	ng_log( 'My Account URL: ' . get_permalink( $account_id ) );
}

// Update block-16 and svg-14 with correct account URL.
$account_url = get_permalink( wc_get_page_id( 'myaccount' ) );
$blocks      = get_option( 'widget_block', array() );
if ( ! empty( $blocks[16]['content'] ) ) {
	$blocks[16]['content'] = '<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px;"><a href="' . esc_url( $account_url ) . '">My Account</a></p>
<!-- /wp:paragraph -->';
	update_option( 'widget_block', $blocks );
	ng_log( 'Updated top bar My Account link.' );
}

$svg = get_option( 'widget_greenpath_core_svg_icon', array() );
if ( ! empty( $svg[14] ) ) {
	$svg[14]['icon_link'] = $account_url;
	update_option( 'widget_greenpath_core_svg_icon', $svg );
	ng_log( 'Updated header account icon link.' );
}

// Disable logo overflow (GreenPath demo has it but user wants match - check demo: qodef-logo-overflow--yes on demo)
// Fix logo height in customizer/transient
$opts = get_option( 'greenpath_core_options', array() );
$opts['qodef_logo_height'] = '90';
$opts['qodef_enable_logo_overflow'] = 'no';
update_option( 'greenpath_core_options', $opts );

$page_id = (int) get_option( 'page_on_front' );
update_post_meta( $page_id, 'qodef_logo_height', '90' );
update_post_meta( $page_id, 'qodef_enable_logo_overflow', 'no' );

delete_option( 'greenpath_core_dynamic_styles' );
delete_transient( 'greenpath_core_dynamic_styles' );
wp_cache_flush();

ng_log( '=== Plugin + account fix complete ===' );