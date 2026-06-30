<?php

include_once GREENPATH_CORE_SHORTCODES_PATH . '/pricing-table/class-greenpathcore-pricing-table-shortcode.php';

foreach ( glob( GREENPATH_CORE_SHORTCODES_PATH . '/pricing-table/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
