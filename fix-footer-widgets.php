<?php
/**
 * One-time repair: restore GreenPath-style footer widget instances.
 */
require __DIR__ . '/wp-load.php';

if ( ! current_user_can( 'manage_options' ) && php_sapi_name() !== 'cli' ) {
	wp_die( 'Unauthorized' );
}

$site = home_url( '/' );
$year = gmdate( 'Y' );

/**
 * Use the same logo as the site header / customizer upload.
 */
function nuttergood_ensure_footer_logo_id() {
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		return $logo_id;
	}
	if ( function_exists( 'greenpath_core_get_post_value_through_levels' ) ) {
		$logo_id = (int) greenpath_core_get_post_value_through_levels( 'qodef_logo_main' );
	}
	return $logo_id;
}

$logo_id = nuttergood_ensure_footer_logo_id();

$phone_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20.003 20.003"><path d="M-6.66,0a1.1,1.1,0,0,0,.22-.04A22.752,22.752,0,0,0-1.42-2.65,31.467,31.467,0,0,0,3.42-6.58a31.257,31.257,0,0,0,3.92-4.83,23.313,23.313,0,0,0,2.62-5.03.658.658,0,0,0,.04-.2.631.631,0,0,0-.1-.34L8.54-19.22a.625.625,0,0,0-.46-.28L3.5-20a.556.556,0,0,0-.36.08.807.807,0,0,0-.26.26L.46-14.76a.619.619,0,0,0-.06.28.586.586,0,0,0,.14.4L2.1-12.22A11.021,11.021,0,0,1,.3-9.7,11.021,11.021,0,0,1-2.22-7.9L-4.08-9.46a.586.586,0,0,0-.4-.14.619.619,0,0,0-.28.06l-4.9,2.42a.807.807,0,0,0-.26.26A.556.556,0,0,0-10-6.5l.5,4.58a.569.569,0,0,0,.3.46L-6.98-.1A.6.6,0,0,0-6.66,0ZM-2.7-6.68a.513.513,0,0,0,.38.14.619.619,0,0,0,.28-.06A11.284,11.284,0,0,0,1.18-8.82a11.952,11.952,0,0,0,2.22-3.2.815.815,0,0,0,.06-.28.541.541,0,0,0-.14-.4L1.76-14.58,3.8-18.7l3.84.4,1.04,1.72A28.571,28.571,0,0,1,2.54-7.46,28.422,28.422,0,0,1-6.58-1.32L-8.3-2.36-8.7-6.2l4.12-2.04Z" transform="translate(10.003 20.003)" /></svg>';

$email_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="12" viewBox="0 0 20 12.5"><path d="M10-4.34l-.04.12-.02.12L9.86-4l-.08.08-.02.04-.08.04L9.6-3.8a.5.5,0,0,1-.22.04H-9.38A.5.5,0,0,1-9.6-3.8l-.08-.04-.08-.04-.02-.02L-9.86-4l-.08-.1-.02-.12L-10-4.34V-15.66l.04-.12.02-.12.08-.1.08-.1.01-.01.01-.01.06-.04.12-.06.12-.02a.116.116,0,0,0,.08-.02H9.38a.116.116,0,0,0,.08.02l.12.04a.307.307,0,0,1,.12.04q.04.04.06.04l.02.04a.216.216,0,0,1,.08.1l.08.08.02.12.04.12V-4.38ZM5.5-9.62a.59.59,0,0,1-.5.24H-5a.59.59,0,0,1-.5-.24L-8.74-13.8v7.6L-8-7.18a.59.59,0,0,1,.5-.24.513.513,0,0,1,.38.14.59.59,0,0,1,.24.5A.674.674,0,0,1-7-6.4L-8.1-5H8.1L7-6.4a.674.674,0,0,1-.12-.38.59.59,0,0,1,.24-.5.513.513,0,0,1,.38-.14.59.59,0,0,1,.5.24l.76.98v-7.6Zm-.8-1L8.1-15H-8.1l3.4,4.38Z" transform="translate(10 16.26)" /></svg>';

