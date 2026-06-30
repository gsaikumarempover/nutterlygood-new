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
		<span class="qcfw-compare-counter-label-text">
			<?php esc_html_e( 'Compare', 'qode-compare-for-woocommerce' ); ?>
		</span>
		<span class="qcfw-compare-counter-label-count">
			<?php echo esc_html( $count ); ?>
		</span>
	</div>
<?php if ( $count > 0 ) : ?>
</a>
<?php endif; ?>
