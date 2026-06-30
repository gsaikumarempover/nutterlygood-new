<div <?php qode_framework_class_attribute( $holder_classes ); ?>>
    <div class="qodef-product-cart-showcase-left">
        <?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-cart-showcase', 'templates/parts/products', '', $params ); ?>
    </div>
    <div class="qodef-product-cart-showcase-right">
        <div class="qodef-product-cart-showcase-right-inner">
            <div class="qodef-product-cart-showcase-grid-inner">
                <?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-cart-showcase', 'templates/parts/order-details', '', $params ); ?>
                <?php greenpath_core_template_part( 'plugins/woocommerce/shortcodes/product-cart-showcase', 'templates/parts/button' ); ?>
            </div>
        </div>
    </div>
</div>