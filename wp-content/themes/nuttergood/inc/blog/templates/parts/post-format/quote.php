<?php
$quote_meta = get_post_meta( get_the_ID(), 'qodef_post_format_quote_text', true );
$quote_text = ! empty( $quote_meta ) ? $quote_meta : get_the_title();

if ( ! empty( $quote_text ) ) {
	$quote_author     = get_post_meta( get_the_ID(), 'qodef_post_format_quote_author', true );
	$title_tag        = isset( $title_tag ) && ! empty( $title_tag ) ? $title_tag : 'h5';
	$author_title_tag = isset( $author_title_tag ) && ! empty( $author_title_tag ) ? $author_title_tag : 'h6';
	?>
	<div class="qodef-e-quote">
		<?php greenpath_render_svg_icon( 'quote', 'qodef-e-quote-icon' ); ?>
		<div class="qodef-quote-info">
			<<?php echo greenpath_escape_title_tag( $title_tag ); ?> class="qodef-e-quote-text"><?php echo esc_html( $quote_text ); ?></<?php echo greenpath_escape_title_tag( $title_tag ); ?>>
			<?php if ( ! empty( $quote_author ) ) { ?>
				<<?php echo greenpath_escape_title_tag( $author_title_tag ); ?> class="qodef-e-quote-author"><?php echo esc_html( $quote_author ); ?></<?php echo greenpath_escape_title_tag( $author_title_tag ); ?>>
			<?php } ?>
		</div>
		<?php if ( ! is_single() ) { ?>
			<a itemprop="url" class="qodef-e-quote-url" href="<?php the_permalink(); ?>"></a>
		<?php } ?>
	</div>
<?php } ?>
