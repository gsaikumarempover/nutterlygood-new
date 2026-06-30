<div class="qodef-e-media">
	<?php
	// Include post date info
	greenpath_template_part( 'blog', 'templates/parts/post-info/date' );

	switch ( get_post_format() ) {
		case 'gallery':
			greenpath_template_part( 'blog', 'templates/parts/post-format/gallery' );
			break;
		case 'video':
			greenpath_template_part( 'blog', 'templates/parts/post-format/video' );
			break;
		case 'audio':
			greenpath_template_part( 'blog', 'templates/parts/post-format/audio' );
			break;
		default:
			greenpath_template_part( 'blog', 'templates/parts/post-info/image' );
			break;
	}
	?>
</div>
