<?php if ( 'instock' == $product->get_stock_status()) {?>
<span class="qodef-pli-stock">
	<?php esc_html_e('Available: ', 'greenpath-core'); ?>
	<?php echo esc_html( $product->get_stock_quantity() ); ?>
	<?php esc_html_e(' in stock', 'greenpath-core'); ?>
</span>
<?php } else { ?>
<span class="qodef-pli-stock">
	<?php esc_html_e('Out of stock', 'greenpath-core'); ?>
</span>
<?php } ?>
