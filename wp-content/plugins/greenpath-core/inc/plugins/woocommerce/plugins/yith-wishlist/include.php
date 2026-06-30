<?php

include_once GREENPATH_CORE_PLUGINS_PATH . '/woocommerce/plugins/yith-wishlist/dashboard/admin/woocommerce-yith-wishlist-options.php';
include_once GREENPATH_CORE_PLUGINS_PATH . '/woocommerce/plugins/yith-wishlist/helper.php';
include_once GREENPATH_CORE_PLUGINS_PATH . '/woocommerce/plugins/yith-wishlist/template-functions.php';
include_once GREENPATH_CORE_PLUGINS_PATH . '/woocommerce/plugins/yith-wishlist/class-greenpathcore-woocommerce-yith-wishlist.php';

foreach ( glob( GREENPATH_CORE_PLUGINS_PATH . '/woocommerce/plugins/yith-wishlist/widgets/*/include.php' ) as $widget ) {
	include_once $widget;
}
