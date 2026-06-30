<div <?php qode_framework_class_attribute( $holder_classes ); ?> <?php qode_framework_inline_style( $holder_styles ); ?>>
	<div class="swiper-container">
		<div class="qodef-m-items swiper-wrapper">
			<?php foreach ( $items as $item ) { ?>
				<a itemprop="url" class="qodef-m-item swiper-slide" href="<?php echo esc_url( $item['item_link'] ); ?>" target="<?php echo esc_attr( $link_target ); ?>">
					<span class="qodef-e-title"><?php echo esc_html( $item['item_title'] ); ?></span>
				</a>
			<?php } ?>
		</div>
	</div>
	<div class="qodef-m-images">
		<?php foreach ( $items as $item ) { ?>
			<div class="qodef-m-image" <?php qode_framework_inline_style( $this_shortcode->get_image_styles( $item ) ); ?>>
				<?php echo wp_get_attachment_image( $item['item_image'], 'full' ); ?>
			</div>
		<?php } ?>
	</div>
</div>
