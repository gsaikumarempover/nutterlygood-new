<div class="qodef-header-standard-extended <?php echo implode( ' ', apply_filters( 'greenpath_core_filter_standard_extended_header_class', array() ) ); ?>">
	<div class="qodef-header-section qodef--top">
		<div class="qodef-header-section--top-inner <?php echo implode( ' ', apply_filters( 'greenpath_core_filter_standard_extended_header_top_inner_class', array() ) ); ?>">
		    <?php
		    // Include logo
		    greenpath_core_get_header_logo_image();

		    // Include widget area one
			greenpath_core_get_header_widget_area();
			?>
		</div>
	</div>
	<div class="qodef-header-section qodef--bottom <?php echo implode( ' ', apply_filters( 'greenpath_core_filter_standard_extended_header_bottom_class', array() ) ); ?>">
		<div class="qodef-header-section--bottom-inner <?php echo implode( ' ', apply_filters( 'greenpath_core_filter_standard_extended_header_bottom_inner_class', array() ) ); ?>">
		    <?php
		    // Include main navigation
		    greenpath_core_get_extended_dropdown_menu();

		    // Include main navigation
		    greenpath_core_template_part( 'header', 'templates/parts/navigation' );

		    // Include widget area one
		    greenpath_core_get_header_widget_area( 'two' );
		    ?>
		</div>
	</div>
</div>