$location_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="20" viewBox="0 0 15 20"><path d="M0,0A.6.6,0,0,0,.29-.07.577.577,0,0,0,.5-.26l5.62-7.9a7.723,7.723,0,0,0,1.03-2.09A7.441,7.441,0,0,0,7.5-12.5a7.494,7.494,0,0,0-.64-3.04A7.438,7.438,0,0,0,5-18.1a7.184,7.184,0,0,0-2.33-1.42A7.6,7.6,0,0,0,0-20a7.623,7.623,0,0,0-2.66.48A7.159,7.159,0,0,0-5-18.1a7.438,7.438,0,0,0-1.86,2.56A7.494,7.494,0,0,0-7.5-12.5a7.441,7.441,0,0,0,.35,2.25A7.723,7.723,0,0,0-6.12-8.16L-.5-.26a.577.577,0,0,0,.21.19A.6.6,0,0,0,0,0ZM0-18.76a6.136,6.136,0,0,1,3.24.91,6.221,6.221,0,0,1,2.32,2.47,6.207,6.207,0,0,1,.7,2.88,6.121,6.121,0,0,1-.3,1.89A6.456,6.456,0,0,1,5.1-8.88L0-1.7-5.1-8.88a6.456,6.456,0,0,1-.86-1.73,6.121,6.121,0,0,1-.3-1.89,6.207,6.207,0,0,1,.7-2.88,6.221,6.221,0,0,1,2.32-2.47A6.136,6.136,0,0,1,0-18.76Zm0,9.7a3.377,3.377,0,0,0,1.73-.46,3.406,3.406,0,0,0,1.25-1.25,3.377,3.377,0,0,0,.46-1.73,3.377,3.377,0,0,0-.46-1.73,3.406,3.406,0,0,0-1.25-1.25A3.377,3.377,0,0,0,0-15.94a3.377,3.377,0,0,0-1.73.46,3.406,3.406,0,0,0-1.25,1.25,3.377,3.377,0,0,0-.46,1.73,3.377,3.377,0,0,0,.46,1.73A3.406,3.406,0,0,0-1.73-9.52,3.377,3.377,0,0,0,0-9.06Zm0-5.62a2.1,2.1,0,0,1,1.54.64,2.1,2.1,0,0,1,.64,1.54,2.1,2.1,0,0,1-.64,1.54A2.1,2.1,0,0,1,0-10.32a2.1,2.1,0,0,1-1.54-.64,2.1,2.1,0,0,1-.64-1.54,2.1,2.1,0,0,1,.64-1.54A2.1,2.1,0,0,1,0-14.68Z" transform="translate(7.5 20)" /></svg>';

$facebook_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="10.176" height="19" viewBox="0 0 10.176 19"><path d="M10.359-5.937l.528-3.439h-3.3v-2.231a1.719,1.719,0,0,1,1.939-1.858h1.5v-2.928a18.293,18.293,0,0,0-2.663-.232C5.646-16.625,3.87-14.978,3.87-12v2.621H.849v3.439H3.87V2.375H7.587V-5.937Z" transform="translate(-0.849 16.625)" /></svg>';

$instagram_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17"><path d="M8.316-11.393A4.257,4.257,0,0,0,4.052-7.129,4.257,4.257,0,0,0,8.316-2.865,4.257,4.257,0,0,0,12.58-7.129,4.257,4.257,0,0,0,8.316-11.393Zm0,7.036A2.777,2.777,0,0,1,5.544-7.129,2.775,2.775,0,0,1,8.316-9.9a2.775,2.775,0,0,1,2.772,2.772A2.777,2.777,0,0,1,8.316-4.357Zm5.433-7.21a1,1,0,0,0-.995-.995,1,1,0,0,0-.995.995.992.992,0,0,0,.995.995A.992.992,0,0,0,13.749-11.567Zm2.824,1.009a4.922,4.922,0,0,0-1.343-3.485,4.954,4.954,0,0,0-3.485-1.343c-1.373-.078-5.488-.078-6.862,0A4.947,4.947,0,0,0,1.4-14.046,4.938,4.938,0,0,0,.056-10.561C-.022-9.188-.022-5.073.056-3.7A4.922,4.922,0,0,0,1.4-.215,4.96,4.96,0,0,0,4.884,1.128c1.373.078,5.488.078,6.862,0A4.922,4.922,0,0,0,15.23-.215,4.954,4.954,0,0,0,16.573-3.7C16.651-5.073,16.651-9.185,16.573-10.558ZM14.8-2.227A2.806,2.806,0,0,1,13.218-.646c-1.095.434-3.692.334-4.9.334s-3.811.1-4.9-.334A2.806,2.806,0,0,1,1.833-2.227C1.4-3.321,1.5-5.919,1.5-7.129s-.1-3.811.334-4.9a2.806,2.806,0,0,1,1.581-1.581c1.095-.434,3.692-.334,4.9-.334s3.811-.1,4.9.334A2.806,2.806,0,0,1,14.8-12.031c.434,1.095.334,3.692.334,4.9S15.233-3.318,14.8-2.227Z" transform="translate(0.003 15.444)" /></svg>';

