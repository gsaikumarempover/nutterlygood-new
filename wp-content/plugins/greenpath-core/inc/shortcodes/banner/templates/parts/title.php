<?php if ( ! empty( $title ) ) : ?>
	<?php echo '<' . greenpath_core_escape_title_tag( $title_tag ); ?> class="qodef-m-title" <?php qode_framework_inline_style( $title_styles ); ?>>
		<?php echo qode_framework_wp_kses_html( 'content', $title ); ?>
	<?php echo '</' . greenpath_core_escape_title_tag( $title_tag ); ?>>
<?php endif; ?>
