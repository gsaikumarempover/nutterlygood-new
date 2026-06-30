<?php
$has_media = ! empty( get_post_meta( get_the_ID(), 'qodef_post_format_gallery_images', true ) ) || ! empty( get_post_meta( get_the_ID(), 'qodef_post_format_video_url', true ) ) || ! empty( get_post_meta( get_the_ID(), 'qodef_post_format_audio_url', true ) ) || has_post_thumbnail();
$classes   = 'qodef-blog-item qodef-e';

if ( ! $has_media ) {
	$classes .= ' qodef--no-media';
}
?>
<article <?php post_class( $classes ); ?>>
	<div class="qodef-e-inner">
		<?php
		// Include post media
		greenpath_template_part( 'blog', 'templates/parts/post-info/media' );
		?>
		<div class="qodef-e-content">
			<div class="qodef-e-top-holder">
				<div class="qodef-e-info">
					<?php
					// Include post author info
					greenpath_template_part( 'blog', 'templates/parts/post-info/author' );

					// Include post category info
					greenpath_template_part( 'blog', 'templates/parts/post-info/categories' );

					// Include post comments info
					greenpath_template_part( 'blog', 'templates/parts/post-info/comments' );
					?>
				</div>
			</div>
			<div class="qodef-e-text">
				<?php
				// Include post title
				greenpath_template_part( 'blog', 'templates/parts/post-info/title' );

				// Include post content
				the_content();

				// Hook to include additional content after blog single content
				do_action( 'greenpath_action_after_blog_single_content' );
				?>
			</div>
			<div class="qodef-e-bottom-holder">
				<div class="qodef-e-left">
					<?php
					// Include post category info
					greenpath_template_part( 'blog', 'templates/parts/post-info/tags' );
					?>
				</div>
				<div class="qodef-e-right">
					<?php
					// Include post tags info
					greenpath_template_part( 'blog', 'templates/parts/post-info/social-share' );
					?>
				</div>
			</div>
		</div>
	</div>
</article>
