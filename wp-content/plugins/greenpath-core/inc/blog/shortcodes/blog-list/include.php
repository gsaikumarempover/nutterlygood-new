<?php

include_once GREENPATH_CORE_INC_PATH . '/blog/shortcodes/blog-list/class-greenpathcore-blog-list-shortcode.php';

foreach ( glob( GREENPATH_CORE_INC_PATH . '/blog/shortcodes/blog-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
