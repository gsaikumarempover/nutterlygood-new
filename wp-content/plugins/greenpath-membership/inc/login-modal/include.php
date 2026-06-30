<?php

include_once GREENPATH_MEMBERSHIP_LOGIN_MODAL_PATH . '/helper.php';

foreach ( glob( GREENPATH_MEMBERSHIP_LOGIN_MODAL_PATH . '/*/include.php' ) as $module ) {
	include_once $module;
}
