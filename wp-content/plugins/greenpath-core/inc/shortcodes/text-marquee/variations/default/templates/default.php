<div <?php qode_framework_class_attribute( $holder_classes ); ?> <?php qode_framework_inline_style( $holder_styles ); ?>>
	<div class="qodef-m-content" <?php qode_framework_inline_style( $text_styles ); ?>>
		<div class="qodef-m-content-inner">
			<?php if ( ! empty( $text_1 ) ) : ?>
				<span class="qodef-m-text-1" <?php qode_framework_inline_style( $text_individual_styles['first'] ); ?>>
					<?php echo esc_html( $text_1 ); ?>
				</span>
			<?php endif; ?>
			<?php if ( ! empty( $separator ) ): ?>
				<span class="qodef-m-separator" <?php qode_framework_inline_style( $text_individual_styles['separator'] ); ?>>
					<?php echo esc_html( $separator ); ?>
				</span>
			<?php endif; ?>
			<?php if ( ! empty( $text_2 ) ) : ?>
				<span class="qodef-m-text-2" <?php qode_framework_inline_style( $text_individual_styles['second'] ); ?>>
					<?php echo esc_html( $text_2 ); ?>
				</span>
			<?php endif; ?>
			<?php if ( ! empty( $separator ) ): ?>
				<span class="qodef-m-separator" <?php qode_framework_inline_style( $text_individual_styles['separator'] ); ?>>
					<?php echo esc_html( $separator ); ?>
				</span>
			<?php endif; ?>
			<?php if ( ! empty( $text_3 ) ) : ?>
				<span class="qodef-m-text-3" <?php qode_framework_inline_style( $text_individual_styles['third'] ); ?>>
					<?php echo esc_html( $text_3 ); ?>
				</span>
			<?php endif; ?>
			<?php if ( ! empty( $separator ) ): ?>
				<span class="qodef-m-separator" <?php qode_framework_inline_style( $text_individual_styles['separator'] ); ?>>
					<?php echo esc_html( $separator ); ?>
				</span>
			<?php endif; ?>
		</div>
	</div>
</div>
