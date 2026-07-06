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

					if ( ! greenpath_is_installed( 'core' ) ) {
						// Include post tags
						greenpath_template_part( 'blog', 'templates/parts/post-info/tags' );
					}

					// Include post comments info
					greenpath_template_part( 'blog', 'templates/parts/post-info/comments' );
					?>
				</div>
			</div>
			<div class="qodef-e-text">
				<?php
				if ( function_exists( 'nuttergood_farmley_render_blog_card_meta' ) ) {
					nuttergood_farmley_render_blog_card_meta();
				}

				// Include post title
				greenpath_template_part( 'blog', 'templates/parts/post-info/title', '', array( 'title_tag' => 'h2' ) );

				// Include post excerpt
				greenpath_template_part( 'blog', 'templates/parts/post-info/excerpt' );

				// Hook to include additional content after blog single content
				do_action( 'greenpath_action_after_blog_single_content' );
				?>
			</div>
			<div class="qodef-e-bottom-holder">
				<div class="qodef-e-left">
					<?php
					// Include post read more
					greenpath_template_part( 'blog', 'templates/parts/post-info/read-more', '', array_merge( $params, array( 'button_layout' => 'filled' ) ) );
					?>
				</div>
			</div>
		</div>
	</div>
</article>
