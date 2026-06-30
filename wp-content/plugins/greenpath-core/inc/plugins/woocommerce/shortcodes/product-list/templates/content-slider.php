<div <?php qode_framework_class_attribute( $slider_holder_classes ); ?>>
	<?php
	if ( 'info-left' === $slider_type ) {
		// Include info
		greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/info', '', $params );
	}
	?>
	<div <?php qode_framework_class_attribute( $holder_classes ); ?> <?php qode_framework_inline_style( $holder_styles ); ?> <?php qode_framework_inline_attr( $slider_attr, 'data-options' ); ?>>
		<ul class="swiper-wrapper">
			<?php
			// Include items
			greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/loop', '', $params );
			?>
		</ul>
		<?php greenpath_core_template_part( 'content', 'templates/swiper-pag', '', $params ); ?>
	</div>
	<?php
	if ( 'info-left' !== $slider_type ) {
		greenpath_core_template_part( 'content', 'templates/swiper-nav', '', $params );
	}
	?>
</div>

