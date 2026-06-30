<?php

include_once GREENPATH_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/product-list/helper.php';
include_once GREENPATH_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/product-list/class-greenpathcore-product-list-shortcode.php';

foreach ( glob( GREENPATH_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/product-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
