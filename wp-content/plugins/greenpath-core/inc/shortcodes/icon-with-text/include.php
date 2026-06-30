<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/icon-with-text/class-greenpathcore-icon-with-text-shortcode.php';

foreach ( glob( GREENPATH_CORE_SHORTCODES_PATH . '/icon-with-text/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
