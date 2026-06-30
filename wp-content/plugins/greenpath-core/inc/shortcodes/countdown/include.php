<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/countdown/class-greenpathcore-countdown-shortcode.php';

foreach ( glob( GREENPATH_CORE_SHORTCODES_PATH . '/countdown/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
