<div <?php qode_framework_class_attribute( $holder_classes ); ?>>
	<div class="qodef-e-gallery-switcher">
		<?php greenpath_core_template_part( 'shortcodes/swapping-image-gallery', 'templates/parts/info', '', $params ); ?>
		<div class="qodef-e-switcher-items">
			<?php
			$i = 0;

			foreach ( $items as $item ) {
				?>
				<div class="qodef-m-item qodef-e" data-index="<?php echo esc_attr( $i ++ ); ?>">
					<?php
					if ( isset( $item['item_icon'] ) && ! empty( $item['item_icon'] ) ) { ?>
						<div class="qodef-item-icon">
							<?php
							echo wp_get_attachment_image( $item['item_icon'], 'full' );
							if ( isset( $item['item_hover_icon'] ) && ! empty( $item['item_hover_icon'] ) ) {
								echo wp_get_attachment_image( $item['item_hover_icon'], 'full', '', array( 'class' => 'qodef-item-hover-icon' ) );
							}
							?>
						</div>
					<?php } ?>
					<div class="qodef-title-wrap">
						<span class="qodef-e-title" <?php qode_framework_inline_style( $title_styles ); ?>><?php echo esc_html( $item['item_title'] ); ?></span>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
	<div class="qodef-e-gallery">
		<?php
		$i = 0;

		foreach ( $items as $item ) {
			if ( isset( $item['item_image'] ) && ! empty( $item['item_image'] ) ) { ?>
				<div class="qodef-item-image" data-index="<?php echo esc_attr( $i ++ ); ?>" <?php qode_framework_inline_style( $item_styles ); ?>>
					<?php
					echo wp_get_attachment_image( $item['item_image'], 'full' );
					?>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>
