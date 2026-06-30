<?php
$price          = $product->get_price_html();
$weight         = $product->get_weight();
$price_per_unit = get_post_meta( get_the_ID(), 'qodef_show_price_per_unit', true );
$weight_html    = '';
$price_html     = '<div class="qodef-woo-product-price qodef-pli-price price">' . $price . '</div>';
if ( 'yes' === $price_per_unit && ! empty( $weight ) ) {
    $weight_num   = floatval( $weight );
    $price_num    = floatval( $product->get_price() );
    $weight_html .= '<span class="qodef-price-per-unit">(';
    $weight_html .= $weight_num > 1 ? $price_num / $weight_num : $price_num / $weight_num;
    $weight_html .= '/' . get_option( 'woocommerce_weight_unit' ) . ')</span>';
    $price_html  .= $weight_html;
}
if ( ! empty( $price ) ) {
    ?>
    <div class="qodef-e-price-holder"><?php echo wp_kses_post( $price_html ); ?></div>
    <?php
}