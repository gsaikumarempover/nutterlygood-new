<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/tabs/class-greenpathcore-tab-shortcode.php';
include_once GREENPATH_CORE_SHORTCODES_PATH . '/tabs/class-greenpathcore-tab-child-shortcode.php';

foreach ( glob( GREENPATH_CORE_SHORTCODES_PATH . '/tabs/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
