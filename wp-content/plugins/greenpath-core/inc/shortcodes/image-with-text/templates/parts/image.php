<div class="qodef-m-image" <?php qode_framework_inline_style( $image_styles ); ?>>
	<?php if ( 'open-popup' === $image_action ) { ?>
		<a class="qodef-magnific-popup qodef-popup-item" itemprop="image" href="<?php echo esc_url( $image_params['url'] ); ?>" data-type="image" title="<?php echo esc_attr( $image_params['alt'] ); ?>">
	<?php } elseif ( 'custom-link' === $image_action && ! empty( $link ) ) { ?>
		<a itemprop="url" href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $target ); ?>">
	<?php } ?>
		<?php
		if( ! empty( $retina_scaling ) && 'yes' === $retina_scaling ) {
			$img = wp_get_attachment_image_src( $image, isset( $image_params['image_size'] ) ? $image_params['image_size'] : 'full' ); ?>
			<img itemprop="image" src="<?php echo esc_url( $img[0] ); ?>"
			     width="<?php echo round( $img[1] / 2 ); ?>"
			     height="<?php echo round( $img[2] / 2 ); ?>"
			     alt="<?php echo esc_attr( $img[3] ); ?>"/>
		<?php
		} else if ( is_array( $image_params['image_size'] ) && count( $image_params['image_size'] ) ) {
			echo qode_framework_generate_thumbnail( $image_params['image_id'], $image_params['image_size'][0], $image_params['image_size'][1] );
		} else {
			echo wp_get_attachment_image( $image_params['image_id'], $image_params['image_size'] );
		}
		if ( ! empty( $hover_image ) ) {
			if( ! empty( $retina_scaling ) && 'yes' === $retina_scaling ) {
				$img = wp_get_attachment_image_src( $hover_image, isset( $hover_image_params['image_size'] ) ? $hover_image_params['image_size'] : 'full' ); ?>
				<img itemprop="image" class="qodef-m-hover-image" src="<?php echo esc_url( $img[0] ); ?>"
				     width="<?php echo round( $img[1] / 2 ); ?>"
				     height="<?php echo round( $img[2] / 2 ); ?>"
				     alt="<?php echo esc_attr( $img[3] ); ?>"/>
				<?php
			} else {
				echo wp_get_attachment_image( $hover_image_params['image_id'], $hover_image_params['image_size'], '', array( 'class' => 'qodef-m-hover-image' ) );
			}
		}
		?>
		<?php if ( 'yes' === $enable_hover && 'yes' === $enable_hover_icon) { ?>
			<span class='qodef--predefined-icon'>
				<?php greenpath_render_svg_icon( 'logo-lemon' ); ?>
			</span>
		<?php } ?>
	<?php if ( 'open-popup' === $image_action || ( 'custom-link' === $image_action && ! empty( $link ) ) ) { ?>
		</a>
	<?php } ?>
</div>
