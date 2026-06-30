<?php if ( ! empty( $video_source ) ) : ?>
	<div class="qodef-m-video">
		<video autoplay="autoplay" loop="loop" muted="muted" playsinline="">
			<source src="<?php echo esc_url( $video_source ); ?>">
		</video>
	</div>
<?php endif; ?>
