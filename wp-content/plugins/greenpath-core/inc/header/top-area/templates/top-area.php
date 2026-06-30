<?php if ( $show_header_area ) { ?>
	<div id="qodef-top-area" <?php qode_framework_class_attribute( apply_filters( 'greenpath_core_filter_top_area_class', array() ) ); ?>>
		<div id="qodef-top-area-inner" <?php qode_framework_class_attribute( apply_filters( 'greenpath_core_filter_top_area_inner_class', array() ) ); ?>>
			<?php
			// Include widget area top right
			greenpath_core_get_header_widget_area( 'left', 'top-area', 'top_area', true );

			// Include widget area top center
			greenpath_core_get_header_widget_area( 'center', 'top-area', 'top_area', true );

			// Include widget area top right
			greenpath_core_get_header_widget_area( 'right', 'top-area', 'top_area', true );

			do_action( 'greenpath_core_action_after_top_area' );
			?>
		</div>
	</div>
<?php } ?>
