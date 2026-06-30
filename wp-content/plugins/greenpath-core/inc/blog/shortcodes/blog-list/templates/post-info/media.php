<div class="qodef-e-media">
	<?php
	// Include post date info
	greenpath_core_theme_template_part( 'blog', 'templates/parts/post-info/date' );

	switch ( get_post_format() ) {
		case 'gallery':
			greenpath_core_theme_template_part( 'blog', 'templates/parts/post-format/gallery', '', $params );
			break;
		case 'video':
			greenpath_core_theme_template_part( 'blog', 'templates/parts/post-format/video', '', $params );
			break;
		case 'audio':
			greenpath_core_theme_template_part( 'blog', 'templates/parts/post-format/audio', '', $params );
			break;
		default:
			greenpath_core_template_part( 'blog/shortcodes/blog-list', 'templates/post-info/image', '', $params );
			break;
	}
	?>
</div>
