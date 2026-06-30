<?php if ( ! empty( $stamp ) ) { ?>
	<div class="qodef-m-stamp" <?php qode_framework_inline_style( $stamp_styles ); ?>>
		<?php echo wp_get_attachment_image( $stamp, 'full' ); ?>
	</div>
<?php } ?>