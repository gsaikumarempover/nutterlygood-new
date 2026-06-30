<?php

include_once GREENPATH_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/product-category-list/media-custom-fields.php';
include_once GREENPATH_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/product-category-list/class-greenpathcore-product-category-list-shortcode.php';

foreach ( glob( GREENPATH_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/product-category-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
