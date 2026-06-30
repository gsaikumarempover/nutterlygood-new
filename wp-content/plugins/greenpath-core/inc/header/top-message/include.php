<?php
include_once GREENPATH_CORE_INC_PATH . '/header/top-message/helper.php';

foreach ( glob( GREENPATH_CORE_INC_PATH . '/header/top-message/dashboard/*/*.php' ) as $dashboard ) {
	include_once $dashboard;
}
