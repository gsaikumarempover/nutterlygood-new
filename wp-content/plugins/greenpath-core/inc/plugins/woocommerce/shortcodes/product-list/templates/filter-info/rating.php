<div class="qodef-e-options-wrapper">
	<div class="qodef-e-options-inner">
		<?php
		for ( $i = 5; $i > 0; $i-- ) {

			$id = isset( $mobile_id_prefix ) ? $mobile_id_prefix . $i : $i;
			?>
			<div class="qodef-e-checkbox">
				<input type="checkbox" id="<?php echo esc_attr( $id . '-star-rating' ); ?>" name="qodef-product-rating" value="<?php echo esc_attr( $i ); ?>">
				<label for="<?php echo esc_attr( $id . '-star-rating' ); ?>">
					<?php
					$j   = $i;
					$k   = 5 - $j;
					$max = 5;
					$min = 0;
					if ( 5 === $j ) {
						$max = $j + .1;
						$min = $j - 0.5;
					} elseif ( 1 === $j ) {
						$max = $j + 0.5;
						$min = $j;
					} else {
						$max = $j + 0.5;
						$min = $j - 0.5;
					}
					$products = get_posts(
						array(
							'post_type'      => 'product',
							'posts_per_page' => -1,
							'meta_query'     =>
								array(
									array(
										'key'     => '_wc_average_rating',
										'value'   => array( $min, $max ),
										'compare' => 'BETWEEN',
									),
								),
						)
					);

					$rating_number = 0;

					if ( ! empty( $products ) ) {
						$rating_number = count( $products );
					}

					for ( $j; $j > 0; $j-- ) {
						greenpath_core_render_svg_icon( 'star', 'qodef-star-full' );
					}
					for ( $k; $k > 0; $k-- ) {
						greenpath_core_render_svg_icon( 'star', 'qodef-star-empty' );
					}
					?>
					<span class="qodef-e-number"><?php echo esc_html( '(' . $rating_number . ')' ); ?></span>
				</label>
			</div>
		<?php } ?>
	</div>
</div>
