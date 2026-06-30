<article <?php post_class( $item_classes ); ?>>
	<div class="qodef-e-inner">
		<?php
		// Include post media
		greenpath_core_template_part( 'blog/shortcodes/blog-list', 'templates/post-info/image', '', $params );
		?>
		<div class="qodef-e-content">
			<div class="qodef-e-text">
				<?php
				// Include post title
				greenpath_core_template_part( 'blog/shortcodes/blog-list', 'templates/post-info/title', '', $params );
				?>
			</div>
			<div class="qodef-e-bottom-holder">
				<div class="qodef-e-info">
					<?php
					// Include post category info
					greenpath_core_theme_template_part( 'blog', 'templates/parts/post-info/read-more' );
					?>
				</div>
			</div>
		</div>
	</div>
</article>
