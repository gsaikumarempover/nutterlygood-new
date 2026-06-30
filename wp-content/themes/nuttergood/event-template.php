<?php
/*
Template Name: Timetable Event
*/
get_header();

// Include event content template
if ( greenpath_is_installed( 'core' ) ) {
	greenpath_core_template_part( 'plugins/timetable', 'templates/content' );
}

get_footer();
