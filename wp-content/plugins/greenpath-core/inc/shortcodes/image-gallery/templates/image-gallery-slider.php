<div <?php qode_framework_class_attribute( $holder_classes ); ?> <?php qode_framework_inline_style( $holder_styles ); ?> <?php qode_framework_inline_attr( $slider_attr, 'data-options' ); ?>>
	<div class="swiper-wrapper">
		<?php
		// Include items
		if ( ! empty( $images ) ) {
			foreach ( $images as $image ) {
				greenpath_core_template_part( 'shortcodes/image-gallery', 'templates/parts/image', '', array_merge( $params, $image ) );
			}
		}
		?>
	</div>
	<?php if ( ! ( 'outside' === $slider_navigation_position ) ) { ?>
		<?php greenpath_core_template_part( 'content', 'templates/swiper-nav', '', $params ); ?>
	<?php } ?>
	<?php greenpath_core_template_part( 'content', 'templates/swiper-pag', '', $params ); ?>
</div>
<?php if( 'outside' === $slider_navigation_position ) { ?>
	<?php greenpath_core_template_part( 'content', 'templates/swiper-nav', '', $params ); ?>
<?php } ?>
