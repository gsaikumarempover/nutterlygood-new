<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/single-image/class-greenpathcore-single-image-shortcode.php';

foreach ( glob( GREENPATH_CORE_SHORTCODES_PATH . '/single-image/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
