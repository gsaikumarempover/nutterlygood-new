<?php
$rating = $product->get_average_rating();

if ( ! empty( $rating ) ) {
	echo greenpath_core_woo_product_get_rating_html( '', $rating, 0 );
}