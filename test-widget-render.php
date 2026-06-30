<?php
require __DIR__ . '/wp-load.php';

$site = home_url();
$logo = $site . '/wp-content/uploads/2024/02/greenpath-logo-footer.png';

$params = array(
	'image'          => $logo,
	'retina_scaling' => 'yes',
	'layout'         => 'default',
	'image_action'   => 'custom-link',
	'link'           => $site,
	'target'         => '_self',
);

if ( class_exists( 'GreenPathCore_Single_Image_Shortcode' ) ) {
	echo GreenPathCore_Single_Image_Shortcode::call_shortcode( $params );
	echo "\n\n";
}

$icon = array(
	'predefined_icon' => 'yes',
	'custom_size'     => '54',
	'color'           => '#88a842',
	'link'            => $site,
	'target'          => '_self',
	'custom_class'    => 'qodef-abs-position qodef-custom-position',
);

if ( class_exists( 'GreenPathCore_Icon_Shortcode' ) ) {
	echo GreenPathCore_Icon_Shortcode::call_shortcode( $icon );
}