<div <?php qode_framework_class_attribute( $holder_classes ); ?> <?php qode_framework_inline_style( $holder_styles ); ?>>
	<div class="qodef-m-items">
		<?php
		$i = 0;

		foreach ( $items as $item ) {
			?>
			<a itemprop="url" class="qodef-m-item qodef-e" href="<?php echo esc_url( $item['item_link'] ); ?>" target="<?php echo esc_attr( $link_target ); ?>" data-index="<?php echo esc_attr( $i ++ ); ?>">
				<span class="qodef-e-title"><?php echo esc_html( $item['item_title'] ); ?></span>
				<?php
				if ( isset( $item['item_image'] ) && ! empty( $item['item_image'] ) ) {
					$images = explode( ',', trim( $item['item_image'] ) );
					$urls   = array();
					foreach ( $images as $image ) {
						$urls[] = wp_get_attachment_image_url( $image, 'full' );
					}
					?>
					<span class="qodef-e-follow-content">
						<span class="qodef-e-follow-image" data-images="<?php echo implode( '|', $urls ); ?>" data-images-count="<?php echo count( $urls ); ?>">
							<?php echo wp_get_attachment_image( $images[0], 'full' ); ?>
							<span class="qodef-e-follow-title"><?php echo esc_html( $item['item_title'] ); ?></span>
						</span>
					</span>
				<?php } ?>
			</a>
		<?php } ?>
	</div>
</div>
