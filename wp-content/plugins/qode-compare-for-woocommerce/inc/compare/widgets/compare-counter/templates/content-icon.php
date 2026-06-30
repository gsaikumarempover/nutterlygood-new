<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

$count = isset( $compare_items ) && ! empty( $compare_items ) ? count( $compare_items ) : 0;

if ( $count > 0 ) : ?>
<a class="qcfw-m-open-compare" aria-label="<?php esc_attr__( 'Open comparison', 'qode-compare-for-woocommerce' ); ?>" rel="noopener noreferrer" href="<?php echo esc_url( $opener_url ); ?>">
	<?php endif; ?>
	<div class="qcfw-compare-counter-label">
		<?php qode_compare_for_woocommerce_render_svg_icon( 'compare' ); ?>
		<span class="qcfw-compare-counter-label-count">
			<?php echo esc_html( $count ); ?>
		</span>
	</div>
	<?php if ( $count > 0 ) : ?>
</a>
<?php endif; ?>
