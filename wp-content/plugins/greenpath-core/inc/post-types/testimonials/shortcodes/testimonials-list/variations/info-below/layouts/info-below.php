<div <?php qode_framework_class_attribute( $item_classes ); ?>>
	<div class="qodef-e-inner">
		<div class="qodef-e-content" data-swiper-parallax-opacity="0">
			<div class="qodef-e-parallax" data-swiper-parallax-x="-120">
				<?php greenpath_core_list_sc_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'post-info/image', '', $params ); ?>
				<?php greenpath_core_list_sc_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'post-info/rating', '', $params ); ?>
			</div>
			<?php greenpath_core_list_sc_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'post-info/title', '', $params ); ?>
			<?php greenpath_core_list_sc_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'post-info/text', '', $params ); ?>
			<?php greenpath_core_list_sc_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'post-info/author', '', $params ); ?>
		</div>
	</div>
</div>
