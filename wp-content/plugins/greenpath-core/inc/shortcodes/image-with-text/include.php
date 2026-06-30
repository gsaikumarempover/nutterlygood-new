<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/image-with-text/class-greenpathcore-image-with-text-shortcode.php';

foreach ( glob( GREENPATH_CORE_SHORTCODES_PATH . '/image-with-text/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
