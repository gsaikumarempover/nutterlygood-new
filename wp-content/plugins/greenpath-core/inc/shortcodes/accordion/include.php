<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/accordion/class-greenpathcore-accordion-shortcode.php';
include_once GREENPATH_CORE_SHORTCODES_PATH . '/accordion/class-greenpathcore-accordion-child-shortcode.php';

foreach ( glob( GREENPATH_CORE_SHORTCODES_PATH . '/accordion/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
