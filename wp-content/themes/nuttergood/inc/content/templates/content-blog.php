<?php
// Hook to include additional content before page content holder
do_action( 'greenpath_action_before_page_content_holder' );
?>
<main id="qodef-page-content" class="qodef-grid qodef-layout--template <?php echo esc_attr( greenpath_get_page_grid_sidebar_classes() ); ?> <?php echo esc_attr( greenpath_get_grid_gutter_classes() ); ?>" <?php echo greenpath_get_grid_gutter_styles(); ?> role="main">
	<div class="qodef-grid-inner">
		<?php
		// Hook to include additional content before blog and sidebar
		do_action( 'greenpath_action_before_blog_sidebar' );

		// Include blog template
		echo apply_filters( 'greenpath_filter_blog_template', greenpath_get_template_part( 'blog', 'templates/blog' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Include page content sidebar
		greenpath_template_part( 'sidebar', 'templates/sidebar' );
		?>
	</div>
</main>
<?php
// Hook to include additional content after main page content holder
do_action( 'greenpath_action_after_page_content_holder' );
?>
