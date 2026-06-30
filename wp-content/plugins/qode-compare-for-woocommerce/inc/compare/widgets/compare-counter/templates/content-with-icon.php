<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( isset( $compare_items ) && ! empty( $compare_items ) ) {
	$count = count( $compare_items );
	?>
	<a class="qcfw-m-open-compare" aria-label="<?php esc_attr__( 'Open comparison', 'qode-compare-for-woocommerce' ); ?>" rel="noopener noreferrer" href="<?php echo esc_url( $opener_url ); ?>">
		<?php qode_compare_for_woocommerce_render_svg_icon( 'compare' ); ?>
		<span class="qcfw-compare-counter-label">
			<?php
			// translators: %s - number of products in comparison.
			echo esc_html( sprintf( _n( '%d product in comparison', '%d products in comparison', $count, 'qode-compare-for-woocommerce' ), $count ) );
			?>
		</span>
	</a>
	<?php
} else {
	qode_compare_for_woocommerce_template_part( 'compare', 'templates/parts/not-found' );
}
