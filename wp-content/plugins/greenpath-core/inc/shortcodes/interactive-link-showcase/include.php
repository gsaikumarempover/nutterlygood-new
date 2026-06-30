<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/interactive-link-showcase/class-greenpathcore-interactive-link-showcase-shortcode.php';

foreach ( glob( GREENPATH_CORE_SHORTCODES_PATH . '/interactive-link-showcase/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
