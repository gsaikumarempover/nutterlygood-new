<header id="qodef-page-mobile-header" role="banner">
	<?php
	// Hook to include additional content before page mobile header inner
	do_action( 'greenpath_core_action_before_page_mobile_header_inner' );
	?>
	<div id="qodef-page-mobile-header-inner" <?php qode_framework_class_attribute( apply_filters( 'greenpath_filter_mobile_header_inner_class', array(), 'mobile' ) ); ?>>
		<?php
		// Include mobile logo
		greenpath_core_get_mobile_header_logo_image();

		// Include mobile widget area one
		if ( is_active_sidebar( 'qodef-mobile-header-widget-area-one' ) ) {
			?>
			<div class="qodef-widget-holder qodef--one">
				<?php dynamic_sidebar( 'qodef-mobile-header-widget-area-one' ); ?>
			</div>
			<?php
		}

		// Include mobile navigation opener
		greenpath_core_template_part( 'mobile-header', 'templates/parts/mobile-navigation-opener' );
		?>
	</div>
	<?php
	// Include mobile navigation
	greenpath_core_template_part( 'mobile-header', 'templates/parts/mobile-navigation' );

	// Hook to include additional content after page mobile header inner
	do_action( 'greenpath_core_action_after_page_mobile_header_inner' );
	?>
</header>
