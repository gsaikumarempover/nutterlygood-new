<?php
$item_id            = get_the_ID();
$client_image       = get_post_meta( $item_id, 'qodef_logo_image', true );
$client_hover_image = get_post_meta( $item_id, 'qodef_logo_hover_image', true );
$link               = get_post_meta( $item_id, 'qodef_client_link', true );
$link_target        = get_post_meta( $item_id, 'qodef_client_link_target', true );
$link_target        = ! empty( $link_target ) ? $link_target : '_blank';

$client_image_src       = wp_get_attachment_image_src( $client_image, 'full' );
$client_hover_image_src = wp_get_attachment_image_src( $client_hover_image, 'full' );

if ( ! empty( $client_image ) ) { ?>
	<span class="qodef-e-image">
		<?php if ( ! empty( $link ) ) { ?>
			<a itemprop="url" href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $link_target ); ?>">
		<?php } ?>
				<?php if ( ! empty( $client_image ) ) { ?>
					<span class="qodef-e-logo">
					<?php if ( isset( $retina_scaling ) && 'yes' === $retina_scaling ) { ?>
						<img itemprop="image" src="<?php echo esc_url( $client_image_src[0] ); ?>"
							 width="<?php echo round( $client_image_src[1] / 2 ); ?>"
							 height="<?php echo round( $client_image_src[2] / 2 ); ?>"
							 alt="<?php echo esc_attr( $client_image_src[3] ); ?>"/>
					<?php } else { ?>
						<?php echo wp_get_attachment_image( $client_image, 'full' ); ?>
					<?php } ?>
				</span>
				<?php } ?>
				<?php if ( ! empty( $client_hover_image ) ) { ?>
					<span class="qodef-e-hover-logo">
					<?php if ( isset( $retina_scaling ) && 'yes' === $retina_scaling ) { ?>
						<img itemprop="image" src="<?php echo esc_url( $client_hover_image_src[0] ); ?>"
							 width="<?php echo round( $client_hover_image_src[1] / 2 ); ?>"
							 height="<?php echo round( $client_hover_image_src[2] / 2 ); ?>"
							 alt="<?php echo esc_attr( $client_hover_image_src[3] ); ?>"/>
					<?php } else { ?>
						<?php echo wp_get_attachment_image( $client_hover_image, 'full' ); ?>
					<?php } ?>
				</span>
				<?php } ?>
				<?php if ( ! empty( $link ) ) { ?>
			</a>
		<?php } ?>
	</span>
<?php } ?>
