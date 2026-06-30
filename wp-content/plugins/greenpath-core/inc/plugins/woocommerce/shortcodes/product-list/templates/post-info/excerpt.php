<?php
$excerpt = get_post_meta( get_the_ID(), 'qodef_product_short_description', true );

if ( ! empty( $excerpt ) ) { ?>
	<p itemprop="description" class="qodef-woo-product-excerpt"><?php echo esc_html( $excerpt ); ?></p>
<?php } ?>
