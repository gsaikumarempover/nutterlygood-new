<?php if ( is_active_sidebar( 'qodef-side-area' ) ) { ?>
	<div id="qodef-side-area" <?php qode_framework_class_attribute( $classes ); ?>>
		<div class="qodef-side-area-top">
			<div class="qodef-side-area-logo">
				<?php
				// Add logo image
				greenpath_core_get_header_logo_image( array( 'only_main' => true ) );

				// Add close icon
				greenpath_core_get_opener_icon_html(
					array(
						'option_name'  => 'side_area',
						'custom_id'    => 'qodef-side-area-close',
						'custom_class' => 'qodef--opened',
					),
					false,
					true
				);
				?>
			</div>
		</div>
		<div id="qodef-side-area-inner">
			<?php dynamic_sidebar( 'qodef-side-area' ); ?>
		</div>
	</div>
<?php } ?>
