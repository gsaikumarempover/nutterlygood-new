<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/text-marquee/class-greenpathcore-text-marquee-shortcode.php';

foreach ( glob( GREENPATH_CORE_INC_PATH . '/shortcodes/text-marquee/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
