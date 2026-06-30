<div <?php qode_framework_class_attribute( $holder_classes ); ?>>
	<a class="qodef-social-share-dropdown-opener" href="javascript:void(0)">
		<span class="qodef-social-title qodef-custom-label"><?php echo ! empty( $title ) ? esc_html( $title ) : esc_html__( 'Share', 'greenpath-core' ); ?></span>
		<?php greenpath_render_svg_icon( 'social-share', 'qodef-e-social-share-icon' ); ?>
	</a>
	<div class="qodef-social-share-dropdown">
		<ul class="qodef-shortcode-list">
			<?php
			foreach ( $social_networks as $net ) {
				echo qode_framework_wp_kses_html( 'html', $net );
			}
			?>
		</ul>
	</div>
</div>
