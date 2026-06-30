<?php
require __DIR__ . '/wp-load.php';
$shop_id = (int) get_option( 'woocommerce_shop_page_id' );
$data = get_post_meta( $shop_id, '_elementor_data', true );
echo $data ? $data : '(empty)';