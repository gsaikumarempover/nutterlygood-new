<?php
/**
 * Fix homepage section 3 banner grid (GreenPath layout).
 */
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-load.php';

$page_id = (int) get_option( 'page_on_front' );
$data    = json_decode( get_post_meta( $page_id, '_elementor_data', true ), true );
if ( ! is_array( $data ) ) {
	die( "No elementor data\n" );
}

$walker = function ( &$items ) use ( &$walker ) {
	foreach ( $items as &$el ) {
		$id = $el['id'] ?? '';
		if ( '064fe67' === $id ) {
			$el['settings']['flex_direction']         = 'row';
			$el['settings']['flex_gap']['column']   = '30';
			$el['settings']['flex_gap']['size']     = 30;
		}
		if ( 'ce75316' === $id ) {
			unset( $el['settings']['width'] );
			$el['settings']['flex_grow']  = '1';
			$el['settings']['min_height'] = array( 'unit' => 'px', 'size' => 0, 'sizes' => array() );
		}
		if ( 'fae9d0a' === $id ) {
			unset( $el['settings']['width'] );
			$el['settings']['flex_direction'] = 'column';
			$el['settings']['flex_gap']       = array(
				'column'   => '30',
				'row'      => '30',
				'isLinked' => true,
				'unit'     => 'px',
				'size'     => 30,
			);
			$el['settings']['flex_grow']      = '1';
		}
		if ( '62f9b1e' === $id ) {
			$el['settings']['_padding']['top'] = '0';
			unset( $el['settings']['_padding_mobile_extra'] );
			unset( $el['settings']['_padding_tablet_extra'] );
		}
		if ( ! empty( $el['elements'] ) ) {
			$walker( $el['elements'] );
		}
	}
};
$walker( $data );

update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
delete_post_meta( $page_id, '_elementor_css' );
if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}
echo "Section 3 banner layout updated.\n";