<div <?php qode_framework_class_attribute( $holder_classes ); ?>>
	<?php if ( ! empty( $title ) ) { ?>
		<span class="qodef-social-title qodef-custom-label"><?php echo esc_html( $title ); ?></span>
	<?php } ?>
	<ul class="qodef-shortcode-list">
		<?php
		foreach ( $social_networks as $net ) {
			echo qode_framework_wp_kses_html( 'html', $net );
		}
		?>
	</ul>
</div>
