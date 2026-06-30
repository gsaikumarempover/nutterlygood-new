<span class="qodef-stars">
	<?php foreach ( $post_ratings as $rating ) { ?>
		<span class="qodef-stars-wrapper-inner">
			<span class="qodef-stars-items">
				<?php
				$review_rating = greenpath_core_post_average_rating( $rating );
				for ( $i = 1; $i <= $review_rating; $i ++ ) {
					echo greenpath_core_get_svg_icon( 'star' );
				} ?>
			</span>
			<?php if ( isset( $rating['label'] ) && ! empty( $rating['label'] ) ) { ?>
				<span class="qodef-stars-label">
					<?php echo esc_html( $rating['label'] ); ?>
				</span>
			<?php } ?>
		</span>
	<?php } ?>
</span>
<a itemprop="url" class="qodef-post-info-comments" href="<?php comments_link(); ?>">
	<span class="qodef-reviews-number">
		<?php echo esc_html( $rating_number ); ?>
	</span>
	<span class="qodef-reviews-label">
		<?php echo esc_html( $rating_label ); ?>
	</span>
</a>
