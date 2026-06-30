<div class="qodef-reviews-list-info qodef-reviews-per-mark">
	<?php foreach ( $post_ratings as $rating ) { ?>
		<?php
		$average_rating     = greenpath_core_post_average_rating( $rating );
		$rating_count       = $rating['count'];
		$rating_count_label = 1 === $rating_count ? esc_html__( 'Rating', 'greenpath-core' ) : esc_html__( 'Ratings', 'greenpath-core' );
		$rating_marks       = $rating['marks'];
		?>
		<div class="qodef-reviews-number-holder">
			<div class="qodef-reviews-number-wrapper">
				<span class="qodef-reviews-number"><?php echo esc_html( $average_rating ); ?></span>
				<span class="qodef-stars-wrapper">
						<span class="qodef-review-rating">
							<?php echo greenpath_core_reviews_get_rating_html( '', $average_rating, 0 ); ?>
						</span>
						<span class="qodef-reviews-count">
							<?php echo esc_html__( 'Rated', 'greenpath-core' ) . ' ' . $average_rating . ' ' . esc_html__( 'out of', 'greenpath-core' ) . ' ' . $rating_count . ' ' . $rating_count_label; ?>
						</span>
						</span>
			</div>
			<div class="qodef-rating-percentage-wrapper">
				<?php
				foreach ( $rating_marks as $item => $value ) {
					$percentage = 0 === $rating_count ? 0 : round( ( $value / $rating_count ) * 100 );
					$pb_params  = array(
						'layout'              => 'line',
						'title'               => esc_attr( $item ) . esc_attr__( ' stars', 'greenpath-core' ),
						'number'              => esc_attr( $percentage ),
						'active_line_color'   => '#FDD835',
						'inactive_line_color' => '#F5F4F4',
						'active_line_width'   => 6,
						'inactive_line_width' => 6,
						'title_tag'           => 'h6',
					);

					echo GreenPathCore_Progress_Bar_Shortcode::call_shortcode( $pb_params );
				}
				?>
			</div>
		</div>
	<?php } ?>
</div>
