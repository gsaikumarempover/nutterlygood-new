<?php
$pages = array(
	'contact'  => 'http://localhost/nutterlyGood/contact/',
	'about-us' => 'http://localhost/nutterlyGood/about-us/',
);

foreach ( $pages as $slug => $url ) {
	$html = file_get_contents( $url );
	echo "=== {$slug} ===\n";
	echo 'map strip: ' . ( strpos( $html, 'ng-farmley-contact-map-strip' ) !== false ? 'yes' : 'no' ) . "\n";
	echo 'about content: ' . ( strpos( $html, 'ng-farmley-about' ) !== false ? 'yes' : 'no' ) . "\n";
	echo 'page title: ' . ( strpos( $html, 'qodef-page-title qodef-m' ) !== false ? 'yes' : 'no' ) . "\n";
	echo 'old placeholder: ' . ( strpos( $html, 'Healthy Snacks' ) !== false ? 'yes' : 'no' ) . "\n";
	$header = strpos( $html, 'qodef-page-header' );
	$map    = strpos( $html, 'ng-farmley-contact-map-strip' );
	$form   = strpos( $html, 'ng-farmley-contact ' );
	if ( $header && $map && $form ) {
		echo 'contact order: ' . ( $header < $map && $map < $form ? 'OK' : 'BAD' ) . "\n";
	}
	echo "\n";
}