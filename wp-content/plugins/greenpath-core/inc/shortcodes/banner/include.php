<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/banner/class-greenpathcore-banner-shortcode.php';

foreach ( glob( GREENPATH_CORE_INC_PATH . '/shortcodes/banner/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
