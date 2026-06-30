<div id="qodef-page-content-bottom" <?php qode_framework_class_attribute( implode( ' ', apply_filters( 'greenpath_core_filter_content_bottom_holder_classes', array( 'qodef-m' ) ) ) ); ?> role="contentinfo">
	<?php
	// Hook to include additional content before page content bottom module content
	do_action( 'greenpath_core_action_before_page_content_bottom_content' );

	// Include module content template
	echo apply_filters( 'greenpath_core_filter_content_bottom_content_template', greenpath_core_get_template_part( 'content-bottom', 'templates/content-bottom-content' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	// Hook to include additional content after page content bottom module content
	do_action( 'greenpath_core_action_after_page_content_bottom_content' );
	?>
</div>
