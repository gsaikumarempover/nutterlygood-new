<?php

include_once GREENPATH_CORE_CPT_PATH . '/testimonials/shortcodes/testimonials-list/class-greenpathcore-testimonials-list-shortcode.php';

foreach ( glob( GREENPATH_CORE_CPT_PATH . '/testimonials/shortcodes/testimonials-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
