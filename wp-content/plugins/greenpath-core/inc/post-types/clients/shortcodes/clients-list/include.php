<?php

include_once GREENPATH_CORE_CPT_PATH . '/clients/shortcodes/clients-list/class-greenpathcore-clients-list-shortcode.php';

foreach ( glob( GREENPATH_CORE_CPT_PATH . '/clients/shortcodes/clients-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
