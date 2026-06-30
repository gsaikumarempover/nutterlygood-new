<div class="qodef-reviews-list-info qodef-reviews-simple">
	<div class="qodef-reviews-number-wrapper">
		<<?php echo greenpath_core_escape_title_tag( $title_tag ); ?> class="qodef-reviews-summary">
			<?php if ( isset( $rating_number ) && ! empty( $rating_number ) ) { ?>
				<span class="qodef-reviews-number">
					<?php echo esc_html( $rating_number ); ?>
				</span>
			<?php } ?>
			<span class="qodef-reviews-label">
				<?php echo esc_html( $rating_label ); ?>
			</span>
		</<?php echo greenpath_core_escape_title_tag( $title_tag ); ?>>
		<span class="qodef-stars-wrapper">
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
	</div>
</div>
