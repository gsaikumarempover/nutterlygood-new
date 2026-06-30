<?php
require __DIR__ . '/wp-load.php';
foreach ( wc_get_attribute_taxonomies() as $attr ) {
	echo $attr->attribute_name . "\n";
}