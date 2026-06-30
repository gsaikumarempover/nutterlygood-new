<?php
/**
 * Farmley homepage hero — Revolution Slider mobile text fixes.
 */

if ( ! function_exists( 'nuttergood_farmley_hero_slider_set_bp' ) ) {
	/**
	 * @param array<string, mixed> $layer Layer data.
	 * @param string               $key   Idle property key.
	 * @param array<string, string> $map  Breakpoint => value.
	 */
	function nuttergood_farmley_hero_slider_set_bp( &$layer, $key, $map ) {
		if ( ! isset( $layer['idle'][ $key ] ) || ! is_array( $layer['idle'][ $key ] ) ) {
			$layer['idle'][ $key ] = array();
		}

		foreach ( $map as $bp => $value ) {
			$layer['idle'][ $key ][ $bp ] = array( 'v' => $value );
		}
	}
}

if ( ! function_exists( 'nuttergood_farmley_hero_slider_set_y' ) ) {
	/**
	 * @param array<string, mixed> $layer Layer data.
	 * @param array<string, string> $map  Breakpoint => y offset.
	 */
	function nuttergood_farmley_hero_slider_set_y( &$layer, $map ) {
		if ( ! isset( $layer['position']['y'] ) || ! is_array( $layer['position']['y'] ) ) {
			$layer['position']['y'] = array();
		}

		foreach ( $map as $bp => $value ) {
			$layer['position']['y'][ $bp ] = array( 'v' => $value );
		}
	}
}

if ( ! function_exists( 'nuttergood_farmley_hero_slider_fix_layers' ) ) {
	/**
	 * Correct corrupted responsive sizes (e.g. kicker 90px on mobile).
	 *
	 * @param array<string, mixed> $layers Slide layers.
	 */
	function nuttergood_farmley_hero_slider_fix_layers( $layers ) {
		if ( ! is_array( $layers ) ) {
			return $layers;
		}

		$fixes = array(
			'0' => function ( &$layer ) {
				nuttergood_farmley_hero_slider_set_bp(
					$layer,
					'fontSize',
					array(
						'd' => '14px',
						'n' => '13px',
						't' => '12px',
						'm' => '11px',
					)
				);
				nuttergood_farmley_hero_slider_set_bp(
					$layer,
					'lineHeight',
					array(
						'd' => '20px',
						'n' => '18px',
						't' => '16px',
						'm' => '14px',
					)
				);
				nuttergood_farmley_hero_slider_set_bp(
					$layer,
					'letterSpacing',
					array(
						'd' => '3px',
						'n' => '2px',
						't' => '2px',
						'm' => '1px',
					)
				);
				nuttergood_farmley_hero_slider_set_y(
					$layer,
					array(
						'd' => '38',
						'n' => '32',
						't' => '28',
						'm' => '24',
					)
				);
			},
			'4' => function ( &$layer ) {
				nuttergood_farmley_hero_slider_set_bp(
					$layer,
					'fontSize',
					array(
						'd' => '64px',
						'n' => '52px',
						't' => '42px',
						'm' => '34px',
					)
				);
				nuttergood_farmley_hero_slider_set_bp(
					$layer,
					'lineHeight',
					array(
						'd' => '68px',
						'n' => '56px',
						't' => '46px',
						'm' => '38px',
					)
				);
				nuttergood_farmley_hero_slider_set_y(
					$layer,
					array(
						'd' => '66',
						'n' => '58',
						't' => '50',
						'm' => '42',
					)
				);
				if ( isset( $layer['size']['width'] ) && is_array( $layer['size']['width'] ) ) {
					foreach (
						array(
							'd' => '860px',
							'n' => '90%',
							't' => '92%',
							'm' => '94%',
						) as $bp => $value
					) {
						$layer['size']['width'][ $bp ] = array( 'v' => $value );
					}
				}
			},
			'1' => function ( &$layer ) {
				nuttergood_farmley_hero_slider_set_bp(
					$layer,
					'fontSize',
					array(
						'd' => '18px',
						'n' => '17px',
						't' => '15px',
						'm' => '14px',
					)
				);
				nuttergood_farmley_hero_slider_set_bp(
					$layer,
					'lineHeight',
					array(
						'd' => '28px',
						'n' => '26px',
						't' => '24px',
						'm' => '22px',
					)
				);
				nuttergood_farmley_hero_slider_set_y(
					$layer,
					array(
						'd' => '148',
						'n' => '140',
						't' => '128',
						'm' => '126',
					)
				);
			},
			'3' => function ( &$layer ) {
				nuttergood_farmley_hero_slider_set_bp(
					$layer,
					'fontSize',
					array(
						'd' => '14px',
						'n' => '14px',
						't' => '13px',
						'm' => '12px',
					)
				);
				nuttergood_farmley_hero_slider_set_y(
					$layer,
					array(
						'd' => '188',
						'n' => '178',
						't' => '168',
						'm' => '164',
					)
				);
			},
		);

		foreach ( $fixes as $index => $callback ) {
			if ( isset( $layers[ $index ] ) ) {
				$callback( $layers[ $index ] );
			}
		}

		return $layers;
	}
}

if ( ! function_exists( 'nuttergood_farmley_hero_slider_maybe_migrate' ) ) {
	function nuttergood_farmley_hero_slider_maybe_migrate() {
		$version = 3;
		if ( (int) get_option( 'ng_farmley_hero_slider_fix', 0 ) >= $version ) {
			return;
		}

		global $wpdb;

		$slider_id = $wpdb->get_var(
			"SELECT id FROM {$wpdb->prefix}revslider_sliders WHERE alias = 'main-home' OR title LIKE '%Farmley Hero%' ORDER BY id DESC LIMIT 1"
		);

		if ( ! $slider_id ) {
			return;
		}

		$slides = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, layers FROM {$wpdb->prefix}revslider_slides WHERE slider_id = %d",
				(int) $slider_id
			)
		);

		if ( empty( $slides ) ) {
			return;
		}

		foreach ( $slides as $slide ) {
			$layers = json_decode( $slide->layers, true );
			$layers = nuttergood_farmley_hero_slider_fix_layers( $layers );

			$wpdb->update(
				$wpdb->prefix . 'revslider_slides',
				array( 'layers' => wp_json_encode( $layers ) ),
				array( 'id' => (int) $slide->id ),
				array( '%s' ),
				array( '%d' )
			);
		}

		if ( class_exists( 'RevSliderSlider' ) ) {
			$slider = new RevSliderSlider();
			if ( method_exists( $slider, 'initByID' ) ) {
				$slider->initByID( (int) $slider_id );
			}
			if ( method_exists( $slider, 'refreshSlider' ) ) {
				$slider->refreshSlider();
			}
		}

		update_option( 'ng_farmley_hero_slider_fix', $version, false );

		$page_id = (int) get_option( 'page_on_front' );
		if ( $page_id ) {
			delete_post_meta( $page_id, '_elementor_css' );
		}
	}
	add_action( 'init', 'nuttergood_farmley_hero_slider_maybe_migrate', 5 );
}
