<?php

$product_image_size = 'shop_catalog';;

if (!$product->is_in_stock()) { ?>
    <span
        class="qodef-pl-out-of-stock"><?php esc_html_e('Out of Stock', 'cyberstore'); ?></span>
<?php }

if (has_post_thumbnail($product_id)) {
	echo get_the_post_thumbnail( $product_id, 'full', array( 'class' => 'qodef-e-image' ) );
}

?>