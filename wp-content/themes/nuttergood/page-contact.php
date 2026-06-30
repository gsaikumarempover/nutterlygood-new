<?php
/**
 * Template Name: Farmley Contact
 * Contact page — map + themed form (no Elementor body).
 */

get_header();
?>
<main id="qodef-page-content" class="qodef-grid qodef-layout--template qodef-grid-template--12 qodef--no-bottom-space ng-farmley-contact-page">
	<div class="qodef-grid-inner clear">
		<div class="qodef-grid-item qodef-page-content-section qodef-col--content">
			<div class="qodef-page-content-inner">
				<?php
				while ( have_posts() ) {
					the_post();
					do_action( 'nuttergood_farmley_before_contact_content' );
					if ( function_exists( 'nuttergood_farmley_render_contact_page' ) ) {
						nuttergood_farmley_render_contact_page();
					}
				}
				?>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();