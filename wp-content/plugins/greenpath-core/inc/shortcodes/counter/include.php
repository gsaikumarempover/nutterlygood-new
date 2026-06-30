<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/counter/class-greenpathcore-counter-shortcode.php';

foreach ( glob( GREENPATH_CORE_SHORTCODES_PATH . '/counter/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
