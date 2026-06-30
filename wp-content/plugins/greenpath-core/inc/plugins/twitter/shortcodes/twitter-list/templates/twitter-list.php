<div <?php qode_framework_class_attribute( $holder_classes ); ?> <?php qode_framework_inline_style( $holder_styles ); ?>>
	<?php echo do_shortcode( "[custom-twitter-feeds $twitter_params]" ); // XSS OK ?>
</div>
