<?php
$rating = 5;
$current_rating = get_post_meta( get_the_ID(), 'qodef_testimonials_rating', true );
?>

<div class="qodef-rating-holder">
	<?php for ($i = 1; $i <= 5; $i++) {
	    if ($i <= $current_rating) { ?>
	        <span class="qodef-e-current-rating">
	    <?php } ?>
			<?php echo greenpath_core_get_svg_icon( 'star' ); ?>
	    <?php if ($i <= $current_rating) { ?>
	        </span>
	    <?php } ?>
	<?php } ?>
</div>
