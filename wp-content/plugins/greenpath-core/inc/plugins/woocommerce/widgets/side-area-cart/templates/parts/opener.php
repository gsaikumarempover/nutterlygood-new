<a itemprop="url" class="qodef-m-opener" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
	<span class="qodef-m-opener-icon"><?php echo greenpath_core_get_svg_icon( 'cart' ); ?></span>
	<span class="qodef-m-opener-count"><?php echo WC()->cart->cart_contents_count; ?></span>
	<div class="qodef-m-opener-amount"><?php wc_cart_totals_subtotal_html(); ?></div>
</a>
