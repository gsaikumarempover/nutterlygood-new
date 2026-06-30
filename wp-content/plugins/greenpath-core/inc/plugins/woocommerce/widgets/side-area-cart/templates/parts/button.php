<div class="qodef-m-action">
	<a itemprop="url" href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="qodef-m-action-link qodef--cart"><?php esc_html_e( 'View Cart', 'greenpath-core' ); ?></a>
	<a itemprop="url" href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="qodef-m-action-link qodef--checkout"><?php esc_html_e( 'Checkout', 'greenpath-core' ); ?></a>
	<div class="qodef-sale-booster">
		<?php greenpath_core_woo_get_progress_bar(); ?>
	</div>
</div>
