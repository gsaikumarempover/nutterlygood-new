<?php

include_once GREENPATH_CORE_INC_PATH . '/search/class-greenpathcore-search.php';
include_once GREENPATH_CORE_INC_PATH . '/search/helper.php';
include_once GREENPATH_CORE_INC_PATH . '/search/dashboard/admin/search-options.php';

foreach ( glob( GREENPATH_CORE_INC_PATH . '/search/layouts/*/include.php' ) as $layout ) {
	include_once $layout;
}
