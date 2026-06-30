<div id="qodef-page-comments">
	<?php if ( have_comments() ) {
		$comments_number = get_comments_number();
		?>
		<div id="qodef-page-comments-list" class="qodef-m">
			<?php // translators: %s - number of comments value ?>
			<h2 class="qodef-m-title"><?php echo esc_html( sprintf( _n( '%s Comment', '%s Comments', intval( $comments_number ), 'nuttergood' ), intval( $comments_number ) ) ); ?></h2>
			<ul class="qodef-m-comments">
				<?php wp_list_comments( array_unique( array_merge( array( 'callback' => 'greenpath_get_comments_list_template' ), apply_filters( 'greenpath_filter_comments_list_template_callback', array() ) ) ) ); ?>
			</ul>

			<?php if ( get_comment_pages_count() > 1 ) { ?>
				<div class="qodef-m-pagination qodef--wp">
					<?php
					the_comments_pagination(
						array(
							'prev_text'          => greenpath_get_svg_icon( 'pagination-arrow-left' ),
							'next_text'          => greenpath_get_svg_icon( 'pagination-arrow-right' ),
							'before_page_number' => '0',
						)
					);
					?>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) { ?>
		<p class="qodef-page-comments-not-found"><?php esc_html_e( 'Comments are closed.', 'nuttergood' ); ?></p>
	<?php } ?>
	<div id="qodef-page-comments-form">
		<?php comment_form( greenpath_get_comment_form_args() ); ?>
	</div>
</div>