$twitter_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="19" height="15.432" viewBox="0 0 19 15.432"><path d="M17.047-10.995A8.37,8.37,0,0,0,19-13.008a7.807,7.807,0,0,1-2.242.6,3.884,3.884,0,0,0,1.712-2.146,7.666,7.666,0,0,1-2.471.94,3.885,3.885,0,0,0-2.845-1.23,3.892,3.892,0,0,0-3.894,3.894,4.4,4.4,0,0,0,.1.892,11.069,11.069,0,0,1-8.029-4.075A3.864,3.864,0,0,0,.8-12.164,3.891,3.891,0,0,0,2.532-8.921a3.921,3.921,0,0,1-1.76-.494v.048A3.9,3.9,0,0,0,3.894-5.546a4.117,4.117,0,0,1-1.025.133,4.908,4.908,0,0,1-.735-.06,3.9,3.9,0,0,0,3.641,2.7A7.8,7.8,0,0,1,.94-1.109,8.059,8.059,0,0,1,0-1.157,11,11,0,0,0,5.98.591,11,11,0,0,0,17.059-10.489C17.059-10.657,17.059-10.826,17.047-10.995Z" transform="translate(0 14.841)" /></svg>';

$pinterest_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="14.25" height="18.521" viewBox="0 0 14.25 18.521"><path d="M7.57-16.384C3.763-16.384,0-13.845,0-9.738c0,2.613,1.47,4.1,2.36,4.1.367,0,.579-1.024.579-1.314,0-.345-.879-1.08-.879-2.516a5.015,5.015,0,0,1,5.21-5.1c2.527,0,4.4,1.436,4.4,4.075,0,1.97-.79,5.667-3.351,5.667A1.648,1.648,0,0,1,6.6-6.453c0-1.4.98-2.761.98-4.208,0-2.457-3.485-2.011-3.485.957a4.36,4.36,0,0,0,.356,1.881c-.512,2.2-1.559,5.488-1.559,7.76,0,.7.1,1.392.167,2.093.126.141.063.126.256.056,1.87-2.561,1.8-3.062,2.65-6.412A3.01,3.01,0,0,0,8.539-2.991c3.941,0,5.711-3.841,5.711-7.3C14.25-13.979,11.066-16.384,7.57-16.384Z" transform="translate(0 16.384)" /></svg>';

