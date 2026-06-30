<?php
$title_tag = isset( $title_tag ) && ! empty( $title_tag ) ? $title_tag : 'h1';

$title_classes   = array();
$title_classes[] = 'qodef-e-title';
$title_classes[] = 'entry-title';
$title_classes[] = ! empty( $title_tag ) ? '' : 'qodef--default-title';
$title_classes   = implode( ' ', $title_classes );
?>
<?php echo '<' . greenpath_escape_title_tag( $title_tag ); ?> itemprop="name" class="<?php echo esc_attr( $title_classes ); ?>">
	<?php if ( ! is_single() ) : ?>
		<a itemprop="url" class="qodef-e-title-link" href="<?php the_permalink(); ?>">
	<?php endif; ?>
	<?php the_title(); ?>
	<?php if ( ! is_single() ) : ?>
		</a>
	<?php endif; ?>
<?php echo '</' . greenpath_escape_title_tag( $title_tag ); ?>>
