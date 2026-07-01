<?php
/**
 * Hide unused GreenPath demo custom post types from wp-admin.
 *
 * Team, Testimonials, and Clients were part of the original theme demo.
 * Nutterly Good uses custom Farmley sections instead (Google reviews, About page, etc.).
 */

if ( ! function_exists( 'nuttergood_farmley_unused_greenpath_cpts' ) ) {
	/**
	 * @return array<int, string>
	 */
	function nuttergood_farmley_unused_greenpath_cpts() {
		return array( 'team', 'testimonials', 'clients' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_disable_unused_greenpath_cpts' ) ) {
	function nuttergood_farmley_disable_unused_greenpath_cpts() {
		foreach ( nuttergood_farmley_unused_greenpath_cpts() as $post_type ) {
			$option = 'greenpath_core_performance_post_type_' . str_replace( '-', '_', $post_type );
			if ( ! get_option( $option ) ) {
				update_option( $option, '1' );
			}
		}
	}
	add_action( 'after_setup_theme', 'nuttergood_farmley_disable_unused_greenpath_cpts', 5 );
}

if ( ! function_exists( 'nuttergood_farmley_dismiss_stale_plugin_notices' ) ) {
	/**
	 * Clear old TGMPA install nag after plugin list was trimmed to match the live stack.
	 */
	function nuttergood_farmley_dismiss_stale_plugin_notices() {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$user_id = get_current_user_id();
		if ( $user_id && ! get_user_meta( $user_id, 'tgmpa_dismissed_notice_tgmpa', true ) ) {
			update_user_meta( $user_id, 'tgmpa_dismissed_notice_tgmpa', 1 );
		}
	}
	add_action( 'admin_init', 'nuttergood_farmley_dismiss_stale_plugin_notices', 20 );
}