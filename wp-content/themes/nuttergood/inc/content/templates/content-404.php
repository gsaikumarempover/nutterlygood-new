<main id="qodef-page-content" role="main">
	<?php
	// Include 404 template
	echo apply_filters( 'greenpath_filter_404_page_template', greenpath_get_template_part( '404', 'templates/404' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
</main>
