<?php

include_once GREENPATH_CORE_INC_PATH . '/social-share/shortcodes/social-share/class-greenpathcore-social-share-shortcode.php';

foreach ( glob( GREENPATH_CORE_INC_PATH . '/social-share/shortcodes/social-share/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
