<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/image-marquee/class-greenpathcore-image-marquee-shortcode.php';

foreach ( glob( GREENPATH_CORE_INC_PATH . '/shortcodes/image-marquee/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
