<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/custom-font/class-greenpathcore-custom-font-shortcode.php';

foreach ( glob( GREENPATH_CORE_SHORTCODES_PATH . '/custom-font/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
