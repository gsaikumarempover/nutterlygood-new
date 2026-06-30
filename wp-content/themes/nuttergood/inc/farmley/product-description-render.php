<?php
/**
 * Product Description tab — simple intro + bullet points only.
 */

if ( ! function_exists( 'nuttergood_farmley_render_description_tab' ) ) {
	function nuttergood_farmley_render_description_tab() {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$intro   = nuttergood_farmley_get_description_intro( $product );
		$bullets = nuttergood_farmley_get_description_bullets( $product );

		if ( '' === $intro && empty( $bullets ) ) {
			return;
		}

		echo '<div class="ng-farmley-sp-description">';

		if ( '' !== $intro ) {
			echo '<div class="ng-farmley-sp-description__text">';
			echo wp_kses_post( wpautop( $intro ) );
			echo '</div>';
		}

		if ( ! empty( $bullets ) ) {
			echo '<ul class="ng-farmley-sp-bullets">';
			foreach ( $bullets as $bullet ) {
				printf( '<li>%s</li>', esc_html( $bullet ) );
			}
			echo '</ul>';
		}

		echo '</div>';
	}
}
