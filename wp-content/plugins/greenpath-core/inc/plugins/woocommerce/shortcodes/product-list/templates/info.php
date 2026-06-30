<div class="qodef-info-left">
	<?php if ( ! empty( $slider_title ) ) { ?>
		<h2 class="qodef-e-slider-title"><?php echo esc_html( $slider_title ); ?></h2>
	<?php } ?>
	<?php if ( ! empty( $slider_text ) ) { ?>
		<p class="qodef-e-slider-text"><?php echo esc_html( $slider_text ); ?></p>
	<?php } ?>
	<div class="qodef-navigation">
		<?php greenpath_core_template_part( 'content', 'templates/swiper-nav', '', $params ); ?>
	</div>
</div>
