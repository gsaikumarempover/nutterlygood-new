<?php
$categories = wp_get_post_terms( get_the_ID(), 'product_cat' );

if ( ! empty( $categories ) ) { ?>
	<?php echo get_the_term_list( get_the_ID(), 'product_cat', '', '<span class="qodef-info-separator-single"></span>' ); ?>
	<div class="qodef-info-separator-end"></div>
<?php } ?>
