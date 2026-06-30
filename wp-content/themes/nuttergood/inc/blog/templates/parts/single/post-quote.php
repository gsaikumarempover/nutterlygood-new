<article <?php post_class( 'qodef-blog-item qodef-e' ); ?>>
	<div class="qodef-e-inner">
		<?php
		// Include post format part
		greenpath_template_part( 'blog', 'templates/parts/post-format/quote' );
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
