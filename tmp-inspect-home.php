<?php
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$page_id = (int) get_option( 'page_on_front' );
$raw     = get_post_meta( $page_id, '_elementor_data', true );

echo 'Front page ID: ' . $page_id . PHP_EOL;
echo 'Has ca3823c: ' . ( strpos( $raw, 'ca3823c' ) !== false ? 'yes' : 'no' ) . PHP_EOL;
echo 'Has 1ea2b9b: ' . ( strpos( $raw, '1ea2b9b' ) !== false ? 'yes' : 'no' ) . PHP_EOL;
echo 'Has blog title: ' . ( strpos( $raw, 'Latest from Our Blog' ) !== false ? 'yes' : 'no' ) . PHP_EOL;

$posts = get_posts(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'numberposts'    => 10,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'suppress_filters' => false,
	)
);
echo 'Published posts: ' . count( $posts ) . PHP_EOL;
foreach ( $posts as $p ) {
	echo ' - ' . $p->ID . ' | ' . $p->post_title . PHP_EOL;
}

// Content bottom widgets
$widgets = get_option( 'sidebars_widgets', array() );
echo 'content-bottom widgets: ' . print_r( $widgets['content-bottom'] ?? 'none', true ) . PHP_EOL;

// CF7 forms
if ( class_exists( 'WPCF7_ContactForm' ) ) {
	$forms = WPCF7_ContactForm::find();
	echo 'CF7 forms: ' . count( $forms ) . PHP_EOL;
	foreach ( $forms as $form ) {
		echo ' - ' . $form->id() . ' | ' . $form->title() . PHP_EOL;
	}
}

// Parse elementor containers at end
$elements = json_decode( $raw, true );
if ( is_array( $elements ) ) {
	echo 'Top-level containers: ' . count( $elements ) . PHP_EOL;
	foreach ( $elements as $el ) {
		$id = $el['id'] ?? '';
		$widgets = array();
		$walk = function ( $nodes ) use ( &$walk, &$widgets ) {
			foreach ( $nodes as $n ) {
				if ( ! empty( $n['widgetType'] ) ) {
					$widgets[] = ( $n['id'] ?? '' ) . ':' . $n['widgetType'];
				}
				if ( ! empty( $n['elements'] ) ) {
					$walk( $n['elements'] );
				}
			}
		};
		if ( ! empty( $el['elements'] ) ) {
			$walk( $el['elements'] );
		}
		echo "Container {$id}: " . implode( ', ', array_slice( $widgets, 0, 5 ) ) . PHP_EOL;
	}
}