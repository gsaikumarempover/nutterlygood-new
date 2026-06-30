<?php
$has_media = ! empty( get_post_meta( get_the_ID(), 'qodef_post_format_gallery_images', true ) ) || ! empty( get_post_meta( get_the_ID(), 'qodef_post_format_video_url', true ) ) || ! empty( get_post_meta( get_the_ID(), 'qodef_post_format_audio_url', true ) ) || has_post_thumbnail();

if ( ! $has_media ) {
	$item_classes .= ' qodef--no-media';
}
?>
<article <?php post_class( $item_classes ); ?>>
	<div class="qodef-e-inner">
		<?php
		// Include post media
		greenpath_core_template_part( 'blog/shortcodes/blog-list', 'templates/post-info/media', '', $params );
		?>
		<div class="qodef-e-content">
			<div class="qodef-e-top-holder">
				<div class="qodef-e-info">
					<?php
					// Include post author info
					greenpath_core_theme_template_part( 'blog', 'templates/parts/post-info/author' );

					// Include post category info
					greenpath_core_theme_template_part( 'blog', 'templates/parts/post-info/categories' );

					// Include post comments info
					greenpath_core_theme_template_part( 'blog', 'templates/parts/post-info/comments' );
					?>
				</div>
			</div>
			<div class="qodef-e-text">
				<?php
				// Include post title
				greenpath_core_template_part( 'blog/shortcodes/blog-list', 'templates/post-info/title', '', $params );

				// Include post excerpt
				greenpath_core_theme_template_part( 'blog', 'templates/parts/post-info/excerpt', '', $params );

				// Hook to include additional content after blog single content
				do_action( 'greenpath_action_after_blog_single_content' );
				?>
			</div>
			<div class="qodef-e-bottom-holder">
				<div class="qodef-e-left">
					<?php
					// Include post read more
					greenpath_core_theme_template_part( 'blog', 'templates/parts/post-info/read-more', '', array_merge( $params, array( 'button_layout' => 'filled' ) ) );
					?>
				</div>
			</div>
		</div>
	</div>
</article>
