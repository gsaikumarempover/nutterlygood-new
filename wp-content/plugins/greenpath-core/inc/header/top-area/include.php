<?php

include_once GREENPATH_CORE_INC_PATH . '/header/top-area/class-greenpathcore-top-area.php';
include_once GREENPATH_CORE_INC_PATH . '/header/top-area/helper.php';

foreach ( glob( GREENPATH_CORE_INC_PATH . '/header/top-area/dashboard/*/*.php' ) as $dashboard ) {
	include_once $dashboard;
}
