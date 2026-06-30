<?php

include_once GREENPATH_CORE_CPT_PATH . '/team/shortcodes/team-list/class-greenpathcore-team-list-shortcode.php';

foreach ( glob( GREENPATH_CORE_CPT_PATH . '/team/shortcodes/team-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
