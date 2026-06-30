<div <?php qode_framework_class_attribute( $holder_classes ); ?> <?php qode_framework_inline_style( $holder_styles ); ?>>
	<div class="qodef-grid-inner">
		<?php
		// Include global masonry template from theme
		greenpath_core_theme_template_part( 'masonry', 'templates/sizer-gutter', '', $params['behavior'] );

		// Include items
		greenpath_core_template_part( 'post-types/clients/shortcodes/clients-list', 'templates/loop', '', $params );
		?>
	</div>
</div>
