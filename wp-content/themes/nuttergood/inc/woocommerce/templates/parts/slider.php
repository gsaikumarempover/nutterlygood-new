<?php
$product = greenpath_woo_get_global_product();
$image   = $product->get_image_id();
$thumbs  = $product->get_gallery_image_ids();

$classes[] = 'qodef-swiper-container';
//$classes[] = 'qodef-swiper--show-navigation qodef-swiper--show-pagination';
$classes   = implode( ' ', $classes );
?>
<?php if ( $image ) : ?>
<div class="<?php echo esc_attr( $classes ); ?>">
	<div class="swiper-wrapper">
		<?php echo wp_get_attachment_image( $image, 'full', false, array( 'class' => 'swiper-slide' ) ); ?>
		<?php endif; ?>
		<?php if ( $image && $thumbs ) : ?>
		<?php foreach ( $thumbs as $thumb ) : ?>
			<?php echo wp_get_attachment_image( $thumb, 'full', false, array( 'class' => 'swiper-slide' ) ); ?>
		<?php endforeach; ?>
	</div>
	<?php /*
	<div class="swiper-button-prev">
		<?php greenpath_render_svg_icon( 'slider-arrow-left' ); ?>
	</div>
	<div class="swiper-button-next">
		<?php greenpath_render_svg_icon( 'slider-arrow-right' ); ?>
	</div>
    */ ?>
</div>
<?php endif; ?>
