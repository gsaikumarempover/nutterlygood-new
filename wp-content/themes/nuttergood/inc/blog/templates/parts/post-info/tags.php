<?php
$tags = get_the_tags();

if ( $tags ) {
	if( ! is_singular( 'post' ) ) {
        the_tags( '', '<span class="qodef-info-separator-single"></span>','<div class="qodef-info-separator-end"></div>' );
    } else { ?>
	<div class="qodef-tag-holder">
		<span class="qodef-tag-label"><?php echo esc_html__( 'Tags: ', 'nuttergood' ) ?></span>
		<?php the_tags( '', '' ); ?>
	</div>
<?php }
}
