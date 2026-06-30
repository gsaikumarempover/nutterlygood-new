<?php if ( ! post_password_required() ) { ?>
	<div class="qodef-e-read-more">
		<?php
		$button_layout = ! empty( $params['button_layout'] ) ? 'filled' : 'textual';
		if ( greenpath_post_has_read_more() ) {
			$button_params = array(
				'link'          => get_permalink() . '#more-' . get_the_ID(),
				'button_layout' => $button_layout,
				'text'          => esc_html__( 'Read More', 'nuttergood' ),
			);
		} else {
			$button_params = array(
				'link'          => get_the_permalink(),
				'button_layout' => $button_layout,
				'text'          => esc_html__( 'Read More', 'nuttergood' ),
			);
		}

		greenpath_render_button_element( $button_params );
		?>
	</div>
<?php } ?>
