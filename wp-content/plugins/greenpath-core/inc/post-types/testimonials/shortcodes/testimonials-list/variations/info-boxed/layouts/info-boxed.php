<?php
$image_on_right = 'yes' === get_post_meta( get_the_ID(), 'qodef_testimonial_image_right', true ) ? true : false;
$move_x = $image_on_right ? 10 : -20;
?>
<div <?php qode_framework_class_attribute( $item_classes ); ?>>
	<div class="qodef-e-inner <?php echo esc_attr( $image_on_right ) ? esc_attr( 'qodef-right-position' ) : ''; ?>">
		<?php if ( ! $image_on_right ) { ?>
			<div data-swiper-parallax-y="-60" data-swiper-scale="0.5">
				<?php greenpath_core_list_sc_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'post-info/image', '', $params ); ?>
			</div>
		<?php } ?>
		<div class="qodef-e-content">
			<?php greenpath_core_list_sc_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'post-info/additional-image', '', $params ); ?>
			<?php
			if( ! empty( $enable_quote ) && 'yes' === $enable_quote ) {
				greenpath_core_list_sc_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'post-info/quote', '', $params );
			}
			?>
			<?php greenpath_core_list_sc_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'post-info/rating', '', $params ); ?>
			<?php greenpath_core_list_sc_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'post-info/title', '', $params ); ?>
			<?php greenpath_core_list_sc_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'post-info/text', '', $params ); ?>
			<?php greenpath_core_list_sc_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'post-info/author', '', $params ); ?>
		</div>
		<?php if ( $image_on_right ) { ?>
			<div data-swiper-parallax-y="-60" data-swiper-scale="0.5">
				<?php greenpath_core_list_sc_template_part( 'post-types/testimonials/shortcodes/testimonials-list', 'post-info/image', '', $params ); ?>
			</div>
		<?php } ?>
	</div>
</div>
