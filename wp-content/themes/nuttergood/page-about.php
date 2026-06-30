<?php
/**
 * Template Name: Farmley About
 * About Us — brand story + product categories (no Elementor body).
 */

get_header();
?>
<main id="qodef-page-content" class="qodef-grid qodef-layout--template qodef-grid-template--12 qodef--no-bottom-space ng-farmley-about-page">
	<div class="qodef-grid-inner clear">
		<div class="qodef-grid-item qodef-page-content-section qodef-col--content">
			<div class="qodef-page-content-inner">
				<?php
				while ( have_posts() ) {
					the_post();
					if ( function_exists( 'nuttergood_farmley_render_about_page' ) ) {
						nuttergood_farmley_render_about_page();
					}
				}
				?>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();