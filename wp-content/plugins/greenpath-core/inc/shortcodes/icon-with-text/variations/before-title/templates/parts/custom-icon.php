<?php

if ( 'custom-icon' === $icon_type && ! empty( $custom_icon ) ) {
	if( ! empty( $retina_scaling ) && 'yes' === $retina_scaling ) {
		$img = wp_get_attachment_image_src( $custom_icon, 'full' ); ?>
		<img itemprop="image" src="<?php echo esc_url( $img[0] ); ?>"
		     width="<?php echo round( $img[1] / 2 ); ?>"
		     height="<?php echo round( $img[2] / 2 ); ?>"
		     alt="<?php echo esc_attr( $img[3] ); ?>"/>
	<?php } else {
		echo wp_get_attachment_image( $custom_icon, 'full' );
	}
}
