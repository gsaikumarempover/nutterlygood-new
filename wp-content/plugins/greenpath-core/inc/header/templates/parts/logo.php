<a itemprop="url" class="qodef-header-logo-link <?php echo esc_attr( $logo_classes ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
	<?php
	// Hooks that allows you to add additional content before logo image
	do_action( 'greenpath_core_before_header_logo_image' );

	// Include header logo image html
	echo qode_framework_wp_kses_html( 'html', $logo_image );

	// Hooks that allows you to add additional content after logo image
	do_action( 'greenpath_core_after_header_logo_image' );
	?>
</a>
