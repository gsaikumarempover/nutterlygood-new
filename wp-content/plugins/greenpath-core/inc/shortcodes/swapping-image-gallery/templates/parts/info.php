<?php if ( ! empty( $title ) ) { ?>
	<<?php echo greenpath_core_escape_title_tag( $title_tag ); ?> class="qodef-m-title" <?php qode_framework_inline_style( $title_styles ); ?>>
	<?php echo esc_html( $title ); ?>
	</<?php echo greenpath_core_escape_title_tag( $title_tag ); ?>>
<?php } ?>
<?php if ( ! empty( $text ) ) { ?>
	<p class="qodef-m-text" <?php qode_framework_inline_style( $text_styles ); ?>>
	<?php echo esc_html( $text ); ?>
	</p>
<?php } ?>