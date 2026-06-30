<?php

foreach ( glob( GREENPATH_MEMBERSHIP_INC_PATH . '/general/dashboard/admin/*.php' ) as $module ) {
	include_once $module;
}

require_once GREENPATH_MEMBERSHIP_INC_PATH . '/general/class-greenpathmembership-page-templates.php';
include_once GREENPATH_MEMBERSHIP_INC_PATH . '/general/helper.php';