$updates = array(
	'widget_greenpath_core_single_image' => array(
		'_multiwidget' => 1,
		11             => array(
			'image'          => (string) $logo_id,
			'retina_scaling' => 'yes',
			'layout'         => 'default',
			'image_action'   => 'custom-link',
			'link'           => $site,
			'target'         => '_self',
		),
	),
	'widget_greenpath_core_icon'         => array(
		'_multiwidget' => 1,
		3              => array(
			'predefined_icon' => 'yes',
			'custom_size'     => '54',
			'color'           => '#88a842',
			'link'            => $site,
			'target'          => '_self',
			'custom_class'    => 'qodef-abs-position qodef-custom-position',
		),
	),
	'widget_greenpath_core_svg_icon'     => array(
		'_multiwidget' => 1,
		3              => array(
			'icon'                   => $facebook_svg,
			'icon_link'              => 'https://www.facebook.com/',
			'icon_link_target'       => '_blank',
			'icon_margin'            => '0 20px 0 0',
			'icon_stroke_color'      => 'transparent',
			'icon_stroke_hover_color'=> 'transparent',
		),
		4              => array(
			'icon'                   => $instagram_svg,
			'icon_link'              => 'https://www.instagram.com/',
			'icon_link_target'       => '_blank',
			'icon_margin'            => '0 20px 0 0',
			'icon_stroke_color'      => 'transparent',
			'icon_stroke_hover_color'=> 'transparent',
		),
		5              => array(
			'icon'                   => $twitter_svg,
			'icon_link'              => 'https://twitter.com/',
			'icon_link_target'       => '_blank',
			'icon_margin'            => '0 20px 0 0',
			'icon_stroke_color'      => 'transparent',
			'icon_stroke_hover_color'=> 'transparent',
		),
		8              => array(
			'widget_margin'          => '0 0 12px',
			'icon'                   => $phone_svg,
			'text'                   => '+91 74162 85566',
			'icon_link'              => 'tel:+917416285566',
			'icon_link_target'       => '_blank',
			'icon_margin'            => '0 -1px 0 0',
			'icon_fill_color'        => '#fdd835',
			'icon_fill_hover_color'  => '#fdd835',
			'icon_stroke_color'      => 'transparent',
			'icon_stroke_hover_color'=> 'transparent',
			'text_tag'               => 'h5',
			'space_between_icon_text'=> '14',
			'icon_vertical_alignment'=> 'center',
		),
		9              => array(
			'icon'                   => $pinterest_svg,
			'icon_link'              => 'https://www.pinterest.com/',
			'icon_link_target'       => '_blank',
			'icon_margin'            => '0',
			'icon_stroke_color'      => 'transparent',
			'icon_stroke_hover_color'=> 'transparent',
		),
		11             => array(
			'widget_margin'          => '0 0 12px',
			'icon'                   => $email_svg,
			'text'                   => 'contact@nutterlygood.com',
			'icon_link'              => 'mailto:contact@nutterlygood.com',
			'icon_link_target'       => '_blank',
			'icon_margin'            => '0 -2px -9px 0',
			'icon_fill_color'        => '#fdd835',
			'icon_fill_hover_color'  => '#fdd835',
			'icon_stroke_color'      => 'transparent',
			'icon_stroke_hover_color'=> 'transparent',
			'text_tag'               => 'h5',
			'space_between_icon_text'=> '15',
			'icon_vertical_alignment'=> 'center',
		),
		13             => array(
			'icon'                   => $location_svg,
			'text'                   => 'CS-09, Etna Block, Rajapushpa Atria, Golden Mile Road, Kokapet, Hyderabad, Telangana 500075',
			'icon_link'              => 'https://www.google.com/maps/search/?api=1&query=Rajapushpa+Atria+Kokapet+Hyderabad',
			'icon_link_target'       => '_blank',
			'icon_margin'            => '0 2px 0 0',
			'icon_fill_color'        => '#fdd835',
			'icon_fill_hover_color'  => '#fdd835',
			'icon_stroke_color'      => 'transparent',
			'icon_stroke_hover_color'=> 'transparent',
			'text_tag'               => 'h5',
			'space_between_icon_text'=> '15',
			'icon_vertical_alignment'=> 'center',
		),
	),
	'widget_block'                       => array(
		'_multiwidget' => 1,
		6              => array(
			'content' => '<!-- wp:paragraph {"style":{"typography":{"fontSize":"12px"}}} -->'
				. '<p style="font-size:12px;">© ' . esc_html( $year ) . ' <a href="' . esc_url( $site ) . '" class="qodef--has-underline">NutterlyGood</a>, All Rights Reserved</p>'
				. '<!-- /wp:paragraph -->',
		),
	),
);

foreach ( $updates as $option => $value ) {
	update_option( $option, $value, false );
	echo "Updated {$option}\n";
}

echo "Footer logo attachment ID: {$logo_id}\n";
echo "Done.\n";