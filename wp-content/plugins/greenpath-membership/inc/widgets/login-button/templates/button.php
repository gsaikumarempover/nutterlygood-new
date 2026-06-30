<?php
$button_text = is_user_logged_in() ? esc_html__( 'Logout', 'greenpath-core' ) : esc_html__( 'Login', 'greenpath-core' );
$nav_items   = greenpath_membership_get_dashboard_navigation_pages();

$link        = is_user_logged_in() ? $nav_items['log-out']['url'] : greenpath_membership_get_dashboard_page_url();

$params = array(
	'button_layout' => 'filled',
	'size'          => 'full',
	'link'          => $link,
	'target'        => '_self',
	'text'          => ! empty( $login_button_text ) ? $login_button_text : $button_text,
);

echo greenpath_membership_get_button_element( $params );