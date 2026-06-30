<?php if ( has_post_thumbnail() ) { ?>
	<?php $thumbnail = get_post_thumbnail_id( get_the_ID() );
	      $img = wp_get_attachment_image_src( $thumbnail, 'full' );

	if ( ! empty( $retina_scaling ) && 'yes' === $retina_scaling ) { ?>
		<div class="qodef-e-media-image">
			<img itemprop="image" src="<?php echo esc_url( $img[0] ); ?>"
			     width="<?php echo round( $img[1] / 2 ); ?>"
			     height="<?php echo round( $img[2] / 2 ); ?>"
			     alt="<?php echo esc_attr( $img[3] ); ?>"/>
		</div>
	<?php } else { ?>
		<div class="qodef-e-media-image">
			<?php the_post_thumbnail( 'full' ); ?>
		</div>
	<?php }
}
?>
