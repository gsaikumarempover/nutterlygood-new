<div <?php qode_framework_class_attribute( $holder_classes ); ?>>
	<?php greenpath_core_template_part( 'shortcodes/banner', 'templates/parts/image', '', $params ); ?>
	<div class="qodef-m-content">
		<div class="qodef-m-content-inner" <?php qode_framework_inline_style( $content_styles ); ?>>
			<?php greenpath_core_template_part( 'shortcodes/banner', 'templates/parts/stamp', '', $params ); ?>
			<?php greenpath_core_template_part( 'shortcodes/banner', 'templates/parts/title', '', $params ); ?>
			<?php greenpath_core_template_part( 'shortcodes/banner', 'templates/parts/subtitle', '', $params ); ?>
			<?php greenpath_core_template_part( 'shortcodes/banner', 'templates/parts/text', '', $params ); ?>
			<?php greenpath_core_template_part( 'shortcodes/banner', 'templates/parts/button', '', $params ); ?>
		</div>
	</div>
</div>
