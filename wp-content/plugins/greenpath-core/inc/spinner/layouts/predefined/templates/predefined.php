<?php
$spinner_image       = greenpath_core_get_post_value_through_levels( 'qodef_spinner_background_image' );
$spinner_svg            = greenpath_core_get_post_value_through_levels( 'qodef_spinner_svg' );
$image_src              = wp_get_attachment_image_src( $spinner_image, 'full' );
?>
<span class="qodef-m-spinner-bg-image">
	<span class="qodef-m-spinner-bg-image-inner">
	<img itemprop="image" src="<?php echo esc_url( $image_src[0] ); ?>"
     width="<?php echo round( $image_src[1]); ?>" height="<?php echo round( $image_src[2]); ?>"
     alt="<?php echo esc_attr( $image_src[3] ); ?>"/>
	</span>
	<span class="qodef-m-spinner-bg-image-inner qodef--copy">
		<img itemprop="image" src="<?php echo esc_url( $image_src[0] ); ?>"
		     width="<?php echo round( $image_src[1] ); ?>" height="<?php echo round( $image_src[2] ); ?>"
		     alt="<?php echo esc_attr( $image_src[3] ); ?>"/>
	</span>
</span>
<span class="qodef-m-spinner-svg-holder">
	<span class='qodef--predefined-icon'>
		<?php greenpath_render_svg_icon( 'logo-lemon' ); ?>
	</span>
	<span class="qodef-m-spinner-svg"><?php echo qode_framework_wp_kses_html( 'svg custom', $spinner_svg ) ?></span>
</span>
