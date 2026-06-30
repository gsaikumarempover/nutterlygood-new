<?php
$products_ids_array = array();

if ( is_single() && is_product() ) {
	$product        = greenpath_core_woo_get_global_product();
	$cross_sell_ids = $product->get_cross_sell_ids();

	foreach ( $cross_sell_ids as $cross_sell_id ) {
		$products_ids_array[] = $cross_sell_id;
	}

	$products_ids_imploded = implode(',', $products_ids_array);

} else if( ! empty( $params['product_ids'] ) ) {
	$products_ids_imploded = $params['product_ids'];
}

$params = array(
	'layout'            => 'info-below',
	'behavior'          => 'slider',
	'slider_pagination' => 'no',
	'slider_navigation' => 'no',
	'additional_params' => 'id',
	'columns'           => 3,
	'columns_1512'      => 3,
	'columns_1368'      => 3,
	'columns_1200'      => 2,
	'columns_1024'      => 2,
	'columns_880'       => 1,
	'columns_680'       => 1,
	'space'             => 'small',
	'post_ids'          => $products_ids_imploded,
	'title_tag'         => 'h6',
);

echo GreenPathCore_Product_List_Shortcode::call_shortcode( $params );
