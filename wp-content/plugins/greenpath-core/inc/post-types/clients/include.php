<?php

include_once GREENPATH_CORE_CPT_PATH . '/clients/helper.php';

foreach ( glob( GREENPATH_CORE_CPT_PATH . '/clients/dashboard/meta-box/*.php' ) as $module ) {
	include_once $module;
}
