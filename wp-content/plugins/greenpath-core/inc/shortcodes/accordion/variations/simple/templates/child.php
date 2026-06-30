<div class="qodef-accordion-item">
	<<?php echo greenpath_core_escape_title_tag( $title_tag ); ?> class="qodef-accordion-title">
		<span class="qodef-tab-title"><?php echo esc_html( $title ); ?></span>
		<span class="qodef-accordion-mark">
			<?php echo greenpath_core_get_svg_icon( 'angle-down' ); ?>
		</span>
	</<?php echo greenpath_core_escape_title_tag( $title_tag ); ?>>
	<div class="qodef-accordion-content">
		<div class="qodef-accordion-content-inner">
			<?php echo do_shortcode( $content ); ?>
		</div>
	</div>
</div>
