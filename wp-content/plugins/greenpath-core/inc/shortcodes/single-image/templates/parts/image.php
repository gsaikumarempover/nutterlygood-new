<?php
if ( ! empty( $image ) ) {
	$image_title = get_the_title( $image );
	$image_src   = wp_get_attachment_image_src( $image, 'full' );
	if ( is_array( $image_src ) ) {
		?>
		<div class="qodef-m-image">
			<?php if ( 'open-popup' === $image_action ) { ?>
			<a class="qodef-magnific-popup qodef-popup-item" itemprop="image" href="<?php echo esc_url( $image_src[0] ); ?>" data-type="image" title="<?php echo esc_attr( $image_title ); ?>">
				<?php } elseif ( 'custom-link' === $image_action && ! empty( $link ) ) { ?>
				<a itemprop="url" href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $target ); ?>">
					<?php } ?>
					<?php if ( isset( $retina_scaling ) && 'yes' === $retina_scaling ) { ?>
						<img itemprop="image" src="<?php echo esc_url( $image_src[0] ); ?>" width="<?php echo round( $image_src[1] / 2 ); ?>" height="<?php echo round( $image_src[2] / 2 ); ?>" alt="<?php echo esc_attr( $image_src[3] ); ?>" />
						<?php
					} else {
						$images_proportion   = isset( $images_proportion ) && ! empty( $images_proportion ) ? esc_attr( $images_proportion ) : 'full';
						$custom_image_width  = isset( $custom_image_width ) && '' !== $custom_image_width ? intval( $custom_image_width ) : 0;
						$custom_image_height = isset( $custom_image_height ) && '' !== $custom_image_height ? intval( $custom_image_height ) : 0;
						echo greenpath_core_get_list_shortcode_item_image( $images_proportion, $image, $custom_image_width, $custom_image_height );
					}
					?>
					<?php if ( 'open-popup' === $image_action || ( 'custom-link' === $image_action && ! empty( $link ) ) ) { ?>
				</a>
			<?php } ?>
		</div>
	<?php }
} ?>
