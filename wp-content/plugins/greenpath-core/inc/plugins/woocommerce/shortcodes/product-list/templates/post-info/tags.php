<?php
$tags = wp_get_post_terms( get_the_ID(), 'product_tag' );

if ( ! empty( $tags ) ) { ?>
	<?php echo get_the_term_list( get_the_ID(), 'product_tag', '', '<span class="qodef-info-separator-single"></span>' ); ?>
	<div class="qodef-info-separator-end"></div>
<?php } ?>
