<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/info-section/class-greenpathcore-info-section-shortcode.php';

foreach ( glob( GREENPATH_CORE_SHORTCODES_PATH . '/info-section/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
