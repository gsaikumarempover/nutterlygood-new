<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/button/class-greenpathcore-button-shortcode.php';

foreach ( glob( GREENPATH_CORE_SHORTCODES_PATH . '/button/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
