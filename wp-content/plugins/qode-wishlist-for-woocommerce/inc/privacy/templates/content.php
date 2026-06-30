<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}
?>
<div class="wp-suggested-text">
	<?php
	foreach ( $sections as $key => $section ) {
		$content = apply_filters( 'qode_wishlist_for_woocommerce_filter_privacy_policy_guide_content', '', $key );

		if ( ! empty( $section['tutorial'] ) || ! empty( $section['description'] ) || $content ) {
			if ( ! empty( $section['title'] ) ) {
				echo '<h2>' . esc_html( $section['title'] ) . '</h2>';
			}

			if ( ! empty( $section['tutorial'] ) ) {
				echo '<p class="privacy-policy-tutorial">' . wp_kses_post( $section['tutorial'] ) . '</p>';
			}

			if ( ! empty( $section['description'] ) ) {
				echo '<p >' . wp_kses_post( $section['description'] ) . '</p>';
			}

			if ( ! empty( $content ) ) {
				echo wp_kses_post( $content );
			}
		}
	}
	?>
</div>
